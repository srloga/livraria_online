<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
$pdo = db_connect();

$userId    = $_SESSION['user_id'];
$cartItems = getCartItems($userId);

// Total do carrinho
$total = array_reduce($cartItems, fn($sum, $i) => $sum + $i['price'] * $i['quantity'], 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address       = trim($_POST['address'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');

    if ($address === '' || $phone === '' || $paymentMethod === '' || empty($cartItems)) {
        $error = "Preencha todos os campos, selecione o método de pagamento e verifique seu carrinho.";
    } else {
        // Cria pedido
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, address, phone, payment_method) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $total, $address, $phone, $paymentMethod]);
        $orderId = $pdo->lastInsertId();

        // Adiciona itens ao pedido
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmtItem->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }

        // Limpa carrinho
        $pdo->prepare("
            DELETE ci FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.user_id = ?
        ")->execute([$userId]);

        // Simulação de pagamento (aprovado automaticamente) - ** para melhorar futuramente **
        $_SESSION['payment_success'] = true;
        $_SESSION['payment_method']  = $paymentMethod;

        header('Location: thankyou.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5 reveal">
    <h2 class="mb-4">Finalizar Pedido</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Formulário de dados -->
        <div class="col-md-6">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Endereço de Entrega</label>
                    <textarea name="address" class="form-control" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Método de Pagamento</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="cartao" <?= (($_POST['payment_method'] ?? '') === 'cartao') ? 'checked' : '' ?> required>
                        <label class="form-check-label" for="creditCard">Cartão de Crédito</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" <?= (($_POST['payment_method'] ?? '') === 'paypal') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="paypal">PayPal</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="mbway" value="mbway" <?= (($_POST['payment_method'] ?? '') === 'pix') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="pix">MBWay</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Confirmar Pedido</button>
                <a href="<?= BASE_URL ?>pages/cart.php" class="btn btn-secondary ms-2">Voltar ao Carrinho</a>
            </form>
        </div>

        <!-- Resumo do pedido -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Resumo do Pedido</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($item['title']) ?> (x<?= $item['quantity'] ?>)
                            <span>€ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></span>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between fw-bold text-primary">
                        Total
                        <span>€ <?= number_format($total, 2, ',', '.') ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

