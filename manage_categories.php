<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn() || $_SESSION["loggedInUser"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$message = "";
$edit = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
    $name = trim($_POST["name"] ?? "");
    $desc = trim($_POST["description"] ?? "");

    switch (true) {
        case isset($_POST["add_category"]) && $name !== "":
            $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $desc);
            $stmt->execute();
            $message = "<p style='color:#66ff66;'>Kategorie přidána.</p>";
            break;

        case isset($_POST["update_category"]) && $name !== "":
            $stmt = $db->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $desc, $id);
            $stmt->execute();
            $message = "<p style='color:#66ff66;'>Kategorie upravena.</p>";
            break;

        case isset($_POST["delete_category"]):
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $message = "<p style='color:#66ff66;'>Kategorie smazána.</p>";
            break;
    }
}

if (isset($_GET["edit_id"])) {
    $eid = (int)$_GET["edit_id"];
    $res = $db->query("SELECT * FROM categories WHERE id = $eid");
    if ($res && $res->num_rows > 0) {
        $edit = $res->fetch_assoc();
    }
}

$categoriesResult = $db->query("SELECT * FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa kategorií – Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_admin_panel.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
    <style>
    table {
        width: 100%;
        background-color: #1c1c1c;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
        border-bottom: 1px solid #333;
        text-align: left;
        color: #eee;
    }

    th {
        background-color: #2a2a2a;
    }

    button.delete {
        background-color: red;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    button.delete:hover {
        background-color: darkred;
    }

    a.edit-button {
        display: inline-block;
        background-color: #2196F3;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        margin-right: 8px;
    }

    a.edit-button:hover {
        background-color: #0b7dda;
    }

    .form-box {
        max-width: 600px;
        background-color: #1c1c1c;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 40px;
    }

    .form-box label {
        display: block;
        margin-top: 15px;
        color: #ccc;
        font-size: 14px;
    }

    .form-box input[type="text"],
    .form-box textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #333;
        border-radius: 4px;
        background-color: #2a2a2a;
        color: #eee;
        box-sizing: border-box;
    }

    .form-box textarea {
        resize: vertical;
    }

    .form-box button,
    .form-box a.cancel {
        margin-top: 20px;
        display: inline-block;
        background-color: #fff;
        color: #000;
        padding: 10px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
    }

    .form-box a.cancel {
        background-color: #444;
        color: #eee;
        margin-left: 10px;
    }

    .form-box button:hover {
        background-color: #ddd;
    }
</style>

</head>
<body>
<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_panel.php">Admin Panel</a>
        <a href="manage_products.php">Správa produktů</a>
        <a href="add_product.php">Přidat nový produkt</a>
        <a href="manage_categories.php">Správa kategorií</a>
        <a href="manage_users.php">Správa uživatelů</a>
        <a href="#">Správa objednávek</a>
        <a href="index.php">Zpět na web</a>
        <a href="logout.php">Odhlásit se</a>
    </div>
    <div class="admin-main">
        <div class="admin-header">
            <h1>Správa kategorií</h1>
        </div>

        <?php echo $message; ?>

        <form method="post" class="form-box">
            <?php if ($edit) {
                echo '<input type="hidden" name="id" value="' . $edit['id'] . '">';
            } ?>

            <label>Název:</label>
            <input type="text" name="name" required value="<?php echo $edit ? htmlentities($edit['name']) : ''; ?>">

            <label>Popis:</label>
            <textarea name="description" rows="3"><?php echo $edit ? htmlentities($edit['description']) : ''; ?></textarea>

            <button type="submit" name="<?php echo $edit ? 'update_category' : 'add_category'; ?>">
                <?php echo $edit ? 'Uložit změny' : 'Přidat kategorii'; ?>
            </button>
            <?php if ($edit) {
                echo '<a href="manage_categories.php">Zrušit</a>';
            } ?>
        </form>

        <h2>Existující kategorie</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Název</th>
                    <th>Popis</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = $categoriesResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo htmlentities($category['name']); ?></td>
                        <td><?php echo htmlentities($category['description']); ?></td>
                        <td>
                        <a href="manage_categories.php?edit_id=<?php echo $category['id']; ?>" class="edit-button">Upravit</a>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                <button type="submit" name="delete_category" class="delete">Smazat</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
