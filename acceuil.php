<!------------------------------------------------------------php---------------------------------------------------------------->
<?php

include('sqlConn/connect.php');
$search = '';

if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

$res_search = '%' . $search . '%';



$sql = " SELECT l.id, l.titre, l.image, a.nom AS auteur, g.nom AS genre 
        FROM livre l
        LEFT JOIN auteur a ON l.auteur_id = a.id
        LEFT JOIN genre g ON l.genre_id = g.id
        WHERE l.titre LIKE '$res_search'";


$req = $conn->query($sql);
$books = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!-------------------------------------------------------------fin php--------------------------------------------------------------->







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Library Management</title>
    <link rel="stylesheet" href="css/headerCss.css">
    <link rel="stylesheet" href="css/stylesacceuil.css">

</head>

<body>
    <?php include("_header.php") ?>
    

    <section class="hero">

        <div class="container">
            <center>
                <h2>Find Your Favorite Book:</h2>
            </center>
            <form method="POST" action="acceuil.php">
                <div class="container">
                    <input checked="" class="checkbox" type="checkbox">
                    <div class="mainbox">
                        <div class="iconContainer">
                            <svg viewBox="0 0 512 512" height="1em" xmlns="http://www.w3.org/2000/svg"
                                class="search_icon">
                                <path
                                    d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z">
                                </path>
                            </svg>
                        </div>
                        <input class="search_input" placeholder="search" name="search" type="text" value="<?php if (!empty($search)) {
                                                                                                                echo ($search);
                                                                                                            }
                                                                                                            ?>">

                    </div>
                </div>

            </form>
            <!---------------------------------------------------------------------------------------------------------------------------->



            <!---------------------------------------------------------------------------------------------------------------------------->

            <style>
                .container {
                    position: relative;
                    box-sizing: border-box;
                    width: fit-content;
                }
            </style>
            <center>
                <h2>Popular Books...</h2>
            </center>
            <div class="grid-container">

                <?php
                if (!empty($books)) {
                    foreach ($books as $book) {
                        $id = $book['id'];
                        $titre = $book['titre'];
                        $auteur = $book['auteur'];
                        $image = $book['image'];

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
                        gap: 20px 20px;
                    }

                    .grid-item {
                        color: red;
                    }
                </style>


            </div>
            <br>
            <br>
            <br>
        </div>
    </section>




</body>

</html>




<!-------------------------------------------------------------------------------------------->