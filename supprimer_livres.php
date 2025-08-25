<?php
session_start();
require 'connected.php';


if ($_SESSION['ROLE'] !== 'admin') {
    $_SESSION['erreur'] = "Vous n'avez pas les droits pour effectuer cette action";
    header('Location: liste_livres.php');
    exit();
}


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




$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_livre = ?");
$stmt->execute([$id]);
$nbEmprunts = $stmt->fetchColumn();

if ($nbEmprunts > 0) {
    $_SESSION['erreur'] = "❌Impossible de supprimer ce livre car il est encore emprunté.";
    header('Location: liste_livres.php');
    exit();
}




$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE id_livre = ?");
$stmt->execute([$id]);
$nbReservations = $stmt->fetchColumn();

if ($nbReservations > 0) {
    $_SESSION['erreur'] = "❌Impossible de supprimer ce livre car il est encore réservé.";
    header('Location: liste_livres.php');
    exit();
}



try {
    // Supprimer l'image associée si elle existe
    // if (!empty($livre['image']) && file_exists($livre['image'])) {
    //     unlink($livre['image']);
    // }

    // Supprimer le livre
    $stmt = $pdo->prepare("DELETE FROM livres WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Le livre a été supprimé avec succès";
} catch (PDOException $e) {
    $_SESSION['erreur'] = "Erreur lors de la suppression du livre : " . $e->getMessage();
}

header('Location: liste_livres.php');
exit();
?>
