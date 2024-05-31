<?php
session_start();


if (isset($_POST["username"])) {
unset($_SESSION['username']);
unset($_SESSION['password']);
session_destroy();
$username = htmlspecialchars($_POST["username"]);
$password = htmlspecialchars($_POST["password"]);
$email = htmlspecialchars($_POST["email"]);
$bio = htmlspecialchars($_POST["bio"]);

try {
$db = new SQLite3('data.db');

$sql = "INSERT INTO USERS (USERNAME, PASSWORD, EMAIL, BIO) VALUES ('$username', '$password', '$email', '$bio')";
    if ($db->exec($sql)) {
    echo '<div class="alert alert-success alert-dismissible fade show " style="z-index: 100000; position: relative;" role="alert">
            <strong>Success!</strong> You have registered successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
} else {
    echo '<div class="alert alert-danger alert-dismissible fade show" style="z-index: 100000; position: relative;" role="alert">
            <strong>Error!</strong> There was a problem inserting your data: ' . $db->lastErrorMsg() . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
} catch (Exception $e) {
echo '<div class="alert alert-danger alert-dismissible fade show" style="z-index: 100000; position: relative;" role="alert">
        <strong>Error!</strong> There was a problem connecting to the database: ' . $e->getMessage() . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
}}



if(isset($_SESSION['username'])){
    header("location: dashboard.php");
}

if (isset($_POST['logout'])) {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    session_destroy();
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<link rel="stylesheet" href="css/index.css">

<body>
    <?php include ("nav1.php"); ?>

    <div class="cont">
        <div class="main ">
            <form action="dashboard.php" method="POST">
                <div class="signup" style="font-size:25px">Log-in</div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input autocomplete="false" type="text" class="form-control" placeholder="username" name="username" required >
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input autocomplete="false" type="password" class="form-control" placeholder="Password" name="password" required>
                </div>
                <div class="signup">Don't have an account? Don't worry <a href="signup.php">Sign up</a></div>
                <div class="bt">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>