<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$messages = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Informe seu e-mail.';
    } else {
        // procura usuário
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // por segurança, pode-se mostrar mensagem neutra
            $messages[] = 'Se o e-mail existir, um link de redefinição será enviado.';
        } else {
            // gera token zoado de forte
            $token = bin2hex(random_bytes(32)); // token visível para envio ao utilizador
            $token_hash = hash('sha256', $token);
            $expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

            // guarda no banco
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token_hash, $expires]);

            // link de reset
            $resetLink = BASE_URL . "pages/reset_password.php?token=$token";

            // Para ambiente de dev (sem mail), mostro o link na tela — remover depois de melhorias**
            $messages[] = "Link de redefinição (ambiente de desenvolvimento): <a href=\"$resetLink\">$resetLink</a>";

            // mensagem neutra para o usuário
            $messages[] = 'Se o e-mail existir, um link de redefinição foi criado. Verifica o e-mail (ou o link de teste).';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <h2>Recuperar Senha</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php foreach($messages as $m): ?>
        <div class="alert alert-info"><?= $m ?></div>
    <?php endforeach; ?>

    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Digite seu e-mail</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button class="btn btn-primary">Enviar link de redefinição</button>
        <a href="<?= BASE_URL ?>pages/login.php" class="btn btn-link">Voltar ao login</a>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
