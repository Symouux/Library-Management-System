<?php
session_start();
require 'connected.php';
include "header/header.html";

// Vérification du rôle admin
if ($_SESSION['ROLE'] !== 'admin') {
    header('Location: liste_livres.php');
    exit();
}

// Catégories disponibles
$categories = [
    'Informatique',
    'Mathématiques', 
    'Physique',
    'Biologie',
    'Histoire',
    'Philosophie'
];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $auteur = htmlspecialchars($_POST['auteur']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $stock = intval($_POST['stock']);
    $description = htmlspecialchars($_POST['description']);

    

    // Gestion de la nouvelle catégorie si "autre" est sélectionné
    if ($categorie === 'autre' && !empty($_POST['nouvelle_categorie'])) {
        $categorie = htmlspecialchars($_POST['nouvelle_categorie']);
    }

    // Validation de la catégorie
    if (!in_array($categorie, $categories) && $categorie !== $_POST['nouvelle_categorie']) {
        $_SESSION['erreur'] = "Veuillez sélectionner une catégorie valide";
        header('Location: ajout_livre.php');
        exit();
    }

    // Gestion de l'upload de l'image
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $image = $destination;
        }
    }

    // Insertion dans la base de données
    $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, categorie, stock, image, description) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$titre, $auteur, $categorie, $stock, $image, $description])) {
        $_SESSION['success'] = "Livre ajouté avec succès.";
        header('Location: liste_livres.php');
        exit();
    } else {
        $_SESSION['erreur'] = "Erreur lors de l'ajout du livre.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre - MonShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/ajout_livre.css">
    <link rel="stylesheet" href="Footer/footer.css">
</head>
<body>
    <div class="background-wrapper"></div>
    <!-- <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.html">Symouux</a>
            </div>
            <nav class="navbar">
                <ul>
                    <li><a href="Accueil.html">Accueil</a></li>
                    <li><a href="dashboard_admin.php">Livres</a></li>
                    <li><a href="mes_emprunts.php">mes emprunts</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="Inscription.php">Inscription</a></li>
                    <li><a href="EspaceUser.php">Logout</a></li>
                </ul>
            </nav>
            <div class="icons">
                <a href="#"><i class="fas fa-search"></i></a>
                <a href="#"><i class="fas fa-user"></i></a>
            </div>
        </div>
    </header>     -->

    <main class="main-content">
        <h2 class="page-title">Ajouter un livre</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['erreur'] ?>
                <?php unset($_SESSION['erreur']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="book-form">
            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" placeholder="Titre du livre" required
                       value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="auteur">Auteur</label>
                <input type="text" id="auteur" name="auteur" placeholder="Auteur du livre" required
                       value="<?= isset($_POST['auteur']) ? htmlspecialchars($_POST['auteur']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="categorie">Catégorie</label>
                <select id="categorie" name="categorie" required>
                    <option value="">-- Sélectionnez une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"
                            <?= (isset($_POST['categorie']) && $_POST['categorie'] === $cat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="autre" <?= (isset($_POST['categorie']) && $_POST['categorie'] === 'autre') ? 'selected' : '' ?>>Autre (précisez)</option>
                </select>
                <input type="text" id="nouvelle_categorie" name="nouvelle_categorie" 
                       class="nouvelle-categorie" placeholder="Entrez la nouvelle catégorie"
                       value="<?= isset($_POST['nouvelle_categorie']) ? htmlspecialchars($_POST['nouvelle_categorie']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="stock">Stock disponible</label>
                <input type="number" id="stock" name="stock" placeholder="Quantité en stock" required min="1"
                       value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Description du livre"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Image du livre</label>
                <div class="file-input-wrapper">
                    <input type="file" id="image" name="image" accept="image/*">
                    <label for="image" class="file-input-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Choisir une image...</span>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Ajouter le livre</button>
        </form>
    </main>



    <?php

        include "Footer/footer.html";

    ?>

</body>
</html>

