<?php
session_start();
require 'connected.php';
include "header/header.html";

if ($_SESSION['ROLE'] !== 'admin') {
    header('Location: liste_livres.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'ajout_livre':
            header('Location: ajout_livre.php');
            exit();
            case 'liste_livre':
                header('Location: liste_livres.php');
                exit();
            case 'modifier':
                header('Location: modifier_livres.php');
                exit();
        case 'supprimer_livre':
            header('Location: liste_livres.php?mode=suppression');
            exit();
        case 'gerer_utilisateurs':
            header('Location: gestion_user.php');
            exit();
        case 'voir_statistiques':
            header('Location: statistiques.php');
            exit();
        default:
            
            header('Location: dashboard_admin.php');
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - MonShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/dashbord_admin.css">
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

    <main class="admin-dashboard main-content">
        <h2 class="dashboard-title">Tableau de bord Administrateur</h2>
        
        <form method="post" class="action-form" action="dashboard_admin.php">
            <select name="action" required>
                <option value="">-- Choisir une action --</option>
                <option value="ajout_livre">Ajouter un livre</option>
                <option value="liste_livre">liste des livre</option>
                <option value="modifier">Modifier un livre</option>
                <option value="supprimer_livre">Supprimer un livre</option>
                <option value="gerer_utilisateurs">Gérer les utilisateurs</option>
                <option value="voir_statistiques">Voir les statistiques</option>
            </select>
            <button type="submit">Exécuter</button>
        </form>
    </main>


    <?php

        include "Footer/footer.html";

    ?>
</body>
</html>