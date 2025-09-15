<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

include __DIR__ . '/header.php';

$conn = db_connect();
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Atualiza status do pedido
if ($action === 'update' && $id && isset($_POST['status'])) {
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
    header("Location: orders.php");
    exit;
}

// Cancela pedido
if ($action === 'cancel' && $id) {
    $stmt = $conn->prepare("UPDATE orders SET status='cancelado' WHERE id=?");
    $stmt->execute([$id]);
    header("Location: orders.php");
    exit;
}

// Carrega pedidos
$stmt = $conn->query("
    SELECT orders.*, users.username 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    ORDER BY orders.created_at DESC
");
$orders = $stmt->fetchAll();

// Status disponíveis
$statusOptions = ['pendente', 'enviado', 'entregue'];

// Classes e ícones - Totalmente desnecessário (Pq eu sou assim??)
function statusClass($status) {
    return match($status) {
        'pendente' => 'table-warning',
        'enviado' => 'table-primary text-white',
        'entregue' => 'table-success',
        'cancelado' => 'table-danger text-white',
        default => '',
    };
}

function statusIcon($status) {
    return match($status) {
        'pendente' => '⏳',
        'enviado' => '📦',
        'entregue' => '✔️',
        'cancelado' => '❌',
        default => '',
    };
}

// Função para mostrar pagamento aprovado
function paymentStatus($paymentMethod, $status) {
    $method = match($paymentMethod) {
        'cartao' => 'Cartão de Crédito',
        'paypal' => 'PayPal',
        'pix' => 'PIX/MBWay',
        default => 'Outro',
    };
    $approved = in_array($status, ['pendente', 'enviado', 'entregue']) ? '✅' : '❌';
    return "$method $approved";
}
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Pedidos</h2>
    <?php if (count($orders) === 0): ?>
        <p>Nenhum pedido registrado.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Total</th>
                    <th>Método Pagamento</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr class="<?= statusClass($order['status']) ?>">
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= htmlspecialchars($order['phone']) ?></td>
                        <td><?= htmlspecialchars($order['address']) ?></td>
                        <td>€ <?= number_format($order['total'], 2, ',', '.') ?></td>
                        <td><?= paymentStatus($order['payment_method'], $order['status']) ?></td>
                        <td>
                            <?php if ($order['status'] !== 'cancelado'): ?>
                                <form method="POST" action="orders.php?action=update&id=<?= $order['id'] ?>" class="d-inline">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <?php foreach ($statusOptions as $status): ?>
                                            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                                                <?= statusIcon($status) ?> <?= ucfirst($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php else: ?>
                                <?= statusIcon('cancelado') ?> Cancelado
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                            <?php if ($order['status'] !== 'cancelado'): ?>
                                <a href="orders.php?action=cancel&id=<?= $order['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Deseja realmente cancelar este pedido?')">
                                   Cancelar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>

