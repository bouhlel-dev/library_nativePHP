<?php
include('sqlConn/connect.php');

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    try {
        $sql = "DELETE FROM livre WHERE id = :id";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $book_id);

        $stmt->execute();

        header("Location: gestion_livres.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: gestion_livres.php");
    exit();
}
?>
