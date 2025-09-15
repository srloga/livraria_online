<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin(); // Garante que o usuário esteja logado

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $bookId = (int) $_POST['book_id'];
    $userId = $_SESSION['user_id'];

    // Verifica se já existe um carrinho para o usuário
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $cart = $stmt->fetch();

    if (!$cart) {
        // Cria novo carrinho se não existir
        $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
        $stmt->execute([$userId]);
        $cartId = $pdo->lastInsertId();
    } else {
        $cartId = $cart['id'];
    }

    // Verifica se o item já está no carrinho
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND book_id = ?");
    $stmt->execute([$cartId, $bookId]);
    $item = $stmt->fetch();

    if ($item) {
        // Atualiza quantidade
        $newQty = $item['quantity'] + 1;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQty, $item['id']]);
    } else {
        // Adiciona novo item
        $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, book_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$cartId, $bookId]);
    }

    header("Location: cart.php");
    exit();
} else {
    header("Location: home.php");
    exit();
}
