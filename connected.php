<?php

$dsn = 'mysql:host=localhost;dbname=bibliotheque';
$user = 'root';
$pass = '1111';

try{
    $pdo =  new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    // echo 'Connection Autoriser !';
}catch (PDOException $e){
    die ('Error : '. $e->getMessage());
}

