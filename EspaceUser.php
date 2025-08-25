



<?php
session_start();
include "header/header.html";

if(!isset($_SESSION['ROLE'])) {
    header('Location: Login.php');
    exit;
}


$username = $_SESSION['username'];
$role = $_SESSION['ROLE'];
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonShop - Boutique en ligne</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/EspaceUser.css">
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
    <div class="content ">
    <h1>Hello, <span><?php echo htmlspecialchars($username); ?></span></h1>
    <p>You Are  <span class="S"><?php echo htmlspecialchars($role); ?></span></p>
    
    <a class="lien" href="Deconnected.php">Déconnexion</a>
    </div>


    
    ?>
</body>
</html>