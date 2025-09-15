<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $conn = db_connect();
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        $success = true;
    } else {
        $error = "Todos os campos são obrigatórios.";
    }
}

include '../includes/header.php';
?>

<div class="container my-5">

    <div class="text-center mb-5 reveal">
        <h2 class="fw-bold">Entre em Contato</h2>
        <p class="text-muted">Estamos aqui para ouvir você! Envie sua mensagem e responderemos em breve.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-4 card-hover reveal">

                <?php if ($success): ?>
                    <div class="alert alert-success reveal">Mensagem enviada com sucesso!</div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger reveal"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" class="mt-3 reveal" style="position: relative; z-index: 2;">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome:</label>
                        <input type="text" id="name" class="form-control" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" class="form-control" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Mensagem:</label>
                        <textarea id="message" class="form-control" name="message" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-hover">Enviar Mensagem</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
// Animação reveal
document.addEventListener('DOMContentLoaded', function() {
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(el => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
    });

    setTimeout(() => {
        reveals.forEach(el => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        });
    }, 100);
});
</script>

<?php include '../includes/footer.php'; ?>


