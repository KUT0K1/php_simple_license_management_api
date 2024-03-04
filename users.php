<?php
// Datenbankverbindung
require_once 'db_connection.php';

// CRUD-Operationen für Benutzer

// Create: Neuen Benutzer hinzufügen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['api_key']) && !empty($_POST['api_key'])) {
        $api_key = $_POST['api_key'];
        $stmt = $conn->prepare('INSERT INTO users (api_key) VALUES (:api_key)');
        $stmt->execute([':api_key' => $api_key]);
        header("Location: users.php");
        exit();
    }
}

// Read: Benutzer abrufen
$users = $conn->query('SELECT * FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

// Update: benutzer bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $id = $_POST['edit_user_id'];
    $api_key = $_POST['edit_api_key'];
    $stmt = $conn->prepare('UPDATE users SET api_key = :api_key WHERE id = :id');
    $stmt->execute([':api_key' => $api_key, ':id' => $id]);
    header("Location: users.php");
    exit();
}

// Delete: Benutzer löschen
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_user_id'])) {
    $id = $_GET['delete_user_id'];
    $stmt = $conn->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
    <form action="" method="post" id="form_user_add"></form>
    <table>
        <tr>
            <td><label for="api_key">API Key:</label></td>
            <td><input type="text" id="api_key" name="api_key" form="form_user_add"></td>
        </tr>
        <tr>
            <td><button type="submit" form="form_user_add">Add User</button></td>
            <td></td>
        </tr>
    </table>

    <h1>Users</h1>
    <form action="" method="post" id="form_user_edit"></form>
    <table>
        <tr>
            <th>ID</th>
            <th>KEY</th>
            <th></th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><input type="text" name="edit_user_id" size="2" value="<?= $user['id'] ?>" form="form_user_edit" readonly></td>
                <td><input type="text" name="edit_api_key" value="<?= $user['api_key'] ?>" form="form_user_edit"></td>
                <td><button type="submit" form="form_user_edit">Edit</button> <a href="users.php?delete_user_id=<?= $user['id'] ?>"><button>Delete</button></a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>