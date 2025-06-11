<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn()) {
    header("Location: login_page.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['loggedInUser']['id'];
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    if (!isset($_POST['variant_id']) || !is_numeric($_POST['variant_id'])) {
        header("Location: products.php");
        exit;
    }
    $variantId = (int)$_POST['variant_id'];

    $stmt_check = $db->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_variant_id = ?");
    $stmt_check->bind_param("ii", $userId, $variantId);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $existing_item = $result_check->fetch_assoc();
    $stmt_check->close();

    if ($existing_item) {
        $new_quantity = $existing_item['quantity'] + 1;
        $stmt_update = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt_update->bind_param("iii", $new_quantity, $existing_item['id'], $userId);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        $stmt_insert = $db->prepare("INSERT INTO cart_items (user_id, product_variant_id, quantity) VALUES (?, ?, 1)");
        $stmt_insert->bind_param("ii", $userId, $variantId);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
} elseif ($action === 'update_quantity') {
    $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($quantity > 0) {
        $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cartItemId, $userId);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartItemId, $userId);
        $stmt->execute();
        $stmt->close();
    }
} elseif ($action === 'remove_item') {
    $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
    $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cartItemId, $userId);
    $stmt->execute();
    $stmt->close();
}

header("Location: cart.php");
exit;