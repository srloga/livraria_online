<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'msg' => 'Você precisa fazer login.']);
    exit;
}

$userId = $_SESSION['user_id'];
$bookId = (int)($_POST['book_id'] ?? 0);

if ($bookId <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Livro inválido.']);
    exit;
}

// Verifica se o livro já está na wishlist
$stmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND book_id = ?");
$stmt->execute([$userId, $bookId]);
$exists = (bool)$stmt->fetchColumn();

if ($exists) {
    // Remove da wishlist
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$userId, $bookId]);
    echo json_encode(['ok' => true, 'added' => false]);
} else {
    // Adiciona à wishlist
    $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, book_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $bookId]);
    echo json_encode(['ok' => true, 'added' => true]);
}
