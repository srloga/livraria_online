<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once 'header.php';

requireAdmin(); // garante que apenas admin acesse (IMPORTANTE lembrar disso)

$pdo = db_connect();

// PROCESSA AÇÕES POST - Quero dormir Zzzzzz
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([(int)$_POST['delete_id']]);
    } elseif (isset($_POST['mark_read_id'])) {
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([(int)$_POST['mark_read_id']]);
    }

    header("Location: messages.php");
    exit;
}

// Paginação
$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// Busca mensagens
$stmt = $pdo->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalStmt = $pdo->query("SELECT COUNT(*) FROM messages");
$totalMessages = (int)$totalStmt->fetchColumn();
$totalPages = ceil($totalMessages / $perPage);
?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Mensagens Recebidas</h2>

    <?php if (empty($messages)): ?>
        <div class="alert alert-info text-center">Nenhuma mensagem encontrada.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($messages as $msg): ?>
                <div class="col-md-6 col-lg-4 reveal">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-2">
                                <?= htmlspecialchars($msg['name']) ?>
                                <?php if (!empty($msg['is_read'])): ?>
                                    <span class="badge bg-success ms-2">Lida</span>
                                <?php else: ?>
                                    <span class="badge bg-warning ms-2">Nova</span>
                                <?php endif; ?>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($msg['email']) ?></h6>
                            <p class="card-text mb-2"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></small>
                            <div>
                                <?php if (empty($msg['is_read'])): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="mark_read_id" value="<?= $msg['id'] ?>">
                                        <button type="submit" 
                                                class="btn btn-sm btn-success btn-hover"
                                                style="position: relative; z-index: 5;">
                                            Marcar como lida
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" class="d-inline ms-1" onsubmit="return confirm('Tem certeza que deseja deletar esta mensagem?');">
                                    <input type="hidden" name="delete_id" value="<?= $msg['id'] ?>">
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger btn-hover"
                                            style="position: relative; z-index: 5;">
                                        Deletar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link btn-hover" href="?page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(el => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });

    setTimeout(() => {
        reveals.forEach(el => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        });
    }, 100);
});
</script>

<?php include 'footer.php'; ?>

