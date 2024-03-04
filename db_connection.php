<?php
// Datenbankkonfiguration
$servername = "license_management_devcontainer-db-1";
$username = "mariadb";
$password = "mariadb";
$database = "mariadb";

// Verbindung zur Datenbank herstellen
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
