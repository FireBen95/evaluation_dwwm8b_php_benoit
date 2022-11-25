<?php 
    /* Connexion a la base MYSQL */ 
    $dsn_db = 'mysql:dbname=dwwm8b_livre;host=127.0.0.1';
    $user_db = 'root';
    $password_db = '';

    try {
        $db = new PDO($dsn_db, $user_db, $password_db);
    } 
    catch (\PDOException $th) 
    {
        die("Error:" . $th->getMessage());
    }

?>