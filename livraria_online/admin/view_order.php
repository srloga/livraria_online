<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

include __DIR__ . '/header.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$conn = db_connect();

// Busca detalhes do pedido
$stmt = $conn->prepare("SELECT orders.*, users.username FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        WHERE orders.id = ?");
$stmt->execute([$_GET['id']]);
$order = $stmt->fetch();

if (!$order) {
    echo "<p>Pedido não encontrado.</p>";
    exit;
}

// Busca itens do pedido
$stmt = $conn->prepare("SELECT order_items.*, books.title FROM order_items 
                        JOIN books ON order_items.book_id = books.id 
                        WHERE order_items.order_id = ?");
$stmt->execute([$_GET['id']]);
$items = $stmt->fetchAll();
?>

<div class="container mt-5 mb-5">
    <h2>Pedido #<?= $order['id'] ?> - <?= htmlspecialchars($order['username']) ?></h2>
    <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>Telefone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p><strong>Endereço:</strong> <?= htmlspecialchars($order['address']) ?></p>
    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>

    <h4 class="mt-4">Itens do Pedido</h4>
    <?php if (count($items) === 0): ?>
        <p>Pedido sem itens.</p>
    <?php else: ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>€ <?= number_format($item['price'], 2, ',', '.') ?></td>
                        <td>€ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
                    </tr>
                    <?php $total += $item['price'] * $item['quantity']; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total do Pedido:</strong> € <?= number_format($total, 2, ',', '.') ?></p>
    <?php endif; ?>

    <a href="orders.php" class="btn btn-secondary mt-3">Voltar</a>
</div>

<?php 
include __DIR__ . '/footer.php';
