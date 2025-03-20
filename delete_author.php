<?php
include('sqlConn/connect.php');

if (isset($_GET['id'])) {
    $author_id = $_GET['id'];

    $sql = "DELETE FROM auteur WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $author_id);

    if ($stmt->execute()) {
        header("Location: gestion_auteur.php"); 
        exit();
    } else {
        die("Error: Could not delete author.");
    }
} else {
    die("Error: No author ID provided.");
}
?>
