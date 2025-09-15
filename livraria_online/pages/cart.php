<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
$user_id = $_SESSION['user_id'];

// Pega ou cria o carrinho do usuário
$stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['id'];
}

// Remover item
if (isset($_GET['remove'])) {
    $item_id = (int)$_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
    $stmt->execute([$item_id, $cart_id]);
    header('Location: cart.php');
    exit;
}

// Atualizar quantidades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $item_id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) {
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
            $stmt->execute([$item_id, $cart_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?");
            $stmt->execute([$qty, $item_id, $cart_id]);
        }
    }
    header('Location: cart.php');
    exit;
}

// Buscar itens do carrinho
$stmt = $pdo->prepare("
    SELECT ci.id, b.title, b.price, ci.quantity
    FROM cart_items ci
    JOIN books b ON ci.book_id = b.id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcula total
$total = array_reduce($items, fn($sum, $i) => $sum + $i['price'] * $i['quantity'], 0);

// Renderiza a página
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5 reveal">
    <h2 class="mb-4">Meu Carrinho</h2>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">Seu carrinho está vazio.</div>
        <a href="<?= BASE_URL ?>pages/home.php" class="btn btn-primary">Continuar Comprando</a>
    <?php else: ?>
        <form method="POST">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Livro</th>
                            <th class="text-center">Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['title']) ?></td>
                                <td class="text-center">
                                    <input 
                                        type="number" 
                                        name="quantities[<?= $item['id'] ?>]" 
                                        value="<?= $item['quantity'] ?>" 
                                        min="0" 
                                        class="form-control mx-auto" 
                                        style="width:80px;"
                                    >
                                </td>
                                <td>€ <?= number_format($item['price'], 2, ',', '.') ?></td>
                                <td>€ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-sm btn-danger">
                                        Remover
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td colspan="2" class="fw-bold text-primary">€ <?= number_format($total, 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
                <a href="<?= BASE_URL ?>pages/home.php" class="btn btn-secondary">
                    Continuar Comprando
                </a>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-warning">Atualizar Carrinho</button>
                    <a href="<?= BASE_URL ?>pages/checkout.php" class="btn btn-success">Finalizar Compra</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


