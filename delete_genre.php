<?php
include('sqlConn/connect.php');

if (isset($_GET['id'])) {
    $genre_id = $_GET['id'];

    try {
        $sql = "DELETE FROM genre WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $genre_id);

        $stmt->execute();

        header("Location: gestion_genre.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Error: Genre ID not specified.");
}
?>
