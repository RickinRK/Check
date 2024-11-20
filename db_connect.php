<?php
$host = 'checkdatabase.mysql.database.azure.com';
$port = 3306;
$username = 'check';
$password = 'Henrique3005';
$dbname = 'checkdb';

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
// Remova a linha $conn->close(); daqui para evitar o fechamento precoce
?>