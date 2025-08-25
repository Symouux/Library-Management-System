<?php
session_start();
require 'connected.php';
include "header/header.html";


if ($_SESSION['ROLE'] !== 'admin') {
    header('Location: liste_livres.php');
    exit();
}


$categories = [
    'Informatique',
    'Mathématiques', 
    'Physique',
    'Biologie',
    'Histoire',
    'Philosophie'
];


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erreur'] = "ID du livre invalide";
    header('Location: liste_livres.php');
    exit();
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$id]);
$livre = $stmt->fetch();

if (!$livre) {
    $_SESSION['erreur'] = "Livre introuvable";
    header('Location: liste_livres.php');
    exit();
}


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

    
    if (!in_array($categorie, $categories) && $categorie !== $_POST['nouvelle_categorie']) {
        $_SESSION['erreur'] = "Veuillez sélectionner une catégorie valide";
        header("Location: modifier_livre.php?id=$id");
        exit();
    }

    // Gestion de l'upload de l'image
    $image = $livre['image']; 
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Supprimer l'ancienne image si elle existe
        if (!empty($livre['image']) && file_exists($livre['image'])) {
            unlink($livre['image']);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $image = $destination;
        }
    }

    
    $stmt = $pdo->prepare("UPDATE livres SET titre = ?, auteur = ?, categorie = ?, stock = ?, image = ?, description = ? WHERE id = ?");
    if ($stmt->execute([$titre, $auteur, $categorie, $stock, $image, $description, $id])) {
        $_SESSION['success'] = "Livre modifié avec succès.";
        header('Location: liste_livres.php');
        exit();
    } else {
        $_SESSION['erreur'] = "Erreur lors de la modification du livre.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un livre - MonShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/modifier_livres.css">
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
        <h2 class="page-title">Modifier le livre</h2>
        
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
                       value="<?= htmlspecialchars($livre['titre']) ?>">
            </div>
            
            <div class="form-group">
                <label for="auteur">Auteur</label>
                <input type="text" id="auteur" name="auteur" placeholder="Auteur du livre" required
                       value="<?= htmlspecialchars($livre['auteur']) ?>">
            </div>
            
            <div class="form-group">
                <label for="categorie">Catégorie</label>
                <select id="categorie" name="categorie" required>
                    <option value="">-- Sélectionnez une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"
                            <?= ($livre['categorie'] === $cat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="autre" <?= (!in_array($livre['categorie'], $categories)) ? 'selected' : '' ?>>Autre (précisez)</option>
                </select>
                <input type="text" id="nouvelle_categorie" name="nouvelle_categorie" 
                       class="nouvelle-categorie" placeholder="Entrez la nouvelle catégorie"
                       value="<?= (!in_array($livre['categorie'], $categories)) ? htmlspecialchars($livre['categorie']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="stock">Stock disponible</label>
                <input type="number" id="stock" name="stock" placeholder="Quantité en stock" required min="0"
                       value="<?= htmlspecialchars($livre['stock']) ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Description du livre"><?= htmlspecialchars($livre['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Image du livre</label>
                <?php if (!empty($livre['image'])): ?>
                    <div class="current-image">
                        <img src="<?= htmlspecialchars($livre['image']) ?>" alt="Image actuelle">
                        <div class="remove-image" onclick="document.getElementById('remove_image').value = '1'; this.parentElement.style.display = 'none';">
                            <i class="fas fa-trash"></i> Supprimer l'image actuelle
                        </div>
                        <input type="hidden" id="remove_image" name="remove_image" value="0">
                    </div>
                <?php endif; ?>
                <div class="file-input-wrapper">
                    <input type="file" id="image" name="image" accept="image/*">
                    <label for="image" class="file-input-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span><?= empty($livre['image']) ? 'Choisir une image...' : 'Remplacer l\'image...' ?></span>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Enregistrer les modifications</button>
        </form>
    </main>

    <script>
        // Afficher/masquer le champ nouvelle catégorie
        // document.getElementById('categorie').addEventListener('change', function() {
        //     const nouvelleCategorie = document.getElementById('nouvelle_categorie');
        //     nouvelleCategorie.style.display = this.value === 'autre' ? 'block' : 'none';
        //     if (this.value !== 'autre') {
        //         nouvelleCategorie.value = '';
        //     }
        // });

        // // Déclencher le changement au chargement si "autre" est déjà sélectionné
        // window.addEventListener('DOMContentLoaded', function() {
        //     const categorieSelect = document.getElementById('categorie');
        //     if (categorieSelect.value === 'autre') {
        //         document.getElementById('nouvelle_categorie').style.display = 'block';
        //     }
        // });
    </script>




    <?php

        include "Footer/footer.html";

    ?>
</body>
</html>