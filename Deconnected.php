<?php 
    session_start();
    if(isset($_SESSION['ROLE'])){
        session_destroy();
        unset($_SESSION);
        header('Location: Login.php');
    }

?>

