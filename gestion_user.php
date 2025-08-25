<?php
session_start();
require_once 'connected.php';
include "header/header.html";

if ($_SESSION['ROLE'] !== 'admin') {
    header('Location: liste_livres.php');
    exit();
}



$stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY role, nom");
$utilisateurs = $stmt->fetchAll();


// Changer le rôle d'un utilisateur
if (isset($_GET['changer_role']) && isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    if ($userId === $_SESSION['user_id']) {
        $_SESSION['erreur'] = "⚠️ Vous ne pouvez pas modifier votre propre rôle.";
        header('Location: gestion_user.php');
        exit();
    }
    
    $stmt = $pdo->prepare("UPDATE utilisateurs SET role = IF(role = 'admin', 'lecteur', 'admin') WHERE id = ?");
    if ($stmt->execute([$userId])) {
        $_SESSION['success'] = "✅ Rôle utilisateur modifié avec succès.";
    } else {
        $_SESSION['erreur'] = "❌ Erreur lors de la modification du rôle.";
    }
    header('Location: gestion_user.php');
    exit();
}

// Supprimer un utilisateur
if (isset($_GET['supprimer']) && isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    
    if ($userId === $_SESSION['user_id']) {
        $_SESSION['erreur'] = "⚠️ Vous ne pouvez pas supprimer votre propre compte.";
        header('Location: gestion_user.php');
        exit();
    }

    try {
        // emprunts actifs
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_utilisateur = ? AND date_retour IS NULL");
        $stmt->execute([$userId]);
        $empruntsEnCours = $stmt->fetchColumn();

        // réservations en attente
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE id_utilisateur = ? AND statut = 'en attente'");
        $stmt->execute([$userId]);
        $reservationsEnAttente = $stmt->fetchColumn();

        
        if ($empruntsEnCours > 0 || $reservationsEnAttente > 0) {
            $_SESSION['erreur'] = "❌ Impossible de supprimer cet utilisateur car il a des emprunts ou des réservations en cours.";
        } else {
            
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['success'] = "✅ Utilisateur supprimé avec succès.";
        }
    } catch (PDOException $e) {
        
        $_SESSION['erreur'] = "❌ Une erreur est survenue lors de la suppression de l'utilisateur.";
    }

    header('Location: gestion_user.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - MonShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/gestion_user.css">
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
        <h2 class="page-title">Gestion des utilisateurs</h2>
        
        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['erreur']) ?>
                <?php unset($_SESSION['erreur']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="role-<?= $user['role'] ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="gestion_user.php?changer_role=1&id=<?= $user['id'] ?>" 
                                   class="action-btn btn-change">
                                    <i class="fas fa-sync-alt"></i> Changer rôle
                                </a>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <a href="gestion_user.php?supprimer=1&id=<?= $user['id'] ?>" 
                                       class="action-btn btn-delete"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <a href="dashboard_admin.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>Retour au tableau de bord
        </a>
    </main>


    <?php

        include "Footer/footer.html";

    ?>
</body>
</html>