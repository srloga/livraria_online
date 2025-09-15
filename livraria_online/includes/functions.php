<?php
function redirect($url) {
    header("Location: $url");
    exit;
}
function getCartItems(int $userId): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT ci.book_id AS id, b.title, b.price, ci.quantity
        FROM carts c
        JOIN cart_items ci ON ci.cart_id = c.id
        JOIN books b ON b.id = ci.book_id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>



