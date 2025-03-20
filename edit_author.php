<?php
include('sqlConn/connect.php');

if (isset($_GET['id'])) {
    $author_id = $_GET['id'];

    $sql = "SELECT id, nom, biographie, date_de_naissance FROM auteur WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $author_id);
    $stmt->execute();
    $author = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$author) {
        die("Error: Author not found.");
    }
} else {
    die("Error: No author ID provided.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $biographie = $_POST['biographie'];
    $date_de_naissance = $_POST['date_de_naissance'];

    $sql = "UPDATE auteur SET nom = :nom, biographie = :biographie, date_de_naissance = :date_de_naissance WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':biographie', $biographie);
    $stmt->bindParam(':date_de_naissance', $date_de_naissance);
    $stmt->bindParam(':id', $author_id);

    if ($stmt->execute()) {
        header("Location: gestion_auteur.php"); 
        exit();
    } else {
        die("Error: Could not update author.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Livre</title>
    <link rel="stylesheet" href="css/headerCss.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<?php include("_header.php") ?>

<body>
    <section class="hero">
        <div class="container">
            <h2>Modifier les Informations du Auteur</h2>


            <?php if (isset($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>

            <form action="edit_author.php?id=<?php echo $author_id; ?>" method="POST">
    <table>
        <tr>
            <td><label for="titre">Nom:</label></td>
            <td><input class="search_input" type="text" name="nom" value="<?php echo $author['nom']; ?>" required><br></td>
        </tr>
        <tr>
            <td><label for="auteur_nom">Biographie:</label></td>
            <td><input class="search_input" type="text" name="biographie" value="<?php echo $author['biographie']; ?>" required><br></td>
        </tr>
        <tr>
            <td><label for="genre_nom">Date de naissance:</label></td>
            <td><input class="search_input" type="date" name="date_de_naissance" value="<?php echo $author['date_de_naissance']; ?>" required><br></td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="gestion_author.php" style="color:#FFC09F;">Retour à la gestion des Auteur</a>
                <button type="submit" class="card__btn">Mettre à jour</button>
            </td>
        </tr>
    </table>
</form>


        </div>
    </section>
</body>
<style>
    .container {
        position: relative;
        box-sizing: border-box;
        width: fit-content;
    }

    .card__btn {
        padding: 10px 20px;
        border: 2px solid #FFC09F;
        border-radius: 8px;
        background-color: transparent;
        color: #FFC09F;
        font-size: 1rem;
        cursor: pointer;
    }

    .card__btn:hover {
        background-color: #FFC09F;
        color: #fff;
    }
</style>
</html>
