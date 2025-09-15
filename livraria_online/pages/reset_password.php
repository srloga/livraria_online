<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (!$token) {
    $error = 'Token inválido.';
    include __DIR__ . '/../includes/header.php';
    echo "<div class='container my-5'><div class='alert alert-danger'>{$error}</div></div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$token_hash = hash('sha256', $token);

// procura token válido
$stmt = $pdo->prepare("SELECT pr.id, pr.user_id, pr.expires_at, u.email FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token_hash = ?");
$stmt->execute([$token_hash]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $error = 'Token inválido ou já usado.';
} else {
    // verifica se está expirado
    $now = new DateTime();
    $expires = new DateTime($row['expires_at']);
    if ($now > $expires) {
        $error = 'Token expirado. Solicite uma nova redefinição.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$new || !$confirm) {
        $error = 'Preencha os campos de senha.';
    } elseif ($new !== $confirm) {
        $error = 'A confirmação não coincide.';
    } else {
        // atualiza senha do usuário
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $row['user_id']]);

        // remove todos tokens desse user (ou apenas o token atual)
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$row['user_id']]);

        $success = 'Senha atualizada com sucesso. Já pode entrar com a nova senha.';
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <h2>Redefinir Senha</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <a href="<?= BASE_URL ?>pages/forgot_password.php" class="btn btn-link">Solicitar novo link</a>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <a href="<?= BASE_URL ?>pages/login.php" class="btn btn-primary">Ir para Login</a>
    <?php else: ?>
        <p>Defina sua nova senha para o e-mail: <strong><?= htmlspecialchars($row['email'] ?? '') ?></strong></p>

        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Nova senha</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar nova senha</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button class="btn btn-success">Redefinir senha</button>
        </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
