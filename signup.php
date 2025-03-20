<?php
include('sqlConn/connect.php');
$message = "";
$test=0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name=$_POST['name'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $password=$_POST['password'];

    $pdo = "select * from utilisateur where email='".$email."'";
    $req = $conn->query($pdo);

    if ($req->rowCount()==0) {
        $insert = "insert into utilisateur (nom,email,telephone,password) values('$name','$email','$phone','$password')";
        $result = $conn->exec($insert);
        $message = "inscription réussie!";
    
        sleep(3);
        header("Location: login.php");
            
        exit();

    }
    else{
        $message="l'email deja existe!";
    }
    
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/connexion.css">
    
</head>
<body>
    <?php include("_header.php")?>
    <div class="wrapper">
        <div class="title">Sign up</div>
        <p style="color: red;"><?php echo ($message); ?></p>
        
        <form action="signup.php" method="POST" class="form">
            <input type="text" placeholder="Name" name="name" class="input">
            <input type="text" placeholder="Phone" name="phone" class="input">
            <input type="email" placeholder="Email" name="email" class="input">
            <input type="password" placeholder="Password" name="password" class="input">
            <button type="submit" class="btn">Confirm!</button>
        </form>
        <p>Already have an account? <a href="login.php">Log in here</a></p>
    </div>
</body>
</html>
