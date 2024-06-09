<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: dashboard.php");
  exit(); 
}

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
}
if (isset($_POST["recipient"])) {
    $_SESSION["recipient"]=$_POST["recipient"];
}
$recipient= $_SESSION["recipient"];



$username= $_SESSION["username"];
try {
    $db = new SQLite3('data.db');
 
    // $db->exec('PRAGMA foreign_keys = ON;');                         -> To-do: find out why this isn't working out (this is should enable 'ON DELETE CASCADE')
    $query = "SELECT * FROM USERS WHERE username='$recipient';";
    $result = $db->query($query);
    $row = $result->fetchArray();
    if ($row) {
        $recipientPfp= $row["profile"];
    } else {
  header("Location: dashboard.php?userexist=false");
  
  }
  } catch (error) {
      exit();
  }

  if (isset($_POST["Delete"])) {

    try {
        
        $query = "SELECT messageData from chatMessages where chatId=(SELECT chatId FROM chats WHERE (user2Id='$recipient' and user1Id='$username') or (user1Id='$recipient' and user2Id='$username'))";
        $result = $db->query($query);
        $row = $result->fetchArray();
        if ($row) {
            $messageIds=explode(",",$row["messageData"]);
            for ($i=0; $i < count($messageIds); $i++) { 
                $q2="DELETE FROM messages where messageId=$messageIds[$i]";
                $db->query($q2);
            }
        }
        $q2 = "DELETE from chatMessages where chatId=(SELECT chatId FROM chats WHERE (user2Id='$recipient' and user1Id='$username') or (user1Id='$recipient' and user2Id='$username'))";
        $db->exec($q2);
        $q1 = "DELETE  FROM chats WHERE (user2Id='$recipient' and user1Id='$username') or (user1Id='$recipient' and user2Id='$username');";
        $db->exec($q1);
    }catch (error) {}
}

include("nav2.php");
echo "<br><br><br>";
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
</head>
<link rel="stylesheet" href="css/chat.css">

<div class="card card-bordered">
    <div class="card-header">
        <h4 class="card-title"><strong>Chat with <?php echo"$recipient"?></strong></h4>
         <form id="deleteChatForm" action="chat.php" method="post">
            <button style="font-size:15px" class="btn btn-xs btn-secondary dark" type="submit" name="Delete" value="1" >Delete Chat</button>
         </form>
    </div>
    <div class="ps-container ps-theme-default ps-active-y" id="chat-content" style="overflow-y: auto !important; height:680px;">
        <?php
        try {
            $query = "SELECT messageData from chatMessages where chatId=(SELECT chatId FROM chats WHERE (user2Id='$recipient' and user1Id='$username') or (user1Id='$recipient' and user2Id='$username'))";
            $result = $db->query($query);
            $row = $result->fetchArray();
            if ($row) {
            $messageIds=explode(",",$row["messageData"]);
            for ($i=0; $i < count($messageIds); $i++) { 
                  $query="SELECT * from messages where messageId=$messageIds[$i]";
                  $result = $db->query($query);
                  $row = $result->fetchArray();
                //   echo"    ".$row['timestamp'].$row['content'].$row['sentBy']."<br>";
        
                  if($row['sentBy']==$username){
                    echo "<div class='media media-chat media-chat-reverse'>
                    <div class='media-body'>
                        <p>" . $row['content'] . "</p>
                        <p class='meta'><time datetime='2024'>" . $row['timestamp'] . "</time></p>
                    </div>
                </div>";
                  }
                  else{
                 echo '<div class="media media-chat">
                 <img class="avatar" src="uploads/'.$recipientPfp.'" alt="...">
                 <div class="media-body">
                     <p>'.$row['content'].'</p>
                     <p class="meta"><time datetime="2024">'.$row['timestamp'].'</time></p>
                 </div>
             </div>';
                  }
                
            }
        } 
            } catch (error) {
            echo "Error";
            }
        ?>

    </div>
    <div class="publisher bt-1 border-light">
        <img class="avatar avatar-xs" src=<?php echo "uploads/$profile"; ?> alt="...">
        <input class="publisher-input" type="text" placeholder="Write something">
        <a class="publisher-btn text-info" href="#" data-abc="true" onclick="submitForm()"><i class="fa fa-paper-plane"></i></a>
    </div>
  </div>
  <script>

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const room = urlParams.get('room')
console.log(room);

roomId=btoa("<?php echo $_SESSION['username']. "~". $recipient ?>".split("~").sort().join('~'))
username="<?php echo "$username"; ?>";
console.log(roomId, username)
const encodedRoomId = encodeURIComponent(roomId);
var conn = new WebSocket(`ws://localhost:8001?room=${encodedRoomId}&username=${username}`);
conn.onopen = function(e) {
  console.log("Connected!");
};

conn.onmessage = function(e) {

    var messageData = JSON.parse(e.data);
    if(messageData.type=="reject"){
      console.log('rejected')
      alert("You are already in this chat")
      window.location='dashboard.php'
      return;
    }
    console.log(messageData);
    
    var chatbox = document.querySelector(".ps-container");
    chatbox.innerHTML += `
        <div class="media media-chat">
            <img class="avatar" src="uploads/<?php echo basename($recipientPfp) ?>" alt="...">
            <div class="media-body">
                <p>${messageData.message}</p>
                <p class="meta"><time datetime="2024">${messageData.time}</time></p>
            </div>
        </div>`;
    scrollToBottom(); 
};

function deletechat(){
    document.getElementById("deleteChatForm").submit();
}

function submitForm() {
    const message = document.querySelector(".publisher-input").value;
    var date = new Date();
    var currentTime = `${(date.getHours() < 10 ? '0' : '') + date.getHours()}:${(date.getMinutes() < 10 ? '0' : '') + date.getMinutes()}`;
     
    
    if (message) {
       var messageData = JSON.stringify({ room: roomId, message: message, time: currentTime, sentBy: "<?php echo $_SESSION['username']; ?>", recipient: "<?php echo $recipient; ?>" });
        conn.send(messageData);
        var chatbox = document.querySelector(".ps-container");
        chatbox.innerHTML += `
            <div class="media media-chat media-chat-reverse">
                <div class="media-body">
                    <p>${message}</p>
                    <p class="meta"><time datetime="2024">${currentTime}</time></p>
                </div>
            </div>`;
        document.querySelector(".publisher-input").value = '';
        scrollToBottom(); 
    }
}

const messageInput = document.querySelector('.publisher-input');
document.body.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        submitForm();
        event.preventDefault(); 
    }
});

const chatContent = document.getElementById('chat-content');

function scrollToBottom() {
    chatContent.scrollTop = chatContent.scrollHeight;
}
scrollToBottom();
const observer = new MutationObserver(() => {
    scrollToBottom();
});

observer.observe(chatContent, { childList: true });
</script>
