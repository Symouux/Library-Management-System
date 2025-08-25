<?php
session_start();
require 'connected.php';
include "header/header.html";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_utilisateur = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['reservation_id'])) {
        $reservation_id = (int)$_POST['reservation_id'];
        $new_status = '';
        
        if ($_POST['action'] === 'confirm') {
            $new_status = 'confirmée';
        } elseif ($_POST['action'] === 'cancel') {
            $new_status = 'annulée';
        }

        if (!empty($new_status)) {
            try {
                $pdo->beginTransaction();

                
                $stmt = $pdo->prepare("SELECT id_livre FROM reservations WHERE id = ? AND id_utilisateur = ?");
                $stmt->execute([$reservation_id, $id_utilisateur]);
                $reservation = $stmt->fetch();
                $id_livre = $reservation['id_livre'];

               
                $stmt = $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ? AND id_utilisateur = ?");
                $stmt->execute([$new_status, $reservation_id, $id_utilisateur]);

                if ($new_status === 'confirmée') {
                    $date_retour = date('Y-m-d H:i:s', strtotime("+30 days"));

                    
                    $stmt = $pdo->prepare("INSERT INTO emprunts (id_utilisateur, id_livre, date_emprunt, date_retour) VALUES (?, ?, NOW(), ?)");
                    $stmt->execute([$id_utilisateur, $id_livre, $date_retour]);

                    
                    $stmt = $pdo->prepare("UPDATE livres SET stock = stock - 1 WHERE id = ?");
                    $stmt->execute([$id_livre]);

                    
                    $stmt = $pdo->prepare("UPDATE livres SET disponible = CASE WHEN stock > 0 THEN 1 ELSE 0 END WHERE id = ?");
                    $stmt->execute([$id_livre]);
                } 
                // elseif ($new_status === 'annulée') {
                
                //     $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ? AND id_utilisateur = ?");
                //     $stmt->execute([$reservation_id, $id_utilisateur]);
                // }


                $pdo->commit();
                $_SESSION['success'] = "✅Opération réussie!";
            } catch (PDOException $e) {
                $pdo->rollBack();
                $_SESSION['error'] = "Erreur: " . $e->getMessage();
            }

            header("Location: mes_emprunts.php");
            exit;
        }
    }
}


$stmt = $pdo->prepare("
    SELECT r.id, r.id_livre, r.date_reservation, r.statut, 
           l.titre, l.auteur, l.image, l.categorie, l.description
    FROM reservations r
    JOIN livres l ON r.id_livre = l.id
    WHERE r.id_utilisateur = ?
    ORDER BY r.date_reservation DESC
");
$stmt->execute([$id_utilisateur]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - Bibliothèque</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/mes_emprunts.css">
    <link rel="stylesheet" href="Footer/footer.css">
</head>
<body>
    <div class="background-wrapper"></div>


    <main class="main-content">
        <div class="container">
            <h1 class="page-title">Mes Réservations</h1>

            <!-- Affichage des alertes de succès ou d'erreur -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error'] ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="reservations-grid">
                <!-- Si aucune réservation, afficher un message -->
                <?php if (empty($reservations)): ?>
                    <div class="no-reservations">
                        <i class="fas fa-calendar-times"></i>
                        <p>Aucune réservation trouvée</p>
                        <a href="liste_livres.php" class="btn-action" style="background: var(--primary-color); color: white; text-decoration: none;">
                            <i class="fas fa-book"></i> Explorer les livres
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Affichage des cartes de réservation -->
                    <?php foreach ($reservations as $r): ?>
                        <div class="reservation-card">
                            <div class="reservation-image">
                                <img src="<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['titre']) ?>">
                            </div>
                            <div class="reservation-info">
                                <h3 class="reservation-title"><?= htmlspecialchars($r['titre']) ?></h3>
                                <p class="reservation-author"><?= htmlspecialchars($r['auteur']) ?></p>
                                <span class="reservation-category"><?= htmlspecialchars($r['categorie']) ?></span>
                                <p class="reservation-description"><?= htmlspecialchars($r['description']) ?></p>

                                <div class="reservation-details">
                                <span class="reservation-date">
    <i class="far fa-calendar-alt"></i> Réservé le: <?= date('d/m/Y H:i', strtotime($r['date_reservation'])) ?>
</span>
<span class="return-date">
    <i class="far fa-calendar-alt"></i> 
    Retour le: <?= !empty($r['date_retour']) ? date('d/m/Y H:i', strtotime($r['date_retour'])) : 'Non défini' ?>
</span>


                                    <span class="reservation-status status-<?= str_replace('é', 'e', strtolower($r['statut'])) ?>">
                                        <?= htmlspecialchars($r['statut']) ?>
                                    </span>
                                </div>

                                <div class="reservation-actions">
                                    <?php if (strtolower($r['statut']) === 'en attente'): ?>
                                        <form method="post" onsubmit="return confirm('Confirmer cette réservation?')">
                                            <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                            <input type="hidden" name="action" value="confirm">
                                            <button type="submit" class="btn-action btn-confirm">
                                                <i class="fas fa-check"></i> Confirmer
                                            </button>
                                        </form>

                                        <form method="post" onsubmit="return confirm('Annuler cette réservation?')">
                                            <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                            <input type="hidden" name="action" value="cancel">
                                            <button type="submit" class="btn-action btn-cancel">
                                                <i class="fas fa-times"></i> Annuler
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="reservation-status status-<?= str_replace('é', 'e', strtolower($r['statut'])) ?>">
                                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($r['statut']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>




    <?php

        include "Footer/footer.html";

    ?>
</body>
</html>