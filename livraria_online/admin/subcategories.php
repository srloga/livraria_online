<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Gerenciar Subcategorias';
//  (admin/header.php)
include __DIR__ . '/header.php';

// Adiciona subcategoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['category_id'])) {
    $stmt = $pdo->prepare("INSERT INTO subcategories (category_id, name, description) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['category_id'], $_POST['name'], $_POST['description'] ?? null]);
    header("Location: subcategories.php");
    exit;
}

// Deleta subcategoria
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM subcategories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: subcategories.php");
    exit;
}

// Busca categorias e subcategorias
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$subcategories = $pdo->query("
    SELECT s.*, c.name AS category_name 
    FROM subcategories s
    JOIN categories c ON s.category_id = c.id
    ORDER BY s.created_at DESC
")->fetchAll();
?>

<div class="container py-4">
    <h2 class="mb-4">Gerenciar Subcategorias</h2>

    <form method="POST" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="category_id" class="form-select" required>
                    <option value="">Categoria</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Nome da subcategoria" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="description" class="form-control" placeholder="Descrição (opcional)">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100">Adicionar</button>
            </div>
        </div>
    </form>

    <?php if ($subcategories): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Categoria</th>
                        <th>Subcategoria</th>
                        <th>Descrição</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subcategories as $sub): ?>
                        <tr>
                            <td><?= $sub['id'] ?></td>
                            <td><?= htmlspecialchars($sub['category_name']) ?></td>
                            <td><?= htmlspecialchars($sub['name']) ?></td>
                            <td><?= htmlspecialchars($sub['description']) ?></td>
                            <td><?= date('d/m/Y', strtotime($sub['created_at'])) ?></td>
                            <td>
                                <a href="?delete=<?= $sub['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Deseja realmente excluir esta subcategoria?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Nenhuma subcategoria cadastrada.</p>
    <?php endif; ?>
</div>

<?php 
include __DIR__ . '/footer.php';
