<?php

include('sqlConn/connect.php');
$searchTitle = '';
$searchAuthor = '';


if (isset($_POST['search_title'])) {
    $searchTitle = $_POST['search_title'];
}

if (isset($_POST['search_author'])) {
    $searchAuthor = $_POST['search_author'];
}

if (isset($_POST['addbook'])) {
    $titre = $_POST['titre'];
    $auteur_nom = $_POST['auteur_nom'];
    $genre_nom = $_POST['genre_nom'];
    $isbn = $_POST['isbn'];
    $description = $_POST['description'];
    $disponible = $_POST['disponible'];
    $image = $_POST['book_picture'];
    if (empty($description)) {
        die("error: Description cannot be empty.");
    }

    try {
        $check = "SELECT id FROM auteur WHERE nom = '$auteur_nom' LIMIT 1";
        $req = $conn->query($check);
        $author = $req->fetch(PDO::FETCH_ASSOC);
        if ($author) {
            $auteur_id = $author['id'];
        }
        $checkGenre = "SELECT id FROM genre WHERE nom = '$genre_nom' LIMIT 1";
        $reqq = $conn->query($checkGenre);

        $genre = $reqq->fetch(PDO::FETCH_ASSOC);
        if ($genre) {
            $genre_id = $genre['id'];
            $sql = "INSERT INTO livre (titre, auteur_id, genre_id, ISBN, description, disponible, image) 
                VALUES ('$titre', '$auteur_id', '$genre_id', '$isbn', '$description', '$disponible', '$image')";
            $stmt = $conn->exec($sql);

            header("Location: gestion_livres.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}




$withTitle = '%' . $searchTitle . '%';
$withAuthor = '%' . $searchAuthor . '%';



$sql = "SELECT l.id, l.titre, l.ISBN, l.disponible, l.description, 
               a.nom AS auteur, g.nom AS genre 
        FROM livre l
        LEFT JOIN auteur a ON l.auteur_id = a.id
        LEFT JOIN genre g ON l.genre_id = g.id
        WHERE l.titre LIKE '$withTitle' AND a.nom LIKE '$withAuthor'";


$req = $conn->query($sql);
$books = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Library Management</title>
    <link rel="stylesheet" href="css/headerCss.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/books.css">

</head>

<body>
    <?php include("_header.php") ?>

    <section class="hero">
        <div class="container">
            <h2>Chercher un livre</h2>
            <div class="container">


                <form method="POST" action="gestion_livres.php">
                    <input type="text" name="search_title" class="search_input" placeholder="Rechercher par titre" value="<?php
                                                                                                                            if (!empty($searchTitle)) {
                                                                                                                                echo htmlspecialchars($searchTitle);
                                                                                                                            }
                                                                                                                            ?>">
                    <input type="text" name="search_author" class="search_input" placeholder="Rechercher par auteur" value="<?php
                                                                                                                            if (!empty($searchAuthor)) {
                                                                                                                                echo htmlspecialchars($searchAuthor);
                                                                                                                            }
                                                                                                                            ?>">
                    <button class="card__btn" type="submit">Rechercher</button>
                </form>

               
                    <table >
                        <br><br>
                        <?php if ($_SESSION['role']=='admin'):?>
                                <button id="addBookBtn" class="card__btn" style="float: right;">Ajouter un Livre</button>
                            <?php endif;?>
                        

                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Genre</th>
                                <th>ISBN</th>
                                <th>Disponible</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($books)): ?>
                                <?php foreach ($books as $book): ?>
                                    <tr>
                                        <td><?php echo $book['titre']; ?></td>
                                        <td><?php echo $book['auteur']; ?></td>
                                        <td><?php echo $book['genre']; ?></td>
                                        <td><?php echo $book['ISBN']; ?></td>
                                        <td><?php
                                           if ($book['disponible'] == 1)
                                           {echo 'oui';}
                                           else{echo 'non';};?>
                                         </td>
                                        <td><?php echo $book['description']; ?></td>
                                        <td>
                            <?php if ($_SESSION['role']=='admin'):?>
                            <a href="edit_book.php?id=<?php echo $book['id']; ?>">Modifier</a> | 
                            <a href="delete_book.php?id=<?php echo $book['id']; ?>">Supprimer</a> |
                            
                            <a href="book-details.php?id=<?php echo $book['id']; ?>">Voir Détails</a>
                            <?php else:?>
                                <a href="book-details.php?id=<?php echo $book['id']; ?>">Voir Détails</a>
                                <?php endif;?>
                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">Aucun livre trouvé.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>


                    <div id="addBookModal" class="modal hidden">
                        <div class="modal-content">
                            <h3>Ajouter un Livre</h3>
                            <form method="POST" action="">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label for="titre" style="color: #000000;">Titre :</label>
                                            </td>
                                            <td><input class="search_input" type="text" id="title" name="titre" required placeholder="Donner le titre"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="auteur"style="color: #000000;">Auteur :</label>
                                            </td>
                                            <td>
                                                <?php
                                                $sql = "SELECT id, nom FROM auteur";
                                                $result = $conn->query($sql);
                                                echo '<select class="search_input" name="auteur_nom" id="author" required><br>';
                                                if ($result->rowCount() > 0) {
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="' . $row['nom'] . '">' . $row['nom'] . '</option>';
                                                    }
                                                } else {
                                                    echo '<option value="">No categories available</option>';
                                                }
                                                echo "</select>";

                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="genre" style="color: #000000;">Genre :</label>
                                            </td>
                                            <td>
                                                <?php
                                                $sql = "SELECT id, nom FROM genre";
                                                $result = $conn->query($sql);
                                                echo '<select class="search_input" name="genre_nom" id="genre" required><br>';
                                                if ($result->rowCount() > 0) {
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="' . $row['nom'] . '">' . $row['nom'] . '</option>';
                                                    }
                                                } else {
                                                    echo '<option value="">No categories available</option>';
                                                }
                                                echo "</select>";

                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="isbn"style="color: #000000;">ISBN :</label>
                                            </td>
                                            <td>
                                                <input class="search_input" type="text" id="isbn" name="isbn" required placeholder="Donner le isbn">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="description"style="color: #000000;">Description :</label>
                                            </td>
                                            <td>
                                                <textarea class="search_input" name="description" required placeholder="Donner le Description"></textarea><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="disponible"style="color: #000000;">Disponible :</label>
                                            </td>
                                            <td>
                                                <select class="search_input" name="disponible" required>
                                                    <option value="1">Oui</option>
                                                    <option value="0">Non</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="image" style="color: #000000;">Url image :</label>
                                            </td>
                                            <td>
                                                <input type="text" class="search_input" name="book_picture" placeholder="Donner lien d'un image" required />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="submit" class="card__btn" name="addbook" value="Ajoute">Ajouter</button>
                                <button class="card__btn" type="button" id="closeModal">Fermer</button>
                            </form>
                        </div>
                    </div>
                
    </section>

    <script>
        // JavaScript pour afficher et masquer le formulaire modal
        const addBookButton = document.getElementById("addBookBtn");
        const addBookModal = document.getElementById("addBookModal");
        const closeModal = document.getElementById("closeModal");

        addBookButton.addEventListener("click", () => {
            addBookModal.classList.remove("hidden");
        });

        closeModal.addEventListener("click", () => {
            addBookModal.classList.add("hidden");
        });
    </script>

    </div>
    <style>
        .container {
            position: relative;
            box-sizing: border-box;
            width: fit-content;
        }
    </style>
    <br>
    <br>
    <br>
    </div>
    </section>
</body>
<style>
    /* Modal */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: auto;

        /* Arrière-plan semi-transparent */
        display: flex;
        justify-content: center;
        align-items: center;
        visibility: hidden;
        opacity: 0;
        transition: visibility 0s, opacity 0.3s;
    }

    .modal-content {
        padding: 2rem;
        margin-top: 100px;
        margin-bottom: 50px;
        background: rgba(218, 218, 213, 0.95);
        /* Fond semi-transparent pour harmonisation */
        padding: 2px;
        padding-top: 2px;
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        /* Ajout d'une ombre */
        width: 90%;
        max-width: 500px;
        text-align: center;
    }

    .modal-content table {
        margin-top: 2px;
    }

    .modal-content table td {
        padding-top: 2px;
        padding-bottom: 2px;
        margin-top: 2px;
    }

    .modal-content h2 {
        font-size: 1.8em;
        color: #000000;
        margin-bottom: 1rem;
    }


    .modal-content form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        color: #232323;
    }

    .modal-content td {
        padding-left: 20px;
        font-size: 1em;
        font-weight: bold;
        text-align: left;
        margin-bottom: 0.5rem;
    }

    .modal-content input[type="text"] {
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 1em;
        width: 70%;
        box-sizing: border-box;
    }

    .modal-content input[type="checkbox"] {
        margin-right: 0.5rem;
    }

    .modal-content button {
        padding-top: 0.1rem 1rem;
        border: none;
        border-radius: 10px;
        font-size: 1em;
        cursor: pointer;
        transition: background 0.3s;
    }

    .modal-content button[type="submit"] {
        background-color: #7258d1;
        color: white;
    }

    .modal-content button[type="submit"]:hover {
        background-color: #7258d1;
    }

    .modal-content button#closeModal {
        background-color: #dc3545;
    }

    .modal-content button#closeModal:hover {
        background-color: #c82333;
    }

    .modal.hidden {
        visibility: hidden;
        opacity: 0;
    }

    .modal:not(.hidden) {
        visibility: visible;
        opacity: 1;
    }
</style>

</html>