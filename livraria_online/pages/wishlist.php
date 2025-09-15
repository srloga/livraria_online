<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Remove item da wishlist 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'remove') {
    $bookId = (int)($_POST['book_id'] ?? 0);
    if ($bookId > 0) {
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$userId, $bookId]);
    }
    header("Location: wishlist.php");
    exit;
}

// Busca livros da wishlist
$sql = "SELECT b.* 
        FROM wishlist w 
        JOIN books b ON w.book_id = b.id 
        WHERE w.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="container my-5 fade-in">
    <h2>Minha Wishlist</h2>
    <br>
    <?php if (empty($books)): ?>
        <div class="alert alert-info text-center">
            Sua wishlist está vazia. <a href="<?= BASE_URL ?>pages/search.php">Descubra novos livros!</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($books as $book): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 fade-in">
                    <div class="card card-livro h-100 shadow-sm position-relative animate-img">

                        <?php 
                            $uploadPath = __DIR__ . '/../' . $book['image_path'];
                            $imagePath = !empty($book['image_path']) && file_exists($uploadPath)
                                ? BASE_URL . $book['image_path']
                                : BASE_URL . 'assets/images/placeholder.jpg';
                        ?>
                        <div class="card-img-wrapper">
                            <img src="<?= $imagePath ?>" 
                                 class="card-img-top animate-img" 
                                 alt="Capa do livro <?= htmlspecialchars($book['title']) ?>">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate" title="<?= htmlspecialchars($book['title']) ?>">
                                <?= htmlspecialchars($book['title']) ?>
                            </h5>
                            <p class="card-text text-muted mb-2"><?= htmlspecialchars($book['author']) ?></p>
                            <p class="mt-auto mb-3 fw-bold text-primary">€ <?= number_format($book['price'], 2, ',', '.') ?></p>

                            <a href="<?= BASE_URL ?>pages/book_details.php?id=<?= $book['id'] ?>" 
                               class="btn btn-primary mb-2 w-100 btn-hover animate-img">
                                📖 Ver detalhes
                            </a>

                            <form method="POST" action="<?= BASE_URL ?>pages/add-to-cart.php">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" 
                                        class="btn btn-warning w-100 btn-hover animate-img mb-2">
                                    🛒 Adicionar ao Carrinho
                                </button>
                            </form>

                            <form method="POST" action="">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" 
                                        class="btn btn-outline-danger w-100 btn-hover animate-img"
                                        onclick="return confirm('Remover este livro da sua wishlist?')">
                                    ❌ Remover da Wishlist
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>

