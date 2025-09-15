<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/header.php';

// Categorias e filtro
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$categoryId = (int)($_GET['category'] ?? 0);

// Paginação
$perPage = 8;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// Conta total de livros
$countSql = "SELECT COUNT(*) FROM books WHERE 1=1";
$countParams = [];
if ($categoryId > 0) {
    $countSql .= " AND category_id = ?";
    $countParams[] = $categoryId;
}
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($countParams);
$totalBooks = (int)$stmtCount->fetchColumn();
$totalPages = ceil($totalBooks / $perPage);

// Busca livros
$sql = "SELECT b.*, c.name AS category 
        FROM books b 
        JOIN categories c ON b.category_id = c.id 
        WHERE 1=1";
$params = [];
if ($categoryId > 0) {
    $sql .= " AND b.category_id = ?";
    $params[] = $categoryId;
}
$sql .= " ORDER BY b.created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Wishlist do usuário (se logado)
$wishlistIds = [];
if (isset($currentUserId)) {
    $wstmt = $pdo->prepare("SELECT book_id FROM wishlist WHERE user_id = ?");
    $wstmt->execute([$currentUserId]);
    $wishlistIds = $wstmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!-- Banner/Carrossel -->
<div id="bannerCarousel" class="carousel slide fade-in" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="<?= BASE_URL ?>assets/images/banner.jpg" class="banner-image animate-img" alt="Banner 1">
            <div class="carousel-caption d-none d-md-block">
                <h5>Bem-vindo à nossa Livraria</h5>
                <p>Descubra os melhores livros selecionados para você.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?= BASE_URL ?>assets/images/banner2.jpg" class="banner-image animate-img" alt="Banner 2">
            <div class="carousel-caption d-none d-md-block">
                <h5>Lançamentos Exclusivos</h5>
                <p>Confira as últimas novidades adicionadas ao catálogo.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?= BASE_URL ?>assets/images/banner4.jpg" class="banner-image animate-img" alt="Banner 3">
            <div class="carousel-caption d-none d-md-block">
                <h5>Ofertas Especiais</h5>
                <p>Aproveite promoções imperdíveis em livros selecionados.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Próximo</span>
    </button>
</div>

<div class="container my-5 fade-in">

    <!-- Filtro de categorias -->
    <div class="mb-4 fade-in">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="category" class="form-select w-auto">
                <option value="0">Todas as categorias</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary btn-hover">Filtrar</button>
        </form>
    </div>

    <!-- Lista de livros -->
    <div class="row g-4 fade-in">
        <?php foreach($books as $b): ?>
            <div class="col-sm-6 col-md-4 col-lg-3 fade-in">
                <div class="card card-livro h-100 shadow-sm position-relative animate-img">

                    <!-- Ribbon de destaques -->
                    <?php if($b['featured']): ?>
                        <div class="ribbon">Destaque</div>
                    <?php endif; ?>

                    <?php 
                        $uploadPath = __DIR__ . '/../' . $b['image_path'];
                        $imagePath = !empty($b['image_path']) && file_exists($uploadPath)
                            ? BASE_URL . $b['image_path']
                            : BASE_URL . 'assets/images/placeholder.jpg';
                    ?>
                    <div class="card-img-wrapper">
                        <img src="<?= $imagePath ?>" class="card-img-top animate-img" alt="Capa do livro <?= htmlspecialchars($b['title']) ?>">
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate" title="<?= htmlspecialchars($b['title']) ?>"><?= htmlspecialchars($b['title']) ?></h5>
                        <p class="card-text text-muted mb-2"><?= htmlspecialchars($b['author']) ?></p>
                        <p class="mt-auto mb-3 fw-bold text-primary">€ <?= number_format($b['price'], 2, ',', '.') ?></p>

                        <a href="<?= BASE_URL ?>pages/book_details.php?id=<?= $b['id'] ?>" 
                           class="btn btn-primary mb-2 w-100 btn-hover animate-img">
                            Ver detalhes
                        </a>

                        <form action="<?= BASE_URL ?>pages/add-to-cart.php" method="POST">
                            <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                            <button class="btn btn-add-cart w-100 btn-hover animate-img" aria-label="Adicionar <?= htmlspecialchars($b['title']) ?> ao carrinho">
                                Adicionar ao Carrinho
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4 fade-in">
            <ul class="pagination justify-content-center">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link btn-hover animate-img" href="?category=<?= $categoryId ?>&page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
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
