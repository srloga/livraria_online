<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

include __DIR__ . '/header.php';

$conn = db_connect();
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;
$newPassword = null;
$emailSent = false;

// Exclui usuário (não deixa excluir o logado)
if ($action === 'delete' && $id) {
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: users.php");
    exit;
}

// Resetar senha
if ($action === 'reset' && $id) {
    if ($id != $_SESSION['user_id']) {
        
        $stmt = $conn->prepare("SELECT email, username FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            // gera senha aleatória - achei mais fácil assim
            $newPassword = "Usr" . rand(1000, 9999) . "!";
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Atualiza senha no banco
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$hash, $id]);

            // Envia e-mail - melhoria possível no futuro
            $to = $userData['email'];
            $subject = "Sua senha foi redefinida";
            $message = "Olá " . $userData['username'] . ",\n\n";
            $message .= "Sua nova senha de acesso é: " . $newPassword . "\n\n";
            $message .= "Recomendamos que altere a senha após o login.\n\n";
            $headers = "From: no-reply@seudominio.com\r\n";

            $emailSent = mail($to, $subject, $message, $headers);
        }
    }
}

// Salvar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $uid = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // evita tirar admin do próprio usuário
    if ($uid == $_SESSION['user_id']) {
        $is_admin = 1;
    }

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, is_admin=? WHERE id=?");
    $stmt->execute([$username, $email, $is_admin, $uid]);

    header("Location: users.php");
    exit;
}

// Se for editar, carrega dados
$editUser = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT id, username, email, is_admin FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lista de usuários
$stmt = $conn->prepare("SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Usuários Cadastrados</h2>

    <?php if ($newPassword): ?>
        <div class="alert alert-success">
            ✅ A nova senha foi gerada: <strong><?= $newPassword ?></strong><br>
            <?php if ($emailSent): ?>
                O usuário também recebeu a nova senha por e-mail.
            <?php else: ?>
                Falha ao enviar e-mail. Informe a senha manualmente ao usuário.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($editUser): ?>
        <!-- Formulário de edição -->
        <div class="card mb-4">
            <div class="card-header">Editar Usuário #<?= $editUser['id'] ?></div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                    <div class="col-md-4">
                        <label class="form-label">Usuário</label>
                        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($editUser['username']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($editUser['email']) ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_admin" class="form-check-input" id="adminCheck" <?= $editUser['is_admin'] ? 'checked' : '' ?>>
                            <label for="adminCheck" class="form-check-label">Administrador</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        <a href="users.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lista -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Email</th>
                <th>Admin?</th>
                <th>Data de Registro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['is_admin'] ? 'Sim' : 'Não' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                <td>
                    <a href="users.php?action=edit&id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <a href="users.php?action=delete&id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                           Excluir
                        </a>
                        <a href="users.php?action=reset&id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-info"
                           onclick="return confirm('Deseja resetar a senha deste usuário?')">
                           Resetar Senha
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Você</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
