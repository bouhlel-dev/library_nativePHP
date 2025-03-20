<?php
include('sqlConn/connect.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_POST['bookId'])) {
    $bookId = $_POST['bookId'];
    try {
        $uid=$_SESSION['user_id'];
        $todayDate=date("Y/m/d h:i:s");
        $twoWeeks=strtotime("+2 Weeks");
        $returnDate=date("Y/m/d h:i:s", $twoWeeks);
        $sql = "update livre set disponible = 0 where id =$bookId;";
        $stmt = $conn->exec($sql);
        $empr = "insert into emprunt(livre_id, utilisateur_id, date_emprunt, date_retour) VALUES ( '$bookId','$uid', '$todayDate', '$returnDate')";
        $conn->exec($empr);

        $bookId = $_POST['bookId'];
        $updatedBooks = [];
        foreach ($_SESSION['books'] as $book) {
            if ($book['id'] != $bookId) {
                $updatedBooks[] = $book;
            }
        }

        $_SESSION['books'] = $updatedBooks;

        header("location: acceuil.php");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
else {
    die("Error: No book ID provided.");
}


$is_logged_in = isset($_SESSION['user_id']);
?>

