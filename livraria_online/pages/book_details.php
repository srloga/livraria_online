<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/header.php';

$bookId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT b.*, c.name AS category
    FROM books b
    JOIN categories c ON b.category_id = c.id
    WHERE b.id = ?
");
$stmt->execute([$bookId]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    echo "<p>Livro não encontrado.</p>";
    require_once '../includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, u.username
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.book_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$bookId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcula média de rating
$averageRating = 0;
if ($reviews) {
    $totalRating = array_sum(array_column($reviews, 'rating'));
    $averageRating = round($totalRating / count($reviews), 1);
}

// Verifica se o livro está na wishlist do usuário
$isFav = false;
if (isset($_SESSION['user_id'])) {
    $currentUserId = $_SESSION['user_id'];
    $wstmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND book_id = ?");
    $wstmt->execute([$currentUserId, $bookId]);
    $isFav = (bool)$wstmt->fetchColumn();
}
?>

<div class="container my-5 fade-in">

    <a href="javascript:history.back()" class="btn btn-secondary mb-3 btn-hover">← Voltar</a>

    <div class="book-details-container">

        <!-- Imagem do livro -->
        <div class="book-image">
            <?php 
            $uploadPath = __DIR__ . '/../' . $book['image_path'];
            $imagePath = !empty($book['image_path']) && file_exists($uploadPath)
                ? BASE_URL . $book['image_path']
                : BASE_URL . 'assets/images/placeholder.jpg';
            ?>
            <img src="<?= $imagePath ?>" alt="Capa do livro <?= htmlspecialchars($book['title']) ?>">
        </div>

        <!-- Infos do livro -->
        <div class="book-details-info">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p><strong>Autor:</strong> <?= htmlspecialchars($book['author']) ?></p>
            <p><strong>Categoria:</strong> <?= htmlspecialchars($book['category']) ?></p>
            <p><strong>Preço:</strong> € <?= number_format($book['price'], 2, ',', '.') ?></p>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Favoritos -->
                <a href="<?= BASE_URL ?>pages/wishlist.php?action=add&id=<?= $book['id'] ?>" 
                   class="btn btn-sm btn-outline-danger mb-2 btn-hover">
                   ❤️ Adicionar este livro aos favoritos
                </a>

                <!-- Adicionar ao Carrinho -->
                <form method="POST" action="<?= BASE_URL ?>pages/add-to-cart.php" class="mb-2">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger btn-hover">
                        🛒 Adicionar ao Carrinho
                    </button>
                </form>
            <?php else: ?>
                <a href="<?= BASE_URL ?>pages/login.php" class="btn btn-sm btn-outline-secondary mb-2 btn-hover">
                   Faça login para adicionar este livro aos favoritos
                </a>
            <?php endif; ?>

            <!-- Média de avaliações -->
            <?php if ($reviews): ?>
                <p class="mt-3">
                    <strong>Avaliação média:</strong>
                    <?= $averageRating ?> 
                    <span class="review-stars">★</span>
                </p>
            <?php else: ?>
                <p class="mt-3"><strong>Avaliação média:</strong> Sem avaliações</p>
            <?php endif; ?>

            <!-- Descrição -->
            <div class="book-description mt-3">
                <?= nl2br(htmlspecialchars($book['description'])) ?>
            </div>
        </div>
    </div>

    <!-- Avaliações -->
    <h3 class="mt-5">Avaliações</h3>
    <?php if ($reviews): ?>
        <?php foreach($reviews as $r): ?>
            <div class="review-card mb-3">
                <p><strong><?= htmlspecialchars($r['username']) ?></strong> 
                    <span class="review-stars"><?= str_repeat('★', $r['rating']) ?></span>
                </p>
                <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Este livro ainda não possui avaliações.</p>
    <?php endif; ?>

    <!-- Formulário de avaliação -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <h3 class="mt-5">Adicionar avaliação</h3>
        <form action="<?= BASE_URL ?>pages/add-review.php" method="POST" class="add-review-form">
            <input type="hidden" name="book_id" value="<?= $bookId ?>">
            <div class="mb-3">
                <label for="rating">Classificação:</label>
                <select name="rating" id="rating" required>
                    <option value="">Escolha</option>
                    <option value="1">1 ★</option>
                    <option value="2">2 ★★</option>
                    <option value="3">3 ★★★</option>
                    <option value="4">4 ★★★★</option>
                    <option value="5">5 ★★★★★</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="comment">Comentário:</label>
                <textarea name="comment" id="comment" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-hover">Enviar avaliação</button>
        </form>
    <?php else: ?>
        <p>Faça login para adicionar uma avaliação.</p>
    <?php endif; ?>

</div>

<script>
$(document).on('click','.btn-wishlist', function(){
    const btn = $(this), bookId = btn.data('id');
    $.post('<?= BASE_URL ?>ajax/toggle_wishlist.php',{book_id:bookId}, function(r){
        if(!r.ok){ alert(r.msg || 'Erro'); return; }
        if(r.added){
            btn.removeClass('btn-outline-primary').addClass('btn-primary').html('<i class="bi bi-heart-fill"></i> Favorito');
        } else {
            btn.removeClass('btn-primary').addClass('btn-outline-primary').html('<i class="bi bi-heart"></i> Favoritar');
        }
    },'json');
});
</script>

<?php require_once '../includes/footer.php'; ?>

