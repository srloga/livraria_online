<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Gerenciar Categorias';

include __DIR__ . '/header.php';

// Adicionar categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['description'] ?? null]);
    header("Location: categories.php");
    exit;
}

// Deletar categoria
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: categories.php");
    exit;
}

// Listar categorias
$categories = $pdo->query("SELECT * FROM categories ORDER BY created_at DESC")->fetchAll();
?>

<div class="mb-4">
    <h2 class="mb-3">Gerenciar Categorias</h2>

    <!-- Formulário para adicionjar categoria -->
    <form method="POST" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Nome da categoria" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="description" class="form-control" placeholder="Descrição (opcional)">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100">Adicionar</button>
            </div>
        </div>
    </form>

    <!-- Lista de categorias -->
    <?php if ($categories): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            <td><?= date('d/m/Y', strtotime($cat['created_at'])) ?></td>
                            <td>
                                <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Deseja realmente excluir esta categoria?')">
                                   Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Nenhuma categoria cadastrada.</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
