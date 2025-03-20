<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_POST['deleteBook'])) {
    $bookId = $_POST['bookId'];

    $updatedBooks = [];
    foreach ($_SESSION['books'] as $book) {
        if ($book['id'] != $bookId) {
            $updatedBooks[] = $book;
        }
    }

    $_SESSION['books'] = $updatedBooks;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Library Management</title>
    <link rel="stylesheet" href="css/headerCss.css">
</head>

<header class="header">
    <div class="container">
        <img src="images/logo.png" alt="logo" width=5% class="logo">
        <h1 class="logo">Booktopia</h1>

        <ul>

            <?php
            if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
                echo '<li><a href="acceuil.php">Accueil</a></li>
                   <li><a href="gestion_livres.php">Livres</a></li>
                   <li><a href="gestion_auteur.php">Auteurs</a></li>
                   <li><a href="gestion_genre.php">Genre</a></li>
                   <li><a href="myaccount.php">My Account</a></li>
        </ul>           
</div>
        <img src="images/panierr.png" alt="" id="panierBtn" class="image-only-button">

                   ';
            } else {
                echo '<li><a href="login.php">Login</a></li>';
                echo '<li><a href="signup.php">signup</a></li> 
                       </ul>           
</div>';
            }
            ?>

    
</header>
<br>
<br>
<br>
<br>
<!-- Basket Popup -->
<div id="basketPopup" class="popup hidden">
    <div class="popup-inner">
        <h2>Votre Panier</h2>

        <table>
            <tbody>
                <?php if (!empty($_SESSION['books'])): ?>
                    <?php foreach ($_SESSION['books'] as $book): ?>

                        <tr>

                            <td>
                                <?php
                                if (isset($book['tmpBookImage']) && !empty($book['tmpBookImage'])) {
                                    echo '<img src="' . htmlspecialchars($book['tmpBookImage']) . '" alt="Book Image" width="50">';
                                } else {
                                    echo 'No image available';
                                }
                                ?>
                            </td>
                            <td><?php echo $book['tmpBookName']; ?></td>
                            <td><?php echo $book['tmpBookAuteur']; ?></td>
                            <td><?php echo $book['tmpBookGenre']; ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="bookId" value="<?php echo $book['id']; ?>">
                                    <input class="card__btn" type="submit" value="Valider" name="Valider" formaction="borrow_book.php">
                                    <input type="submit" class="card__btn" name="deleteBook" id="deleteBook" value="Supprimer">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Aucun livre Dans le panier.</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

        <button id="closeBasketPopup">Close</button>
    </div>
</div>

<script>
    const basketBtn = document.getElementById("panierBtn"); 
    const basketPopup = document.getElementById("basketPopup"); 
    const closeBasketPopup = document.getElementById("closeBasketPopup"); 


    basketBtn.addEventListener("click", () => {
        basketPopup.classList.remove("hidden"); 
    });

    closeBasketPopup.addEventListener("click", () => {
        basketPopup.classList.add("hidden"); 
    });
</script>

</div>