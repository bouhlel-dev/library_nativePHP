<?php
session_start(); 
include('sqlConn/connect.php');
$user_id = $_SESSION['user_id'];

if (isset($_POST['logout'])) {
    session_unset(); 
    session_destroy(); 
    header('Location: index.php'); 
    exit();
}

if (isset($_POST['return_book']) && isset($_POST['book_id'])) {
    $todayDate = date("Y/m/d h:i:s");
    $book_id = $_POST['book_id'];
    $returnDisp = "UPDATE livre SET disponible = 1 WHERE id = $book_id";
    $conn->query($returnDisp);

    $return = "UPDATE emprunt SET returned = 1 WHERE livre_id = $book_id AND utilisateur_id = $user_id";
    $conn->query($return);
    $upDate = "UPDATE emprunt SET date_retour = '$todayDate' WHERE livre_id = $book_id AND utilisateur_id = $user_id";
    $conn->query($upDate);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


if (!isset($_SESSION['user_id'])) {
    header('Location: acceuil.php');
    exit();
}


$req = $conn->query("SELECT * FROM utilisateur WHERE id = $user_id");


$data = $req->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT livre.id AS book_id, livre.titre, livre.auteur_id, emprunt.date_emprunt,emprunt.date_retour,emprunt.returned, auteur.nom AS author_name
FROM livre
INNER JOIN auteur ON livre.auteur_id = auteur.id
INNER JOIN emprunt ON livre.id = emprunt.livre_id
WHERE emprunt.utilisateur_id = $user_id";
$send = $conn->query($sql);
$books = $send->fetchAll(PDO::FETCH_ASSOC);

if ($data) {
    $name = htmlspecialchars($data['nom']);
} else {
    die('User not found');
}


if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $image = file_get_contents($_FILES['profile_picture']['tmp_name']); // Get binary data from uploaded file
    $stmt = $conn->prepare("UPDATE utilisateur SET profile_picture = :profile_picture WHERE id = :id");
    $stmt->bindParam(':profile_picture', $image, PDO::PARAM_LOB); // Bind the binary data to the statement
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "Profile picture updated successfully!";
    } else {
        echo "Error updating profile picture.";
    }
}



$log = "SELECT 
    livre.id AS book_id, 
    livre.titre AS book_title, 
    livre.auteur_id AS author_id, 
    auteur.nom AS author_name, 
    emprunt.date_emprunt AS borrow_date, 
    emprunt.date_retour AS return_date, 
    emprunt.returned AS is_returned, 
    emprunt.utilisateur_id AS borrower_id, 
    utilisateur.nom AS borrower_name
FROM 
    livre
LEFT JOIN 
    auteur ON livre.auteur_id = auteur.id
LEFT JOIN 
    emprunt ON livre.id = emprunt.livre_id
LEFT JOIN 
    utilisateur ON emprunt.utilisateur_id = utilisateur.id
ORDER BY 
    livre.id, emprunt.date_emprunt;
";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Library Management</title>
    <link rel="stylesheet" href="css/compte.css">
    <link rel="stylesheet" href="css/books.css">



</head>

<body>

    <?php include("_header.php") ?>

    <section class="hero">

        <center>
            <div class="card">
                <div class="card__avatar">
                    <?php
                    if ($data['profile_picture']) {
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($data['profile_picture']) . '" alt="Profile Picture" />';
                    } else {
                        echo '<svg viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg"><circle cx="64" cy="64" fill="#ff8475" r="60"></circle><circle cx="64" cy="64" fill="#f85565" opacity=".4" r="48"></circle><path d="m64 14a32 32 0 0 1 32 32v41a6 6 0 0 1 -6 6h-52a6 6 0 0 1 -6-6v-41a32 32 0 0 1 32-32z" fill="#7f3838"></path><path d="m62.73 22h2.54a23.73 23.73 0 0 1 23.73 23.73v42.82a4.45 4.45 0 0 1 -4.45 4.45h-41.1a4.45 4.45 0 0 1 -4.45-4.45v-42.82a23.73 23.73 0 0 1 23.73-23.73z" fill="#393c54" opacity=".4"></path><circle cx="89" cy="65" fill="#fbc0aa" r="7"></circle><path d="m64 124a59.67 59.67 0 0 0 34.69-11.06l-3.32-9.3a10 10 0 0 0 -9.37-6.64h-43.95a10 10 0 0 0 -9.42 6.64l-3.32 9.3a59.67 59.67 0 0 0 34.69 11.06z" fill="#4bc190"></path><path d="m45 110 5.55 2.92-2.55 8.92a60.14 60.14 0 0 0 9 1.74v-27.08l-12.38 10.25a2 2 0 0 0 .38 3.25z" fill="#356cb6" opacity=".3"></path><path d="m71 96.5v27.09a60.14 60.14 0 0 0 9-1.74l-2.54-8.93 5.54-2.92a2 2 0 0 0 .41-3.25z" fill="#356cb6" opacity=".3"></path><path d="m57 123.68a58.54 58.54 0 0 0 14 0v-25.68h-14z" fill="#fff"></path><path d="m64 88.75v9.75" fill="none" stroke="#fbc0aa" stroke-linecap="round" stroke-linejoin="round" stroke-width="14"></path><circle cx="39" cy="65" fill="#fbc0aa" r="7"></circle><path d="m64 91a25 25 0 0 1 -25-25v-16.48a25 25 0 1 1 50 0v16.48a25 25 0 0 1 -25 25z" fill="#ffd8c9"></path><path d="m91.49 51.12v-4.72c0-14.95-11.71-27.61-26.66-28a27.51 27.51 0 0 0 -28.32 27.42v5.33a2 2 0 0 0 2 2h6.81a8 8 0 0 0 6.5-3.33l4.94-6.88a18.45 18.45 0 0 1 1.37 1.63 22.84 22.84 0 0 0 17.87 8.58h13.45a2 2 0 0 0 2.04-2.03z" fill="#bc5b57"></path><path d="m62.76 36.94c4.24 8.74 10.71 10.21 16.09 10.21h5" style="fill:none;stroke-linecap:round;stroke:#fff;stroke-miterlimit:10;stroke-width:2;opacity:.1"></path><path d="m71 35c2.52 5.22 6.39 6.09 9.6 6.09h3" style="fill:none;stroke-linecap:round;stroke:#fff;stroke-miterlimit:10;stroke-width:2;opacity:.1"></path><circle cx="76" cy="62.28" fill="#515570" r="3"></circle><circle cx="52" cy="62.28" fill="#515570" r="3"></circle><ellipse cx="50.42" cy="69.67" fill="#f85565" opacity=".1" rx="4.58" ry="2.98"></ellipse><ellipse cx="77.58" cy="69.67" fill="#f85565" opacity=".1" rx="4.58" ry="2.98"></ellipse><g fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="m64 67v4" stroke="#fbc0aa" stroke-width="4"></path><path d="m55 56h-9.25" opacity=".2" stroke="#515570" stroke-width="2"></path><path d="m82 56h-9.25" opacity=".2" stroke="#515570" stroke-width="2"></path></g><path d="m64 84c5 0 7-3 7-3h-14s2 3 7 3z" fill="#f85565" opacity=".4"></path><path d="m65.07 78.93-.55.55a.73.73 0 0 1 -1 0l-.55-.55c-1.14-1.14-2.93-.93-4.27.47l-1.7 1.6h14l-1.66-1.6c-1.34-1.4-3.13-1.61-4.27-.47z" fill="#f85565"></path></svg></div>';
                    }
                    ?>


                    <div class="card__title"><?php echo $name ?></div>
                    <div class="card__subtitle">The more that you read, the more things you will know</div>
                    <form action="" method="POST">
                        <button type="submit" name="logout" class="card__btn">Logout</button><br><br>
                    </form>
                    <div class="card__wrapper">
                        <button id="addBookBtn" class="card__btn"><?php
                                                                    if ($_SESSION['role'] == 'admin') {
                                                                        echo "archive";
                                                                    } else {
                                                                        echo "my books";
                                                                    }
                                                                    ?></button>
                        <form action="" method="POST" enctype="multipart/form-data">

                            <input type="file" class="card__btn" name="profile_picture" accept="image/*" />
                            <button type="submit" class="card__btn">Upload Picture</button>

                        </form>



                    </div>



                </div>
            </div>

        </center>
    </section>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <?php endif; ?>

    <div id="addBookModal" style="display:none;">
        <section class="hero">
            <table>
                <?php if ($_SESSION['role'] != 'admin'): ?>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Date Emprunt</th>
                            <th>Date Retoure</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($books)): ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo $book['titre']; ?></td>
                                    <td><?php echo $book['author_name']; ?></td>
                                    <td><?php echo $book['date_emprunt']; ?></td>
                                    <td><?php echo $book['date_retour']; ?></td>
                                    <td>
                                        <?php if ($book['returned'] == 1): ?>
                                            <strong>Status: Returned</strong></form>
                                        <?php else: ?>
                                            <form action="" method="POST">
                                                <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']) ?>">
                                                <input class="card__btn" type="submit" value="returner" name="return_book">
                                            </form>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>



                        <?php else: ?>
                            <tr>
                                <td colspan="7">Aucun livre trouvé.</td>
                            </tr>
                        <?php endif; ?>

                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <thead>
                            <tr>
                                <th>livre id</th>
                                <th>livre titre</th>
                                <th>author id</th>
                                <th>Date Emprunt</th>
                                <th>Date Retoure</th>
                                <th>status return</th>
                                <th>emprunt id</th>
                                <th>emprunt nom</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php
                        $getlog = $conn->query($log);
                        $logs = $getlog->fetchAll(PDO::FETCH_ASSOC);
                        if (!empty($log)):
                        ?>
                            <?php foreach ($logs as $line): ?>
                                <tr>
                                    <td><?php echo $line['book_id']; ?></td>
                                    <td><?php echo $line['book_title']; ?></td>
                                    <td><?php echo $line['author_id']; ?></td>
                                    <td><?php echo $line['borrow_date']; ?></td>
                                    <td><?php echo $line['return_date']; ?></td>
                                    <td><?php echo $line['is_returned']; ?></td>
                                    <td><?php echo $line['borrower_id']; ?></td>
                                    <td><?php echo $line['borrower_name']; ?></td>
                                </tr>
                                
                            <?php endforeach; ?>
                            

                        <?php else: ?>
                            <tr>
                                <td colspan="7">Aucun histoire trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    
                <?php endif; ?>

            </table>
        </section>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <?php else: ?>
            <br><br><br><br>
        <?php endif; ?>
        <center><button class="card__btn" type="button" id="closeModalBtn">Fermer</button></center>
       
    </div>
    <script>
        document.getElementById('addBookBtn').addEventListener('click', function() {
            document.getElementById('addBookModal').style.display = 'block';
        });

        document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('addBookModal').style.display = 'none';
        });
    </script>
</body>

</html>



<!-------------------------------------------------------------------------------------------------------------------------------->