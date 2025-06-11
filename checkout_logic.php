<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['loggedInUser']['id'];

$sql_cart = "SELECT ci.product_variant_id, ci.quantity, p.price
             FROM cart_items ci
             JOIN product_variants pv ON ci.product_variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             WHERE ci.user_id = ?";

$stmt_cart = $db->prepare($sql_cart);
if ($stmt_cart === false) {
    die("Chyba při přípravě dotazu na získání položek z košíku: " . $db->error);
}
$stmt_cart->bind_param("i", $userId);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();
$cartItems = [];
while ($row = $result_cart->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmt_cart->close();

if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

$db->begin_transaction();

$stmt_order = $db->prepare("INSERT INTO orders (user_id, status) VALUES (?, 'zpracovává se')");
if ($stmt_order === false) {
    die("Chyba při přípravě dotazu na vytvoření objednávky: " . $db->error);
}
$stmt_order->bind_param("i", $userId);
$stmt_order->execute();
$newOrderId = $db->insert_id;
$stmt_order->close();

$stmt_order_item = $db->prepare("INSERT INTO order_items (order_id, product_variant_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
if ($stmt_order_item === false) {
    die("Chyba při přípravě dotazu na vložení položek objednávky: " . $db->error);
}

foreach ($cartItems as $item) {
    $stmt_order_item->bind_param("iiid", $newOrderId, $item['product_variant_id'], $item['quantity'], $item['price']);
    $stmt_order_item->execute();
}
$stmt_order_item->close();

$stmt_clear_cart = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
if ($stmt_clear_cart === false) {
    die("Chyba při přípravě dotazu na smazání košíku: " . $db->error);
}
$stmt_clear_cart->bind_param("i", $userId);
$stmt_clear_cart->execute();
$stmt_clear_cart->close();

$db->commit();

header("Location: order_success.php?order_id=" . $newOrderId);
exit;