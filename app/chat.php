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

try {
  $db = new SQLite3('data.db');
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
        <h2 class="card-title"><strong>Chat with <?php echo"$recipient"?></strong></h2>
        <!-- <a class="btn btn-xs btn-secondary" href="#" data-abc="true">Let's Chat App</a> -->
    </div>
    <div class="ps-container ps-theme-default ps-active-y" id="chat-content" style="overflow-y: auto !important; height:680px;">
        <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;">
            <div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps-scrollbar-y-rail" style="top: 0px; height: 0px; right: 2px;">
            <div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 2px;"></div>
        </div>
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

function submitForm() {
    const message = document.querySelector(".publisher-input").value;
    var date = new Date();
    var currentTime = `${(date.getHours() < 10 ? '0' : '') + date.getHours()}:${(date.getMinutes() < 10 ? '0' : '') + date.getMinutes()}`;
     
    

    // const room = "A"; // Assuming room is defined on the server-side and injected into the script
    
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
        scrollToBottom(); // Ensure the chat scrolls to the bottom when a new message is sent
    }
}

const messageInput = document.querySelector('.publisher-input');
document.body.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        submitForm();
        event.preventDefault(); // Prevent default form submission behavior
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
