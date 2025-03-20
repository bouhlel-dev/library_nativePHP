<?php

include('sqlConn/connect.php');

$sql = "SELECT * FROM genre";
$stmt = $conn->prepare($sql);
$stmt->execute();
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['addGenre'])) {
  $nom = trim($_POST['nom']);
  $description = $_POST['description'];

  if (empty($nom)) {
    die("Error: Genre name cannot be empty.");
  }

  try {
    $checkAuthorQuery = "SELECT id FROM genre WHERE nom = :nom LIMIT 1";
    $stmt = $conn->prepare($checkAuthorQuery);
    $stmt->bindParam(':nom', $nom);
    $stmt->execute();

    $genre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($genre) {
      die("Error: Author '$nom' already exists in the database.");
    }


    $sql = "INSERT INTO genre (nom, description) 
              VALUES (:nom, :description)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':description', $description);

    $stmt->execute();

    header("Location: gestion_genre.php");
    exit();
  } catch (PDOException $e) {
    die("Error: " . $e->getMessage());
  }
}
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
      <center>
        <h2>genre management</h2>
      </center>
      <table>
        <br><br>
        <?php if ($_SESSION['role'] == 'admin'): ?>
          <button id="addBookBtn" class="card__btn" style="float: right;">Ajouter un Genre</button>
        <?php endif; ?>
        <thead>
          <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($genres) > 0): ?>
            <?php foreach ($genres as $genre): ?>
              <tr>
                <td><?php echo htmlspecialchars($genre['nom']); ?></td>
                <td><?php echo htmlspecialchars($genre['description']); ?></td>
                <td>
                  <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="edit_genre.php?id=<?php echo $genre['id']; ?>">Modifier</a> |
                    <a href="delete_genre.php?id=<?php echo $genre['id']; ?>">Supprimer</a> |
                  <?php else: ?>
                    <p>you have no rights.</p>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">Aucun genre trouvé.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>


      <div id="addBookModal" class="modal hidden">
        <div class="modal-content">
          <h3>Ajouter un Genre</h3>
          <form method="POST" action="">
            <table>

              <tbody>
                <tr>
                  <td>
                    <label for="titre" style="color: #000000;">Nom :</label>
                  </td>
                  <td><input class="search_input" type="text" id="title" name="nom" required placeholder="Donner le Nom"></td>
                </tr>
                <tr>
                  <td>
                    <label for="auteur" style="color: #000000;">Description :</label>
                  </td>
                  <td>
                    <input class="search_input" type="text" id="Biographie" name="description" required placeholder="Donner le Biographie">
                  </td>
                </tr>

              </tbody>
            </table>
            <button type="submit" class="card__btn" name="addGenre" value="Ajoute">Ajouter</button>
            <button class="card__btn" type="button" id="closeModal">Fermer</button>
          </form>
        </div>
      </div>

  </section>

  <script>
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