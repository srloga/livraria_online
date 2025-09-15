<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT o.id, o.total, o.address, o.phone, o.created_at,
           GROUP_CONCAT(CONCAT(oi.quantity,'x ',b.title) SEPARATOR ', ') as items
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN books b ON oi.book_id = b.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-5 reveal">
    <h2>Meus Pedidos</h2>
    <?php if(empty($orders)): ?>
        <div class="alert alert-info">Você ainda não fez nenhum pedido.</div>
    <?php else: ?>
        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Data</th>
                    <th>Itens</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                        <td><?= htmlspecialchars($o['items']) ?></td>
                        <td><?= htmlspecialchars($o['address']) ?></td>
                        <td><?= htmlspecialchars($o['phone']) ?></td>
                        <td>R$ <?= number_format($o['total'],2,',','.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
