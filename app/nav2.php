<?php
if (isset($_SESSION["username"])) {
$username= $_SESSION['username'];
$password=$_SESSION['password'];
try {
  $db = new SQLite3('data.db');
  if ($db === false) {
      throw new Exception('Failed to connect to the database.');
  }
  $query = "SELECT * FROM USERS WHERE username='$username' and password='$password';";
  $result = $db->query($query);

} catch (error) {
 echo "Error";
 exit();
}
$row = $result->fetchArray();
$profile=$row['profile'];
$username= $row['username'];
}


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>
  .media .avatar {
    flex-shrink: 0;
}
.media {
    padding: 16px 12px;
    -webkit-transition: background-color .2s linear;
    transition: background-color .2s linear;
}
.avatar {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 36px;
    line-height: 36px;
    text-align: center;
    border-radius: 100%;
    background-color: #f5f6f7;
    color: #8b95a5;
    text-transform: uppercase;
}
.click{
  cursor: pointer;
}

</style>
  </head>
<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">Welcome <?php echo '@'.$username; ?></a>
          <div class="flex">
            <form method="post" action="index.php">
              <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            </form>
            <?php 
            if (isset($_SESSION['username'])) {
            echo "
            <div class='media media-chat'><img class='avatar click' src='uploads/$profile' alt='...'></div>";}?>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </div>
          </button>
          <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Chats</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
            <form class="d-flex mt-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-success" type="submit">Search</button>
              </form>
               <br>
              <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
               
         


                <li class="nav-item">
                <a class="postLink nav-link" href="#" data-recipient="lucifer">Lucifer</a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown
                  </a>
                  <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                  </ul>
                </li>
              </ul>
              </div>
          </div>
        </div>
      </nav>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.querySelector('.postLink').addEventListener('click', function(event) {
            event.preventDefault(); 
            const key1 = this.getAttribute('data-recipient');

            const form = document.createElement('form');
            form.method = 'post';
            form.action = '/chat.php';
            form.style.display = 'none';
            const recipientField = document.createElement('input');
            recipientField.type = 'hidden';
            recipientField.name = 'recipient';
            recipientField.value = key1;
            form.appendChild(recipientField);
            document.body.appendChild(form);
            form.submit();
        });
       let pfp=document.querySelector(".click")
      pfp.addEventListener("click",()=>{
        window.location="/dashboard.php"
      })
    </script>
  </body>
</html>