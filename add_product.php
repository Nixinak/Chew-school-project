<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn() || $_SESSION["loggedInUser"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

function addProduct($db, $name, $description, $price, $categoryId, $image, $sizes, $stocks)
{
    $productsImageFolder = "uploads";
    $image_url = null;

    if (empty($image['name']) || $image['error'] !== UPLOAD_ERR_OK) {
        return "Nebyl vybrán žádný obrázek nebo nastala chyba při nahrávání.";
    }

    $fileType = mime_content_type($image["tmp_name"]);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($fileType, $allowedTypes)) {
        return "Nepodporovaný typ souboru. Použij JPG, PNG, WEBP nebo GIF.";
    }

    if (!file_exists($productsImageFolder)) {
        mkdir($productsImageFolder, 0777, true);
    }

    $filename = uniqid() . "_" . basename($image["name"]);
    $targetFile = $productsImageFolder . "/" . $filename;

    if (!move_uploaded_file($image["tmp_name"], $targetFile)) {
        return "Chyba při ukládání souboru na server.";
    }
    $image_url = $targetFile;
    
    $db->begin_transaction();

    $stmt_product = $db->prepare("INSERT INTO products (name, description, price, image_url, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt_product->bind_param("ssdsi", $name, $description, $price, $image_url, $categoryId);
    
    if (!$stmt_product->execute()) {
        $db->rollback();
        return "Chyba při ukládání hlavního produktu.";
    }

    $newProductId = $db->insert_id;
    $stmt_product->close();
    
    $stmt_variant = $db->prepare("INSERT INTO product_variants (product_id, size, stock) VALUES (?, ?, ?)");
    
    $variantAdded = false;
    for ($i = 0; $i < count($sizes); $i++) {
        if (!empty($sizes[$i]) && isset($stocks[$i])) {
            $size = trim($sizes[$i]);
            $stock = (int)$stocks[$i];
            
            $stmt_variant->bind_param("isi", $newProductId, $size, $stock);
            if (!$stmt_variant->execute()) {
                $db->rollback();
                return "Chyba při ukládání varianty produktu.";
            }
            $variantAdded = true;
        }
    }
    
    $stmt_variant->close();

    if (!$variantAdded) {
        $db->rollback();
        return "Musíte zadat alespoň jednu velikost a skladovou zásobu.";
    }

    $db->commit();
    return null;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_product"])) {
    $errorMessage = addProduct(
        $db,
        $_POST["name"],
        $_POST["description"],
        $_POST["price"],
        $_POST["category"],
        $_FILES['image'],
        $_POST['sizes'],
        $_POST['stocks']
    );

    if ($errorMessage) {
        $message = "<p style='color: red;'>" . $errorMessage . "</p>";
    } else {
        $message = "<p style='color: #66ff66;'>Produkt byl úspěšně přidán.</p>";
    }
}

$categories = $db->query("SELECT id, name FROM categories");
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Přidat produkt – Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_admin_panel.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
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
                <h1>Přidat nový produkt</h1>
            </div>

            <?php echo $message; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="add_product" value="1">

                <label for="name">Název produktu:</label>
                <input type="text" id="name" name="name" required>

                <label for="description">Popis:</label>
                <textarea id="description" name="description" rows="4"></textarea>

                <label for="price">Cena (Kč):</label>
                <input type="number" id="price" name="price" step="0.01" required>

                <label for="category">Kategorie:</label>
                <select id="category" name="category" required>
                    <option value="" disabled selected>Vyberte kategorii</option>
                    <?php while ($cat = $categories->fetch_assoc()) { ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlentities($cat['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>

                <label for="image">Obrázek produktu:</label>
                <input type="file" id="image" name="image" accept="image/*" required>

                <div class="variant-group">
                    <h3>Velikosti a skladové zásoby</h3>
                    <p>Vyplňte alespoň jednu velikost.</p>
                    
                    <div class="variant-inputs">
                        <div>
                            <label>Velikost 1:</label>
                            <input type="text" name="sizes[]" placeholder="např. M">
                        </div>
                        <div>
                            <label>Skladem (ks):</label>
                            <input type="number" name="stocks[]" placeholder="např. 10" min="0">
                        </div>
                    </div>

                    <div class="variant-inputs">
                        <div>
                            <label>Velikost 2:</label>
                            <input type="text" name="sizes[]" placeholder="např. L">
                        </div>
                        <div>
                            <label>Skladem (ks):</label>
                            <input type="number" name="stocks[]" placeholder="např. 15" min="0">
                        </div>
                    </div>
                    
                    <div class="variant-inputs">
                        <div>
                            <label>Velikost 3:</label>
                            <input type="text" name="sizes[]" placeholder="např. XL">
                        </div>
                        <div>
                            <label>Skladem (ks):</label>
                            <input type="number" name="stocks[]" placeholder="např. 5" min="0">
                        </div>
                    </div>
                </div>

                <button type="submit">Přidat produkt</button>
            </form>
        </div>
    </div>
</body>
</html>