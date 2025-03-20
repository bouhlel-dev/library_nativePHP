<?php
include('sqlConn/connect.php');

if (isset($_GET['id'])) {
    $genre_id = $_GET['id'];

    $sql = "SELECT * FROM genre WHERE id = :id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $genre_id);
    $stmt->execute();
    $genre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$genre) {
        die("Error: Genre not found.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = trim($_POST['nom']);
        $description = $_POST['description'];

        if (empty($nom)) {
            die("Error: Genre name cannot be empty.");
        }

        try {
            $sql = "UPDATE genre SET nom = :nom, description = :description WHERE id = :id";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $genre_id);

            $stmt->execute();

            header("Location: gestion_genre.php");
            exit();
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
} else {
    die("Error: Genre ID not specified.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Genre</title>
    <link rel="stylesheet" href="css/headerCss.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<?php include("_header.php") ?>

<body>
    <section class="hero">
        <div class="container">
            <h2>Modifier les Informations du Genre</h2>


            <?php if (isset($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>

            <form action="edit_genre.php?id=<?php echo $genre_id; ?>" method="POST">
                <table>
                    <tr>
                        <td><label for="titre">Nom:</label></td>
                        <td> <input class="search_input" type="text" name="nom" value="<?php echo htmlspecialchars($genre['nom']); ?>" required><br>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="auteur_nom">Description:</label></td>
                        <td><input class="search_input" type="text" name="description" value="<?php echo htmlspecialchars($genre['description']); ?>" required><br></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <a href="gestion_genre.php" style="color:#FFC09F;">Retour à la gestion des Genre</a>
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

