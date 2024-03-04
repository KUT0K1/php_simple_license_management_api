<?php
// Datenbankverbindung
require_once 'db_connection.php';

// Funktion zur Überprüfung der API-Schlüssel
function authenticate($api_key)
{
    // Überprüfe, ob der übergebene API-Schlüssel in der Datenbank vorhanden ist
    global $conn;
    $sql = "SELECT * FROM users WHERE api_key = '$api_key'";
    $result = $conn->query($sql);
    return $result->rowCount() > 0;
}

// Funktion zum Validieren der Lizenzschlüssel
function validate($license_key)
{
    if (!preg_match('/^[a-zA-Z0-9]{16}$/', $license_key)) {
        header('HTTP/1.1 400 Bad Request');
        echo "Invalid license key format";
        exit();
    }
}

// API-Endpunkt zum Abrufen eines Lizenzschlüssels
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api_key'])) {
    // Überprüfe, ob der API-Schlüssel in der Anfrage vorhanden ist
    if (!isset($_GET['api_key'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo "API key is missing";
        exit();
    }

    // Überprüfe die Authentizität des API-Schlüssels
    $api_key = $_GET['api_key'];
    if (!authenticate($api_key)) {
        header('HTTP/1.1 403 Forbidden');
        echo "Invalid API key";
        exit();
    }

    // SQL-Abfrage, um den Lizenzschlüssel aus der Datenbank abzurufen
    $sql = "SELECT licenses.* FROM licenses";
    if (!isset($_GET['license_key'])) {
        $sql .= " JOIN users ON api_key = '$api_key'";
        $sql .= " JOIN users_licenses ON license_id = licenses.id AND user_id = users.id";
        $sql .= " WHERE valid_until IS NULL OR valid_until > NOW()";
        $sql .= " ORDER BY created DESC LIMIT 1";
    } else {
        $license_key = $_GET['license_key'];
        $sql .= " WHERE license_key = '$license_key'";
    }

    $result = $conn->query($sql);
    if ($result->rowCount() > 0) {
        // Lizenzschlüssel gefunden, Daten als JSON ausgeben
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($row, !isset($_GET['pretty']) ? 0 : JSON_PRETTY_PRINT);
    } else {
        // Lizenzschlüssel nicht gefunden
        header('HTTP/1.1 404 Not Found');
        echo "License key not found";
    }
}
