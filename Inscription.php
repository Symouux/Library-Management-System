
<?php 
require 'connected.php';
include "header/header.html";


$error_username = '';
$error_email = '';
$error_password = '';
$success_message = '';

$message = '';

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error_username = 'Nom d\'utilisateur invalide';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = 'Email invalide';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error_password = 'Mot de passe faible. Il doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre';
    } else {
        
        $st = $pdo->prepare('SELECT * FROM utilisateurs WHERE nom = ? OR email = ?');
        $st->execute([$username, $email]);

        if ($st->rowCount() > 0) {
            $error_username = 'Nom d\'utilisateur ou email déjà utilisé';
        } else {
            $passHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (:username, :email, :pass, "lecteur")');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':pass', $passHash);

            if ($stmt->execute()) {
                $success_message = 'Inscription réussie';
            } else {
                $error_message = 'Erreur lors de l\'inscription';
            }
        }
    }
}
?>








<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonShop - Boutique en ligne</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/Inscription.css">
    <link rel="stylesheet" href="Footer/footer.css">
    <style>
        /* .message {
    text-align: center;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    font-weight: bold;
}
.success {
    color: green;
}
.error {
    color: red;
} */

    /* .message.error {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    } */

 .message {
    padding: 12px 18px;
    border-radius: 5px;
    font-size: 0.80em;
    margin: 15px 0;
    position: relative;
    padding-left: 40px;
    font-weight: 500;
}

.message::before {
    position: absolute;
    left: 15px;
    top: 12px;
    font-size: 1.2em;
}

.message.success {
    background-color: #e6f9ec;
    color: #2e7d32;
    border-left: 4px solid #2e7d32;
}

.message.success::before {
    content: "✅";
}

.message.error {
    background-color: #ffe6e6;
    color: #cc0000;
    border-left: 4px solid #cc0000;
}

.message.error::before {
    content: "❌";
}

.input-error {
    border: 1px solid red !important;
    background-color: #ffe6e6;
    outline: none;
}







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

    <main>
        <div class="form-container">
            
            <?php if (!empty($success_message)): ?>
                <div class="message success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="message error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <div class="form-header">
                <h1>Create Account</h1>
                <p>Join our community today</p>
            </div>
            
            <form action="Inscription.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    <?php if ($error_username != ''): ?>
                        <p class="message error"><?= htmlspecialchars($error_username) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" placeholder="your@email.com" required>
                    <?php if ($error_email != ''): ?>
                        <p class="message error"><?= htmlspecialchars($error_email) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <span class="password-toggle">👁️</span>
                    <?php if ($error_password != ''): ?>
                        <p class="message error"><?= htmlspecialchars($error_password) ?></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="submit-btn" name="submit">Register Now</button>
                
                <div class="form-footer">
                    Already have an account? <a href="login.php">Sign in</a>
                </div>
            </form>
        </div>
    </main>

    <script src="js/inscription.js"></script>




    <?php

        include "Footer/footer.html";

    ?>
</body>
</html>