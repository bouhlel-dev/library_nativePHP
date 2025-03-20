<?php
include('sqlConn/connect.php');
session_start();

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    try {
        $sql = "SELECT l.id, l.titre, l.ISBN, l.disponible, l.description, a.nom AS auteur_nom, g.nom AS genre_nom
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

    if (!$book) {
        die("Error: Book not found.");
    }
} else {
    die("Error: No book ID provided.");
}

$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Livre</title>
</head>
<body>
    <h1>Informations du Livre</h1>

    <h2><?php echo htmlspecialchars($book['titre']); ?></h2>
    <p><strong>Auteur:</strong> <?php echo htmlspecialchars($book['auteur_nom']); ?></p>
    <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre_nom']); ?></p>
    <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['ISBN']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>

    <?php if ($is_logged_in): ?>
    <form method="POST" action="borrow_book.php">
        <?php if ($book['disponible'] == 1): ?>  
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            <input type="submit" value="Emprunter" />
        <?php elseif ($book['disponible'] == 0): ?>
            <p><em>Ce livre est déjà emprunté.</em></p>
        <?php endif; ?>
    </form>
<?php else: ?>
    <p><em>Veuillez vous connecter pour emprunter ce livre.</em></p>
<?php endif; ?>

</body>
</html>
