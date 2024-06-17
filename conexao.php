<?php

$host = "localhost"; 
$user = "root";     
$pass = "";          
$bd = "upload";    

// Criando uma nova conexão usando MySQLi
$mysqli = new mysqli($host, $user, $pass, $bd);

// Verificando se houve algum erro na conexão
if ($mysqli->connect_errno) {
    echo "Falha na conexão: " . $mysqli->connect_error;
    exit();
}

?>
