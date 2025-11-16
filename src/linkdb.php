<?php
//Banco de dados SQLITE

$database_path = '/var/www/database/icms_verde.sqlite';
$pdo = new PDO("sqlite:" . $database_path);

?>