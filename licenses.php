<?php
require_once 'db_connection.php';

// CRUD-Operationen für Lizenzen

// Create: Neue Lizenz hinzufügen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['license_key']) && !empty($_POST['license_key']) && isset($_POST['license_data']) && !empty($_POST['license_data']) && isset($_POST['valid_until']) && !empty($_POST['valid_until'])) {
        $license_key = $_POST['license_key'];
        $license_data = $_POST['license_data'];
        $valid_until = $_POST['valid_until'];
        $stmt = $conn->prepare('INSERT INTO licenses (license_key, license_data, valid_until, created, updated) VALUES (?, ?, ?, NOW(), NULL)');
        $stmt->execute([$license_key, $license_data, $valid_until]);
        $license_id = $conn->lastInsertId();

        // Verbindung zwischen Lizenz und Benutzer hinzufügen
        if (isset($_POST['user_id']) && isset($license_id)) {
            $user_id = $_POST['user_id'];
            $stmt = $conn->prepare('INSERT INTO users_licenses (user_id, license_id) VALUES (?, ?)');
            $stmt->execute([$user_id, $license_id]);
        }

        header("Location: licenses.php");
        exit();
    }
}

// Read: Lizenzen abrufen
$licenses = $conn->query('SELECT * FROM licenses ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

// Update: Lizenz bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_license_id'])) {
    $id = $_POST['edit_license_id'];
    $license_key = $_POST['edit_license_key'];
    $license_data = $_POST['edit_license_data'];
    $valid_until = $_POST['edit_valid_until'];
    $stmt = $conn->prepare('UPDATE licenses SET license_key = ?, license_data = ?, valid_until = ?, updated = NOW() WHERE id = ?');
    $stmt->execute([$license_key, $license_data, $valid_until, $id]);
    header("Location: licenses.php");
    exit();
}

// Delete: Lizenz löschen
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_license_id'])) {
    $id = $_GET['delete_license_id'];

    // Verbindung zwischen Lizenz und Benutzern entfernen
    $stmt = $conn->prepare('DELETE FROM users_licenses WHERE license_id = ?');
    $stmt->execute([$id]);

    // Lizenz löschen
    $stmt = $conn->prepare('DELETE FROM licenses WHERE id = ?');
    $stmt->execute([$id]);
    header("Location: licenses.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licenses</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
    <form action="" method="post" id="form_license_add"></form>
    <table>
        <tr>
            <td><label for="user_id">User:</label></td>
            <td>
                <select id="user_id" name="user_id">
                    <?php
                    $users = $conn->query('SELECT * FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($users as $user) : ?>
                        <option value="<?= $user['id'] ?>">
                            <?= $user['api_key'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="license_key">License Key:</label></td>
            <td><input type="text" id="license_key" name="license_key" form="form_license_add" required></td>
        </tr>
        <tr>
            <td><label for="license_data">License Data:</label></td>
            <td><textarea id="license_data" name="license_data" form="form_license_add" required></textarea></td>
        </tr>
        <tr>
            <td><label for="valid_until">Valid Until:</label></td>
            <td><input type="datetime-local" id="valid_until" name="valid_until" form="form_license_add" required></td>
        </tr>
        <tr>
            <td><button type="submit" form="form_license_add">Add License</button></td>
            <td></td>
        </tr>
    </table>

    <h1>Licenses</h1>
    <form action="" method="post" id="form_license_edit"></form>
    <table>
        <tr>
            <th>ID</th>
            <th>KEY</th>
            <th>DATA</th>
            <th>VALID UNTIL</th>
            <th></th>
        </tr>
        <?php foreach ($licenses as $license) : ?>
            <tr>
                <td><input type="text" name="edit_license_id" size="2" value="<?= $license['id'] ?>" form="form_license_edit" readonly></td>
                <td><input type="text" name="edit_license_key" value="<?= $license['license_key'] ?>" form="form_license_edit"></td>
                <td><textarea name="edit_license_data" form="form_license_edit"><?= $license['license_data'] ?></textarea></td>
                <td><input type="datetime-local" name="edit_valid_until" value="<?= date('Y-m-d\TH:i', strtotime($license['valid_until'])) ?>" form="form_license_edit"></td>
                <td><button type="submit" form="form_license_edit">Edit</button> <a href="licenses.php?delete_license_id=<?= $license['id'] ?>"><button>Delete</button></a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>