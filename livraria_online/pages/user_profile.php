<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

$userId = $_SESSION['user_id'] ?? 0;

// Processa atualização de perfil
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$username || !$email) {
            $error = "Todos os campos são obrigatórios.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $userId]);
            $_SESSION['username'] = $username; // Atualiza sessão
            $success = "Perfil atualizado com sucesso!";
        }
    } elseif (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$current || !$new || !$confirm) {
            $error = "Todos os campos de senha são obrigatórios.";
        } elseif ($new !== $confirm) {
            $error = "Nova senha e confirmação não coincidem.";
        } else {
            // Busca senha atual
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || !password_verify($current, $row['password'])) {
                $error = "Senha atual incorreta.";
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hash, $userId]);
                $success = "Senha alterada com sucesso!";
            }
        }
    }
}

// Busca dados atualizados do usuário
$stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container my-5 reveal">
    <h2 class="mb-4">Meu Perfil</h2>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Formulário de atualização de perfil -->
    <form method="POST" class="mb-5">
        <input type="hidden" name="update_profile">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <button class="btn btn-primary">Atualizar Perfil</button>
    </form>

    <!-- Formulário de alteração de senha -->
    <form method="POST">
        <input type="hidden" name="change_password">
        <div class="mb-3">
            <label class="form-label">Senha Atual</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nova Senha</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirmar Nova Senha</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button class="btn btn-success">Alterar Senha</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
