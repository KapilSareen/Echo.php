<?php 
session_start();

if(isset($_FILES["fileToUpload"])){
  $username= $_POST["username"];
  $File=  $_FILES["fileToUpload"]["name"];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // // Check if image file is a actual image or fake image
    // if(isset($_POST["submit"])) {
    // $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    // if($check !== false) {
    // echo "File is an image - " . $check["mime"] . ".";
    // $uploadOk = 1;
    // } else {
    // echo "File is not an image.";
    // $uploadOk = 0;
    // }
    // }


    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
    $uploadOk = 0;
    exit();
    }




    if ($uploadOk == 0) {
      echo "<pre>Sorry, your file was not uploaded <br></pre>";
      exit();
    } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "<pre>The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded. Your pfp has been updated</pre>";

        try {
          $db = new SQLite3('data.db');

        if ($db === false) {
            throw new Exception('Failed to connect to the database.');
        }
        $query = "UPDATE users SET profile = '$File' where username='$username';";
        $result = $db->exec($query);

        } catch (Exception $e) {
          echo 'Error: ' . $e->getMessage();
      }
      
      } else {
        echo "Sorry, there was an error uploading your file.<br>";
      }
    }


  exit();
}



if ((isset($_POST["username"]) && isset($_POST["password"])) || isset($_SESSION['username'])) {
    
  if(isset($_SESSION['username'])){

    $username = $_SESSION['username'];  
    $password = $_SESSION['password'];
  }
  else{
    $username =htmlspecialchars( $_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
  }

    
    
    try {
        $db = new SQLite3('data.db');

        if ($db === false) {
            throw new Exception('Failed to connect to the database.');
        }

                                // //Vulnerable to SQLi code because user input is treated as SQL code:

      // $query = "SELECT * FROM USERS WHERE username='$username' and password='$password';";
      // $result = $db->query($query);

                                // //This method makes it safe from SQLi by treating user input as plaintext rather than SQL code:
        $query = "SELECT * FROM USERS WHERE username = :username AND password = :password";
        $stmt = $db->prepare($query);

        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);

        $result = $stmt->execute();
        
        $debugQuery = "SELECT * FROM USERS WHERE username = '{$username}' AND password = '{$password}'";
        
        // echo "Executing query: $debugQuery <br>";

        $row = $result->fetchArray();
        if ($row) {
            include ("nav2.php");
            echo"<br><br><br><br>";
            // echo '<pre> Logged in as: @' . $row['username'].'</pre>';

            $_SESSION['username'] = $row['username'];
            $_SESSION['password'] = $row['password'];


            $email=$row['email'];
            $bio=$row['bio'];
            $profile=$row['profile'];
        } else {
            echo '<pre>   Incorrect username or password! </pre>';
            exit();
        }

    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit();
    }
} else {
  header("Location: index.php");
    echo 'Error: username and password are required.';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<script>
  function updateFileName() {
      var input = document.getElementById('fileToUpload');
      var fileNameDisplay = document.getElementById('fileNameDisplay');
      var fileName = input.files.length > 0 ? input.files[0].name : 'No file chosen';
      fileNameDisplay.textContent = fileName;
  }
</script>
  <a href="./chat.php">Chat with @lucifer</a>
<div class="card" style="width: 30rem; height:40rem;">

    <img class="profile" style="width:22rem; height:19rem;" src="./uploads/<?php echo isset($profile) ? $profile : 'default.jpeg'; ?>" class="card-img-top" alt="...">

  <div class="card-body">
      <p class="card-text"></p>
      
    </div>
    <ul class="list-group list-group-flush">
      
      <li class="list-group-item"><h5 class="card-title">@<?php echo $username ?></h5></li>
    <li class="list-group-item">Email: <?php echo $email ?></li>
    <li class="list-group-item">Bio: <?php echo $bio?></li>
  </ul>
  <div class="card-body flex">
  <div class="upload-form">
        <h6>Select image to update profile:</h6>
        <form class="flex" action="dashboard.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload" onchange="updateFileName()" required>
            <label for="fileToUpload">Choose File</label>
            <div class="filename" id="fileNameDisplay">No file chosen</div>
            <input type="hidden" id="username" name="username" value="<?php echo $username ;  ?>">
            <input type="submit" value="Upload Image" name="submit">

        </form>
    </div>
  </div>
</div>
</body>
</html>