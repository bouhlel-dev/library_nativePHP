<?php
include('sqlConn/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);  
    $biographie = $_POST['biographie'];
    $date_de_naissance = $_POST['date_de_naissance'];

    if (empty($nom)) {
        die("Error: Author name cannot be empty.");
    }

    try {
        $checkAuthorQuery = "SELECT id FROM auteur WHERE nom = :nom LIMIT 1";
        $stmt = $conn->prepare($checkAuthorQuery);
        $stmt->bindParam(':nom', $nom);
        $stmt->execute();
        
        $author = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($author) {
            die("Error: Author '$nom' already exists in the database.");
        }

        $sql = "INSERT INTO auteur (nom, biographie, date_de_naissance) 
                VALUES (:nom, :biographie, :date_de_naissance)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':biographie', $biographie);
        $stmt->bindParam(':date_de_naissance', $date_de_naissance);

        $stmt->execute();

        header("Location: gestion_auteurs.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
