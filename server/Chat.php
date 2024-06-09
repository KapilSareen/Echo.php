<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SQLite3;

 

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $db;
    protected $chatId;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->initializeDb();
        }

    public function initializeDb() {
        try {
            $this->db = new SQLite3('./app/data.db');
            echo"Successfully connected to DB";
            
            } catch (error) {
                echo "Error connecting to DB...";
                exit();
            }
                 
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

                $decodedRoom = base64_decode($room);
                $users = explode("~", $decodedRoom);

                //      table chats
                // chatId INTEGER PRIMARY KEY,
                // user1Id INTEGER NOT NULL,
                // user2Id INTEGER NOT NULL,
                // FOREIGN KEY (user1Id) REFERENCES users(userId),
                // FOREIGN KEY (user2Id) REFERENCES users(userId),
                // CONSTRAINT unique_users UNIQUE (user1Id, user2Id)

        $query = "SELECT * FROM chats WHERE user1Id='$users[0]' and user2Id='$users[1]';";
        $result = $this->db->query($query);
        $row = $result->fetchArray();
        
        $sql = "INSERT INTO CHATS (user1Id, user2Id) VALUES ('$users[0]', '$users[1]')";
        if(!$row){
        if ($this->db->exec($sql) ){
        $this->chatId = $this->db->lastInsertRowID();
        echo "Chat inserted with chatId $this->chatId !";
        
        }
}
        else{
        $this->chatId=$row['chatId'];
        echo "Chat already there with chatid $this->chatId";
        
}             
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

                    $sentBy=$data['sentBy'];
                    $content=$data['message'];
                    $time=$data['time'];
//                                TABLE messages 
//     messageId INTEGER PRIMARY KEY,
//     content TEXT NOT NULL,
//     timestamp DATETIME NOT NULL
//   , sentBy TEXT NOT NULL);

//                                 TABLE chatMessages 
//     chatId INTEGER,
//     messageData TEXT NOT NULL,
//     FOREIGN KEY (chatId) REFERENCES chats(chatId) ON DELETE CASCADE,
//     PRIMARY KEY (chatId)
                $sql = "INSERT INTO MESSAGES (content, timestamp, sentBy) VALUES ('$content', '$time', '$sentBy')";
              
                if ($this->db->exec($sql) ){
                echo "Message inserted!";
                echo "This is chatId $this->chatId";
                $lastmessageId = $this->db->lastInsertRowID();
                echo "The ID of the inserted message is: $lastmessageId";

                $query = "SELECT * FROM chatMessages WHERE chatId='$this->chatId';";
                $result = $this->db->query($query);
                $row = $result->fetchArray();

                if(!$row){

                    $sql = "INSERT INTO chatMessages (chatId, messageData) VALUES ('$this->chatId','$lastmessageId')";
                    if ($this->db->exec($sql) )
                {
                echo "Message added in chatMessages with message id $lastmessageId and chatId $this->chatId";
                
                }else{
                    echo"Message not added in chatMessages";
                }
            }
            else{
                $existingMessageData = $row['messageData'];
                $newMessageData = $existingMessageData . ',' . $lastmessageId;
                $sql = "UPDATE chatMessages SET messageData = '$newMessageData' WHERE chatId = '$this->chatId'";
                if ($this->db->exec($sql)) {
                echo "ChatMessage entry already there updated chatmessage Id";
               }
               
             }          
        } 
            
    }}
    
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