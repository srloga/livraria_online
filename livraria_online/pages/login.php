<?php
require_once __DIR__   . '/../includes/config.php';
require_once __DIR__   . '/../includes/db.php';
require_once __DIR__   . '/../includes/auth.php';

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =         $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Preencha todos os campos.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            $redirect = $user['is_admin']
                ? BASE_URL . 'admin/categories.php'
                : BASE_URL . 'pages/home.php';

            header("Location: $redirect");
            exit;
        } else {
            $errors[] = 'E-mail ou senha inválidos.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="login reveal">
    <div class="form">
        <img 
          src="<?= BASE_URL ?>assets/images/logo.png" 
          alt="<?= SITE_NAME ?>" 
          class="imagem"
        >

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input 
              type="email" 
              name="email" 
              placeholder="Seu Email" 
              required 
              value="<?= htmlspecialchars($email) ?>"
            >
            <input 
              type="password" 
              name="password" 
              placeholder="Senha" 
              required
            >
            <button type="submit">Login</button>
        </form>

        <!-- Link para recuperar senha -->
        <div class="mt-3">
            <a href="<?= BASE_URL ?>pages/forgot_password.php">
                Esqueceu a senha?
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>





