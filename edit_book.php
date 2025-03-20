<?php
// Include database connection

include('sqlConn/connect.php');

// Check if an ID is provided in the URL
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch book details from the database
    try {
        $sql = "SELECT l.id, l.titre, l.ISBN, l.disponible, l.description, a.nom AS auteur_nom, g.nom AS genre_nom, l.auteur_id 
                FROM livre l
                LEFT JOIN auteur a ON l.auteur_id = a.id
                LEFT JOIN genre g ON l.genre_id = g.id
                WHERE l.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $book_id);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    // If book not found, show an error
    if (!$book) {
        die("Error: Book not found.");
    }
} else {
    die("Error: No book ID provided.");
}

// Handle form submission to update book details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated data from the form
    $titre = $_POST['titre'];
    $auteur_nom = $_POST['auteur_nom']; // This is the name of the author, we need to get the ID
    $genre_nom = $_POST['genre_nom']; // Same as for auteur_nom, this is the name of the genre, not the ID
    $isbn = $_POST['isbn'];
    $description = $_POST['description'];
    $disponible = $_POST['disponible'];

    // Get the author ID from the auteur table based on the provided name
    try {
        $sql_auteur = "SELECT id FROM auteur WHERE nom = :auteur_nom";
        $stmt_auteur = $conn->prepare($sql_auteur);
        $stmt_auteur->bindParam(':auteur_nom', $auteur_nom);
        $stmt_auteur->execute();
        $auteur = $stmt_auteur->fetch(PDO::FETCH_ASSOC);
        if ($auteur) {
            $auteur_id = $auteur['id'];
        } else {
            die("Error: Author not found.");
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    // Get the genre ID from the genre table based on the provided genre name
    try {
        $sql_genre = "SELECT id FROM genre WHERE nom = :genre_nom";
        $stmt_genre = $conn->prepare($sql_genre);
        $stmt_genre->bindParam(':genre_nom', $genre_nom);
        $stmt_genre->execute();
        $genre = $stmt_genre->fetch(PDO::FETCH_ASSOC);
        if ($genre) {
            $genre_id = $genre['id'];
        } else {
            die("Error: Genre not found.");
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    // Update book details in the database
    try {
        $update_sql = "UPDATE livre 
                       SET titre = :titre, auteur_id = :auteur_id, genre_id = :genre_id, 
                           ISBN = :isbn, description = :description, disponible = :disponible 
                       WHERE id = :id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':titre', $titre);
        $update_stmt->bindParam(':auteur_id', $auteur_id);
        $update_stmt->bindParam(':genre_id', $genre_id);
        $update_stmt->bindParam(':isbn', $isbn);
        $update_stmt->bindParam(':description', $description);
        $update_stmt->bindParam(':disponible', $disponible);
        $update_stmt->bindParam(':id', $book_id);
        $update_stmt->execute();
        $message = "Book details updated successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
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
            <h2>Modifier les Informations du Livre</h2>

            <?php if (isset($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>

            <form action="edit_book.php?id=<?php echo $book['id']; ?>" method="POST">
                <table>
                    <tr>
                        <td><label for="titre">Titre:</label></td>
                        <td> <input class="search_input" type="text" name="titre" value="<?php echo htmlspecialchars($book['titre']); ?>" required><br>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="auteur_nom">Auteur:</label></td>
                        <td><input class="search_input" type="text" name="auteur_nom" value="<?php echo htmlspecialchars($book['auteur_nom']); ?>" required><br></td>
                    </tr>
                    <tr>
                        <td><label for="genre_nom">Genre:</label></td>
                        <td> <input class="search_input" type="text" name="genre_nom" value="<?php echo htmlspecialchars($book['genre_nom']); ?>" required><br></td>
                    </tr>
                    <tr>
                        <td> <label for="isbn">ISBN:</label></td>
                        <td> <input class="search_input" type="text" name="isbn" value="<?php echo htmlspecialchars($book['ISBN']); ?>" required><br></td>
                    </tr>
                    <tr>
                        <td><label for="description">Description:</label></td>
                        <td><textarea class="search_input" name="description"  required><?php echo htmlspecialchars($book['description']); ?></textarea><br></td>
                    </tr>
                    <tr>
                        <td><label for="disponible">Disponible:</label></td>
                        <td><select class="search_input" name="disponible" required>
                                <option value="1" <?php echo ($book['disponible'] == 1 ? 'selected' : ''); ?>>Oui</option>
                                <option value="0" <?php echo ($book['disponible'] == 0 ? 'selected' : ''); ?>>Non</option>
                            </select></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <a href="gestion_livres.php" style="color:#FFC09F;">Retour à la gestion des livres</a>
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
        /* Larger buttons */
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


<!-------------------------------------------------------------------------------------------------------------->