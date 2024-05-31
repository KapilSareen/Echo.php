<?php
session_start();
include("nav2.php") ;
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
                <h2 class="card-title"><strong>Chat</strong></h2>
                <a class="btn btn-xs btn-secondary" href="#" data-abc="true">Let's Chat App</a>
              </div>


              <div class="ps-container ps-theme-default ps-active-y" id="chat-content" style="overflow-y: auto !important; height:680px;">
                
              <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; height: 0px; right: 2px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 2px;"></div></div></div>

              <div class="publisher bt-1 border-light">
                <img class="avatar avatar-xs" src=<?php echo "uploads/$profile" ;?> alt="...">
                <input class="publisher-input" type="text" placeholder="Write something">
                <a class="publisher-btn text-info" href="#" data-abc="true" onclick="submitForm()"><i class="fa fa-paper-plane"></i></a>
              </div>

             </div>
<script>
var conn = new WebSocket('ws://localhost:8001');
conn.onopen = function(e) {
console.log("Connection established!");
};

conn.onmessage = function(e) {
messagRecieved=e.data;
console.log(messagRecieved)
chatbox=document.querySelector(".ps-container")
chatbox.innerHTML+=`
                  <div class="media media-chat">
                  <img class="avatar" src="https://img.icons8.com/color/36/000000/administrator-male.png" alt="...">
                  <div class="media-body">
                    <p>${messagRecieved}</p>
                    <p class="meta"><time datetime="2018">00:12</time></p>
                  </div>
                </div>`

};


function submitForm() {
  const message = document.querySelector(".publisher-input").value;

  console.log(message)
  if (message) {
     conn.send(message)

 chatbox=document.querySelector(".ps-container")
chatbox.innerHTML+=`<div class="media media-chat media-chat-reverse">
                  <div class="media-body">
                    <p>${message}</p>
                    <p class="meta"><time datetime="2018">00:12</time></p>
                  </div>
                </div>`
     document.querySelector(".publisher-input").value='';
  }
}
const messageInput = document.getElementById('.publisher-input');
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