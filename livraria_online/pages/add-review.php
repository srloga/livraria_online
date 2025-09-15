<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin(); // garante que o usuário esteja logado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $bookId = (int)($_POST['book_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if (!$bookId || $rating < 1 || $rating > 5 || $comment === '') {
        $error = "Todos os campos são obrigatórios e a avaliação deve ser de 1 a 5 estrelas.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO reviews (book_id, user_id, rating, comment, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$bookId, $userId, $rating, $comment]);
        header("Location: book_details.php?id={$bookId}");
        exit;
    }
}
?>
