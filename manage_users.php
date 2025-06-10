<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn() || $_SESSION["loggedInUser"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$message = "";
$currentAdminId = $_SESSION["loggedInUser"]["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_role"])) {
    $userIdToChange = (int)$_POST["user_id"];
    $newRole = $_POST["new_role"];

    if ($userIdToChange === $currentAdminId) {
        $message = "<p style='color: red;'>Nemůžete změnit roli sami sobě.</p>";
    } elseif ($newRole !== 'user' && $newRole !== 'admin') {
        $message = "<p style='color: red;'>Neplatná role.</p>";
    } else {
        $sql = "UPDATE users SET role = '$newRole' WHERE id = $userIdToChange";
        
        if ($db->query($sql)) {
            $message = "<p style='color: #66ff66;'>Role uživatele byla úspěšně změněna.</p>";
        } else {
            $message = "<p style='color: red;'>Chyba při změně role.</p>";
        }
    }
}

$usersResult = $db->query("SELECT id, email, role FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa uživatelů – Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_admin_panel.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
</head>
<body>
<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_panel.php">Admin Panel</a>
        <a href="delete_products.php">Správa produktů</a>
        <a href="add_product.php">Přidat nový produkt</a>
        <a href="manage_categories.php">Správa kategorií</a>
        <a href="manage_users.php">Správa uživatelů</a>
        <a href="#">Správa objednávek</a>
        <a href="index.php">Zpět na web</a>
        <a href="logout.php">Odhlásit se</a>
    </div>
    <div class="admin-main">
        <div class="admin-header">
            <h1>Správa uživatelů</h1>
        </div>

        <?php echo $message; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Aktuální role</th>
                    <th>Změnit roli</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usersResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $user["id"]; ?></td>
                        <td><?php echo htmlentities($user["email"], ENT_QUOTES, "UTF-8"); ?></td>
                        <td><?php echo htmlentities($user["role"], ENT_QUOTES, "UTF-8"); ?></td>
                        <td>
                            <?php if ($user["id"] === $currentAdminId) { ?>
                                <span>(Vy)</span>
                            <?php } else { ?>
                                <form method="post">
                                    <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
                                    <input type="hidden" name="change_role" value="1">
                                    <select name="new_role">
                                        <option value="user" <?php if ($user["role"] === 'user') {
                                                                    echo 'selected';
                                                                } ?>>user</option>
                                        <option value="admin" <?php if ($user["role"] === 'admin') {
                                                                    echo 'selected';
                                                                } ?>>admin</option>
                                    </select>
                                    <button type="submit">Uložit</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>
</body>
</html>