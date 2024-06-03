<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SQLite3;

try {
$db = new SQLite3('./app/data.db');
echo"Successfully connected to DB";

} catch (error) {
    echo "Error connecting to DB...";
    exit();
}
      

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        
        // Check if a room parameter was provided in the connection query string
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParameters);
        $room = isset($queryParameters['room']) ? $queryParameters['room'] : null;
        $username = isset($queryParameters['username']) ? $queryParameters['username'] : null;
    
        // Log the connection attempt
        echo "New connection! ({$conn->resourceId}) Room: {$room}, Username: {$username}\n";
    
        // Check if the username is already in use
        foreach ($this->clients as $client) {
            if ($client !== $conn && isset($client->username) && $client->username === $username && $client->room== $room) {
                $conn->send(json_encode(['type' => 'reject', 'message' => "Username '$username' is already in use."]));
                $conn->close();
                echo "Connection rejected! Username '$username' is already in use.\n";
                return;
            }
            else{

                $sql = "INSERT INTO chats (USERNAME, PASSWORD, EMAIL, BIO) VALUES ('$username', '$password', '$email', '$bio')";
                $db->exec($sql)
            }
        }

        $conn->room = $room; 
        $conn->username = $username; 
    }
     
    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
    
        $data = json_decode($msg, true);
        if (isset($data['room'])) {
            $room = $data['room'];
            $username = $data['sentBy'];
            foreach ($this->clients as $client) {
                // Check if the client belongs to the same room
                if ($from !== $client && isset($client->room) && $client->room === $room ) {
                    $client->send($msg);
                }
            }
        }
    }
    
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($conn->room); 
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}