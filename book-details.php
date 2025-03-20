<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('sqlConn/connect.php');
$id = $_GET['id'];

$sql = "SELECT l.id, l.titre, l.ISBN, l.disponible, l.description, l.image ,
               a.nom AS auteur, g.nom AS genre 
        FROM livre l
        LEFT JOIN auteur a ON l.auteur_id = a.id
        LEFT JOIN genre g ON l.genre_id = g.id
        WHERE l.id Like '$id'";


$req = $conn->query($sql);
$books = $req->fetch(PDO::FETCH_ASSOC);
//echo var_dump($books);
//echo $books['image'];

if (isset($_POST['Emprunter'])) {
    $bookExists = false;
    if (!empty($_SESSION['books'])) {
        foreach ($_SESSION['books'] as $book) {
            if ($book['id'] == $id) {
                $bookExists = true;
                break;
            }
        }
    }

    if (!$bookExists) {
        $_SESSION['books'][] = [
            'id' => $books['id'],
            'tmpBookImage' => $books['image'],
            'tmpBookName' => $books['titre'],
            'tmpBookAuteur' => $books['auteur'],
            'tmpBookGenre' => $books['genre'],
        ];
    }
}


$book_id = $books['id']; 

$sql = "SELECT l.id, l.titre, l.image, a.nom AS auteur, g.nom AS genre 
        FROM livre l
        LEFT JOIN auteur a ON l.auteur_id = a.id
        LEFT JOIN genre g ON l.genre_id = g.id
        WHERE l.genre_id = (
            SELECT genre_id 
            FROM livre 
            WHERE id = $book_id
        )";

$req = $conn->query($sql);
$livres = $req->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>details</title>
    <link rel="stylesheet" href="css/headerCss.css">
    <link rel="stylesheet" href="css/stylesacceuil.css">




</head>

<?php include("_header.php") ?>


<body>
    <section class="hero">
        <div class="container">
            <div class="">
                <table style="margin-left: 100px; margin-right: 100px;">
                    <tr>
                        <td><img src="<?php echo ($books['image']); ?>" alt="bookimage" width=400></td>
                        <td style="text-align:justify; padding: 20px;">
                            <h2><?php echo ($books['titre']); ?></h2>

                            <p><strong>Auteur:</strong> <?php echo ($books['auteur']); ?></p>
                            <p><strong>Genre:</strong> <?php echo ($books['genre']); ?></p>
                            <p><strong>ISBN:</strong> <?php echo ($books['ISBN']); ?></p>
                            <p><strong>Disponible :</strong> <?php echo $books['disponible']; ?></p>
                            <p><strong>Description:</strong> <br><?php echo $books['description']; ?></p>

                            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                                <form method="POST" action="">
                                    <!-- action="borrow_book.php"> -->
                                    <?php if ($books['disponible'] == 1): ?>
                                        <input type="hidden" name="book_id" value="<?php echo $books['id']; ?>">
                                        <?php if ($_SESSION['role'] != 'admin'): ?>
                                            <input type="submit" class="card__btn" value="Emprunter" name="Emprunter" />
                                         <?php endif; ?>
                                    <?php elseif ($books['disponible'] == 0): ?>
                                        <p style="color: red;"><em>ce livre est déjà emprunté.</em></p>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <p style="color: red;"><em>veuillez vous connecter pour emprunter ce livre.</em></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <br>
                <h2 style="font-size:xx-large;">Similar Books...</h2>
                <section class="hero">

                    <div class="container">
                        <div class="grid-container">

                            <?php
                            if (!empty($books)) {
                                foreach ($livres as $livre) {
                                    $id = $livre['id'];
                                    $titre = $livre['titre'];
                                    $auteur = $livre['auteur'];
                                    $image = $livre['image'];

                                    echo '<div class="grid-item"><a href="book-details.php?id=' . $id . '">';
                                    echo '<div class="card" style="margin-right: 5px;">';
                                    echo '<div class="image" style="background-image: url(\'' . $image . '\'); background-size: cover; background-position: center;"></div>';
                                    echo '<span class="title">' . $titre . '</span>';
                                    echo '<span class="price">' . $auteur . '</span>';
                                    echo '</div></a></div>';
                                }
                            } else {
                                echo '<h3 style="color: red;">Aucun livre trouvé.</h3>';
                            }

                            ?>



                            <style>
                                .grid-container {
                                    display: grid;
                                    grid-template-columns: auto auto auto auto auto;
                                }

                                .grid-container {
                                    /*display: inline-grid;*/
                                    gap: 10px 0px;
                                }

                                .grid-item {
                                    color: red;
                                }
                            </style>


                        </div>



                    </div>
                </section>
            </div>

        </div>


    </section>
</body>
<br><br><br><br><br>


</html>



