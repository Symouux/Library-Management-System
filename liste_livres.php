<?php
session_start();
require 'connected.php';
include "header/header.html";


if (!$_SESSION['ROLE']) {
    header('Location: login.php');
    exit();
}



$categoriesQuery = $pdo->query("SELECT DISTINCT categorie FROM livres ORDER BY categorie");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_COLUMN);


$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';


$sql = "SELECT * FROM livres WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (titre LIKE ? OR auteur LIKE ? OR description LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($selectedCategory)) {
    $sql .= " AND categorie = ?";
    $params[] = $selectedCategory;
}

$sql .= " ORDER BY titre";





$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des livres - MonShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/liste_livres.css">
    <link rel="stylesheet" href="Footer/footer.css">
</head>
<body>
    <div class="background-wrapper"></div>

    <main class="main-content">
        <h2 class="page-title">Liste des livres</h2>
        
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
        
        <?php if ($_SESSION['ROLE'] === 'admin'): ?>
            <a href="ajout_livre.php" class="btn btn-add">
                <i class="fas fa-plus"></i> Ajouter un nouveau livre
            </a>
        <?php endif; ?>
        
        <div class="filters-container">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Rechercher un livre..." value="<?= htmlspecialchars($search) ?>">
            </form>
            
            <form method="GET" class="category-filter">
                <select name="category" onchange="this.form.submit()">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $selectedCategory === $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            </form>
            
            <a href="liste_livres.php" class="reset-btn">
                <i class="fas fa-redo"></i> Réinitialiser
            </a>
        </div>
        
        <div class="books-grid">
            <?php if (empty($livres)): ?>
                <div class="no-books">
                    <i class="fas fa-book-open"></i>
                    <p>Aucun livre trouvé</p>
                    <?php if (!empty($search) || !empty($selectedCategory)): ?>
                        <p>Essayez de modifier vos critères de recherche</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($livres as $livre): ?>
    <div class="book-card">
        <div class="book-image">
            <img src="<?= $livre['image'] ? htmlspecialchars($livre['image']) : 'https://via.placeholder.com/300x200?text=Couverture+non+disponible' ?>" alt="<?= htmlspecialchars($livre['titre']) ?>">
        </div>
        <div class="book-info">
            <h3 class="book-title"><?= htmlspecialchars($livre['titre']) ?></h3>
            <p class="book-author"><?= htmlspecialchars($livre['auteur']) ?></p>
            <span class="book-category"><?= htmlspecialchars($livre['categorie']) ?></span>
            <p class="book-description"><?= htmlspecialchars($livre['description']) ?></p>

            <div class="book-stock">
                <span class="stock-info <?= $livre['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                    <?= $livre['stock'] > 0 ? 'En stock ('.$livre['stock'].')' : 'Rupture de stock' ?>
                </span>

                <?php if ($_SESSION['ROLE'] === 'admin'): ?>
                    <div class="action-buttons">
                        <a href="modifier_livres.php?id=<?= $livre['id'] ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="supprimer_livres.php?id=<?= $livre['id'] ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <?php if ($livre['stock'] > 0): ?>
                        <a href="reservation.php?id_livre=<?= $livre['id'] ?>" class="btn btn-reserve">
                            <i class="fas fa-calendar-plus"></i> Réserver
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

            <?php endif; ?>
        </div>

    </main>

    



    <script>
        // // Afficher/masquer le champ nouvelle catégorie
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