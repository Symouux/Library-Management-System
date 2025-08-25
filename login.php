<?php 
session_start();
require 'connected.php';
include "header/header.html";



$error_email = '';
$error_password = '';
$success_message = '';
$message = '';


$email_value = isset($_POST['email']) ? $_POST['email'] : (isset($_COOKIE['email']) ? $_COOKIE['email'] : '');
$password_value = isset($_POST['password']) ? $_POST['password'] : (isset($_COOKIE['password']) ? $_COOKIE['password'] : '');

// $password_value = isset($_POST['password']) ? $_POST['password'] : '';




if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); 


    if ($remember) {
        setcookie('email', $email, time() + (86400 * 30), "/"); 
        setcookie('password', $password, time() + (86400 * 30), "/"); 
    } else {
        setcookie('email', '', time() - 3600, "/"); 
        setcookie('password', '', time() - 3600, "/");
    }




    
    if(empty($email)){
        $error_email = "Veuillez saisir votre adresse e-mail.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = "Adresse e-mail invalide.";
    }

    
    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if(empty($password)){
        $error_password = "Veuillez saisir votre mot de passe.";
    } elseif (!preg_match($password_pattern, $password)) {
        $error_password = "Le mot de passe doit contenir au moins 8 caractères, incluant une majuscule, une minuscule, un chiffre et un symbole.";
    }


    if($error_email === '' && $error_password === ''){
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);





    
    
    if($row = $stmt->fetch()){
        if(password_verify($password, $row['mot_de_passe'])){
            $_SESSION['ROLE'] = $row['role'];
            $_SESSION['username'] = $row["nom"];
            $_SESSION['user_id'] = $row['id']; 

            

            header('Location: Accueil.php');
            exit;
        } else {
             $error_password = "Mot de passe incorrect.";
        }
    } else {
        $error_email = "Adresse e-mail introuvable.";
    }
    }

}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonShop - Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/login.css">
    <link rel="stylesheet" href="Footer/footer.css">
    <style>
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
    <div class="background-wrapper animate"></div>
    <main>
        <div class="form-container">
            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>
            
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" placeholder="your@email.com" value="<?php echo htmlspecialchars($email_value); ?>"  required  <?php if ($error_email != '') echo 'class="input-error"'; ?>>

                    <?php if ($error_email != ''): ?>
                        <p id="emailError" class="message error"><?= htmlspecialchars($error_email) ?></p>
                    <?php else: ?>
                        <p class="message error" style="display:none;"></p>
                    <?php endif; ?>


                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required <?php if ($error_password != '') echo 'class="input-error"'; ?> value="<?php echo htmlspecialchars($password_value); ?>">
                    <span class="password-toggle">👁️</span>

                    <?php if ($error_password != ''): ?>
                        <p id="passwordError" class="message error"><?= htmlspecialchars($error_password) ?></p>
                    <?php else: ?>
                        <p class="message error" style="display:none;"></p>
                    <?php endif; ?>


                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" <?php if(isset($_COOKIE['email'])) echo 'checked' ?>>
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#">Forgot password?</a>
                </div>
                
                <button type="submit" class="submit-btn" name="submit">Sign In</button>
                
                <div class="form-footer">
                    Don't have an account? <a href="Inscription.php">Sign up</a>
                </div>
            </form>
        </div>
    </main>

    <script src="js/login.js"></script>


    <?php

        include "Footer/footer.html";

    ?>
</body>
</html>