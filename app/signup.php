<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<link rel="stylesheet" href="css/signup.css">
<body>
<?php include("nav1.php");?>

<div class="cont ">
<form  class="main" action="index.php" method="POST" >
    <div class="signup" style="font-size:25px">Sign-up</div>    

    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1">@</span>
        <input type="text" class="form-control" placeholder="Username" name="username" required>
    </div>

    <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="Email" name="email" required >
    </div>
    
    <div class="input-group mb-3">
        <input type="password" class="form-control" placeholder="Password" name="password" required>
    </div>
    
    <div class="input-group">
        <span class="input-group-text">Bio</span>
        <textarea class="form-control" placeholder="Enter your bio(optional)" name="bio"></textarea>
    </div>
    <span class="signup">
        Already registered? <a href="/index.php">Login</a>
    </span>
    <div class="bt">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
</form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
