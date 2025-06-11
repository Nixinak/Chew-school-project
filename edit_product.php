<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn() || $_SESSION["loggedInUser"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_products.php");
    exit;
}

$productId = (int)$_GET['id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $db->real_escape_string($_POST['name']);
    $description = $db->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];
    $categoryId = (int)$_POST['category'];

    $sql = "UPDATE products SET name = '$name', description = '$description', price = $price, category_id = $categoryId WHERE id = $productId";

    if ($db->query($sql)) {
        $message = "<p style='color: #66ff66;'>Produkt byl úspěšně aktualizován.</p>";
    } else {
        $message = "<p style='color: red;'>Chyba při aktualizaci produktu: " . $db->error . "</p>";
    }
}

$productResult = $db->query("SELECT * FROM products WHERE id = $productId");
if ($productResult->num_rows === 0) {
    header("Location: manage_products.php");
    exit;
}
$product = $productResult->fetch_assoc();

$categories = $db->query("SELECT id, name FROM categories");

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Upravit produkt – Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_admin_panel.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
    <style>
        .form-container { max-width: 700px; background-color: #1c1c1c; padding: 30px; border-radius: 8px; }
        .form-container label { display: block; margin-top: 15px; color: #ccc; }
        .form-container input, .form-container textarea, .form-container select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #333; border-radius: 4px; background-color: #2a2a2a; color: #eee; box-sizing: border-box; }
        .form-container button { margin-top: 20px; background-color: #fff; color: #000; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .current-image { margin-top: 15px; }
        .current-image img { max-width: 150px; border-radius: 4px; }
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
                <h1>Upravit produkt: <?php echo htmlentities($product['name']); ?></h1>
            </div>

            <?php echo $message; ?>

            <div class="form-container">
                <form method="post">
                    <label for="name">Název produktu:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlentities($product['name']); ?>" required>

                    <label for="description">Popis:</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlentities($product['description']); ?></textarea>

                    <label for="price">Cena (Kč):</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>

                    <label for="category">Kategorie:</label>
                    <select id="category" name="category" required>
                        <?php while ($cat = $categories->fetch_assoc()) {
                            $selected = ($cat['id'] == $product['category_id']) ? 'selected' : '';
                            echo "<option value='{$cat['id']}' {$selected}>" . htmlentities($cat['name']) . "</option>";
                        } ?>
                    </select>

                    <div class="current-image">
                        <label>Aktuální obrázek:</label>
                        <img src="/ch/<?php echo htmlentities($product['image_url']); ?>" alt="Aktuální obrázek">
                    </div>
                    
                    <button type="submit">Uložit změny</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>