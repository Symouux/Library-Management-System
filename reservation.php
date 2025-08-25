<?php
session_start();
require 'connected.php';
include "header/header.html";

$message = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=login_required");
    exit;
}

if ($_SESSION['ROLE'] !== 'lecteur') {
    header("Location: liste_livres.php");
    exit;
}

if (isset($_GET['id_livre']) && is_numeric($_GET['id_livre'])) {
    $id_livre = intval($_GET['id_livre']);
    $id_utilisateur = $_SESSION['user_id'];
    $date = date('Y-m-d');

    $livreStmt = $pdo->prepare("SELECT stock, disponible, titre FROM livres WHERE id = ?");
    $livreStmt->execute([$id_livre]);
    $livre = $livreStmt->fetch(PDO::FETCH_ASSOC);

    if ($livre) {
        if ($livre['stock'] > 0 && $livre['disponible'] == 1) {

            // Vérifier si une réservation "en attente" existe déjà
            $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id_utilisateur = ? AND id_livre = ? AND statut IN ('en attente', 'confirmée')");
            $stmt->execute([$id_utilisateur, $id_livre]);

            if ($stmt->rowCount() === 0) {
                $insert = $pdo->prepare("INSERT INTO reservations (id_utilisateur, id_livre, date_reservation) VALUES (?, ?, ?)");
                $insert->execute([$id_utilisateur, $id_livre, $date]);

                $message = "✅ Votre réservation pour \"".htmlspecialchars($livre['titre'])."\" a été enregistrée avec succès.";
            } else {
                $message = "⚠️ Vous avez déjà une réservation en attente pour ce livre.";
            }
        } else {
            $message = "❌ Ce livre n'est plus disponible pour le moment.";
        }
    } else {
        $message = "❌ Livre introuvable.";
    }
} else {
    $message = "✅ Votre demande a été prise en compte.";
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de livre - MonShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/reservation.css">
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
    </header>   -->

    <main class="main-content">
        <div class="reservation-card">
            <div class="reservation-icon">
                <i class="fas fa-book"></i>
            </div>
            <h1 class="reservation-title">Réservation de livre</h1>
            
            <div class="reservation-message <?php 
                if (strpos($message, '✅') !== false) echo 'message-success';
                elseif (strpos($message, '⚠️') !== false) echo 'message-warning';
                else echo 'message-error';
            ?>">
                <?= $message ?>
            </div>
            
            <a href="liste_livres.php" class="btn">
                <i class="fas fa-arrow-left"></i> Retour aux livres
            </a>
        </div>
    </main>



    <?php

        include "Footer/footer.html";

    ?>
</body>
</html>