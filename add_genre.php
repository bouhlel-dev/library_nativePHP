<?php
include('sqlConn/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $description = $_POST['description'];

    if (empty($nom)) {
        die("Error: Genre name cannot be empty.");
    }

    try {
        $checkGenreQuery = "SELECT id FROM genre WHERE nom = :nom LIMIT 1";
        $stmt = $conn->prepare($checkGenreQuery);
        $stmt->bindParam(':nom', $nom);
        $stmt->execute();
        
        $genre = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($genre) {
            die("Error: Genre '$nom' already exists in the database.");
        }

        $sql = "INSERT INTO genre (nom, description) VALUES (:nom, :description)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);

        $stmt->execute();

        header("Location: gestion_genre.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
