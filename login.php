<?php
session_start();
include('sqlConn/connect.php');

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    try {
        $sql = "select id, password, role from utilisateur where email ='$email'";
        $pdo = $conn->query($sql);
        if ($pdo->rowCount() > 0) {
            $user = $pdo->fetch(PDO::FETCH_ASSOC);
            if ($user['password'] == $password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['user_logged_in'] = true;
                $_SESSION['role']=$user['role'];
                header("Location: acceuil.php");
                exit();
            }
            else {
                $error = "email ou mot de passe incorrect";
            }
        }
        else {
            $error = "email ou mot de passe incorrect";
        }
    }
    catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = "un erreur de connection";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/connexion.css">
</head>
<body>
<?php include("_header.php")?>
    <div class="wrapper">
        <div class="title">Log in</div>
        <p style="color: red;"><?php echo ($error); ?></p>
        <form action="login.php" method="POST" class="form">
            <input type="email" placeholder="Email" name="email" class="input">
            <input type="password" placeholder="Password" name="password" class="input">
            <button type="submit" class="btn">Let's go!</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>
