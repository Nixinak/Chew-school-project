<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn() || $_SESSION["loggedInUser"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_product"])) {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = (float)$_POST["price"];
    $category = (int)$_POST["category"];
    $image = $_FILES["image"];
    $sizes = $_POST["sizes"] ?? [];
    $stocks = $_POST["stocks"] ?? [];

    $uploadDir = "uploads";
    $fileName = uniqid() . "_" . basename($image["name"]);
    $targetPath = "$uploadDir/$fileName";

    $allowed = ["image/jpeg", "image/png", "image/webp", "image/gif"];
    $isValid = in_array(mime_content_type($image["tmp_name"]), $allowed);

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (!$isValid || !move_uploaded_file($image["tmp_name"], $targetPath)) {
        $message = "<p style='color:red;'>Chyba při nahrávání obrázku.</p>";
    } else {
        $db->begin_transaction();

        $stmt = $db->prepare("INSERT INTO products (name, description, price, image_url, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $name, $description, $price, $targetPath, $category);
        $stmt->execute();
        $productId = $stmt->insert_id;
        $stmt->close();

        $variantOk = false;
        $stmtVar = $db->prepare("INSERT INTO product_variants (product_id, size, stock) VALUES (?, ?, ?)");

        foreach ($sizes as $i => $size) {
            $s = trim($size);
            $stock = (int)($stocks[$i] ?? 0);

            if ($s !== "") {
                $stmtVar->bind_param("isi", $productId, $s, $stock);
                $stmtVar->execute();
                $variantOk = true;
            }
        }

        $stmtVar->close();

        if ($variantOk) {
            $db->commit();
            $message = "<p style='color:#66ff66;'>Produkt úspěšně přidán.</p>";
        } else {
            $db->rollback();
            $message = "<p style='color:red;'>Musíte zadat alespoň jednu velikost a sklad.</p>";
        }
    }
}

$categories = $db->query("SELECT id, name FROM categories");
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přidat produkt</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_admin_panel.css">

    <style>
        form {
            max-width: 700px;
            background-color: #1c1c1c;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        form label {
            display: block;
            margin-top: 15px;
            color: #ccc;
            font-size: 15px;
        }

        form input[type="text"],
        form input[type="number"],
        form textarea,
        form select,
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2a2a2a;
            color: #eee;
            box-sizing: border-box;
        }

        form textarea {
            resize: vertical;
        }

        form button {
            margin-top: 20px;
            background-color: #fff;
            color: #000;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #ddd;
        }
        
        .variant-group {
            border-top: 1px solid #444;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .variant-inputs {
            display: flex;
            gap: 20px;
        }

        .variant-inputs div {
            flex: 1;
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
        <h1>Přidat nový produkt</h1>
        <?php echo $message; ?>

        <form method="post" enctype="multipart/form-data" class="form-box">
            <input type="hidden" name="add_product" value="1">

            <label>Název:</label>
            <input type="text" name="name" required>

            <label>Popis:</label>
            <textarea name="description" rows="4"></textarea>

            <label>Cena (Kč):</label>
            <input type="number" name="price" step="0.01" required>

            <label>Kategorie:</label>
            <select name="category" required>
                <option value="">Vyberte kategorii</option>
                <?php while ($cat = $categories->fetch_assoc()) { ?>
                    <option value="<?php echo $cat["id"]; ?>"><?php echo htmlentities($cat["name"]); ?></option>
                <?php } ?>
            </select>

            <label>Obrázek produktu:</label>
            <input type="file" name="image" accept="image/*" required>

            <div class="variant-group">
                <h3>Velikosti a sklad</h3>
                <?php for ($i = 0; $i < 3; $i++) { ?>
                    <div class="variant-inputs">
                        <div>
                            <label>Velikost <?php echo $i + 1; ?>:</label>
                            <input type="text" name="sizes[]" placeholder="např. M">
                        </div>
                        <div>
                            <label>Sklad (ks):</label>
                            <input type="number" name="stocks[]" min="0" placeholder="např. 10">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <button type="submit">Přidat produkt</button>
        </form>
    </div>
</div>
</body>
</html>
