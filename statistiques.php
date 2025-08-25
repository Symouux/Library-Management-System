<?php
session_start();
require_once 'connected.php';
include "header/header.html";

if ($_SESSION['ROLE'] !== 'admin') {
    header('Location: liste_livres.php');
    exit();
}

// === Statistiques utilisateurs ===
$stmt = $pdo->query("SELECT role, COUNT(*) as total FROM utilisateurs GROUP BY role");
$totalAdmins = $totalLecteurs = 0;
while ($row = $stmt->fetch()) {
    if ($row['role'] === 'admin') $totalAdmins = $row['total'];
    if ($row['role'] === 'lecteur') $totalLecteurs = $row['total'];
}

// === Statistiques livres ===
$totalLivres = $pdo->query("SELECT COUNT(*) FROM livres")->fetchColumn();
$livresDisponibles = $pdo->query("SELECT COUNT(*) FROM livres WHERE disponible = 1")->fetchColumn();

// === Statistiques emprunts ===
$totalEmprunts = $pdo->query("SELECT COUNT(*) FROM emprunts")->fetchColumn();
$empruntsActifs = $pdo->query("SELECT COUNT(*) FROM emprunts WHERE date_retour IS NULL")->fetchColumn();

// === Statistiques réservations ===
$totalReservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$resEnAttente = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'en attente'")->fetchColumn();
$resConfirmees = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'confirmée'")->fetchColumn();
$resAnnulees = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'annulée'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="Style/statistiques.css">
    <link rel="stylesheet" href="Footer/footer.css">
    <style>


        
    </style>
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
    </header>  -->

    <h2 class="page-title"> Statistiques Générales</h2>

<div class="charts-container">
    <div class="chart-box"><canvas id="usersChart"></canvas></div>
    <div class="chart-box"><canvas id="booksChart"></canvas></div>
    <div class="chart-box"><canvas id="empruntsChart"></canvas></div>
    <div class="chart-box"><canvas id="reservationsChart"></canvas></div>
</div>

<a href="dashboard_admin.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>Retour au tableau de bord
        </a>

<script>
    new Chart(document.getElementById('usersChart'), {
        type: 'pie',
        data: {
            labels: ['Admins', 'Lecteurs'],
            datasets: [{
                data: [<?= $totalAdmins ?>, <?= $totalLecteurs ?>],
                backgroundColor: ['#36A2EB', '#FF6384']
            }]
        }
    });

    new Chart(document.getElementById('booksChart'), {
        type: 'bar',
        data: {
            labels: ['Total Livres', 'Disponibles'],
            datasets: [{
                label: 'Livres',
                data: [<?= $totalLivres ?>, <?= $livresDisponibles ?>],
                backgroundColor: ['#4BC0C0', '#9966FF']
            }]
        }
    });

    new Chart(document.getElementById('empruntsChart'), {
        type: 'doughnut',
        data: {
            labels: ['Total Emprunts', 'Actifs'],
            datasets: [{
                data: [<?= $totalEmprunts ?>, <?= $empruntsActifs ?>],
                backgroundColor: ['#FFCE56', '#FF6384']
            }]
        }
    });

    new Chart(document.getElementById('reservationsChart'), {
        type: 'bar',
        data: {
            labels: ['Total', 'En attente', 'Confirmées', 'Annulées'],
            datasets: [{
                label: 'Réservations',
                data: [<?= $totalReservations ?>, <?= $resEnAttente ?>, <?= $resConfirmees ?>, <?= $resAnnulees ?>],
                backgroundColor: ['#36A2EB', '#FFCE56', '#4BC0C0', '#FF6384']
            }]
        }
    });
</script>



    <?php

        include "Footer/footer.html";

    ?>

</body>
</html>
