<?php
require_once __DIR__ . '/../includes/config.php';
$pdo = db_connect();
include __DIR__ . '/../includes/header.php';

// "Pega" parâmetros da busca e filtros
$searchTerm = trim($_GET['q'] ?? '');
$categoryId = (int)($_GET['category'] ?? 0);
$subcategoryId = (int)($_GET['subcategory'] ?? 0);
$priceMin   = (float)($_GET['price_min'] ?? 0);
$priceMax   = (float)($_GET['price_max'] ?? 0);
$availability = $_GET['availability'] ?? '';
$orderBy = $_GET['order_by'] ?? 'recent';

// Busca categorias e subcategorias
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$subcategories = [];
if ($categoryId > 0) {
    $stmtSub = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name");
    $stmtSub->execute([$categoryId]);
    $subcategories = $stmtSub->fetchAll(PDO::FETCH_ASSOC);
}

// Monta query base
$sql = "SELECT b.*, c.name AS category 
        FROM books b 
        JOIN categories c ON b.category_id = c.id 
        WHERE 1=1";
$params = [];

// Filtros
if ($searchTerm !== '') { 
    $sql .= " AND (b.title LIKE ? OR b.author LIKE ? OR c.name LIKE ?)";
    $like = "%$searchTerm%";
    $params = array_merge($params, [$like, $like, $like]);
}
if ($categoryId > 0) { $sql .= " AND b.category_id = ?"; $params[] = $categoryId; }
if ($subcategoryId > 0) { $sql .= " AND b.subcategory_id = ?"; $params[] = $subcategoryId; }
if ($priceMin > 0) { $sql .= " AND b.price >= ?"; $params[] = $priceMin; }
if ($priceMax > 0) { $sql .= " AND b.price <= ?"; $params[] = $priceMax; }
if ($availability === 'in') { $sql .= " AND b.stock > 0"; }
if ($availability === 'out') { $sql .= " AND b.stock <= 0"; }

// Ordenação
switch ($orderBy) {
    case 'oldest': $sql .= " ORDER BY b.created_at ASC"; break;
    case 'cheap': $sql .= " ORDER BY b.price ASC"; break;
    case 'expensive': $sql .= " ORDER BY b.price DESC"; break;
    case 'author': $sql .= " ORDER BY b.author ASC"; break;
    default: $sql .= " ORDER BY b.created_at DESC"; // recent
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Wishlist do usuário 
$wishlistIds = [];
if (isset($currentUserId)) {
    $wstmt = $pdo->prepare("SELECT book_id FROM wishlist WHERE user_id = ?");
    $wstmt->execute([$currentUserId]);
    $wishlistIds = $wstmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div class="container my-5 fade-in">
    <div class="row">

        <!-- Sidebar categorias -->
        <div class="col-md-3 mb-4 fade-in">
            <div class="list-group animate-img">
                <a href="<?= BASE_URL ?>pages/search.php" class="list-group-item list-group-item-action <?= !$categoryId ? 'active' : '' ?>">Todas as categorias</a>
                <?php foreach($categories as $cat): ?>
                    <a href="<?= BASE_URL ?>pages/search.php?category=<?= $cat['id'] ?>" class="list-group-item list-group-item-action <?= $categoryId === (int)$cat['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($subcategories)): ?>
            <div class="mt-3 list-group animate-img">
                <span class="list-group-item active">Subcategorias</span>
                <?php foreach($subcategories as $sub): ?>
                    <a href="<?= BASE_URL ?>pages/search.php?category=<?= $categoryId ?>&subcategory=<?= $sub['id'] ?>" class="list-group-item list-group-item-action <?= $subcategoryId === (int)$sub['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($sub['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Conteúdo de livros -->
        <div class="col-md-9 fade-in">
            <!-- Filtros -->
            <form method="GET" class="row g-2 mb-4 animate-img">
                <input type="hidden" name="category" value="<?= $categoryId ?>">
                <input type="hidden" name="subcategory" value="<?= $subcategoryId ?>">

                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="Título, autor ou categoria" value="<?= htmlspecialchars($searchTerm) ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" step="1.00" name="price_min" class="form-control" placeholder="Preço mín." value="<?= $priceMin ?: '' ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" step="1.00" name="price_max" class="form-control" placeholder="Preço máx." value="<?= $priceMax ?: '' ?>">
                </div>
                <div class="col-md-2">
                    <select name="availability" class="form-select">
                        <option value="">Disponibilidade</option>
                        <option value="in" <?= $availability==='in'?'selected':'' ?>>Em estoque</option>
                        <option value="out" <?= $availability==='out'?'selected':'' ?>>Esgotado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="order_by" class="form-select">
                        <option value="recent" <?= $orderBy==='recent'?'selected':'' ?>>Mais recentes</option>
                        <option value="oldest" <?= $orderBy==='oldest'?'selected':'' ?>>Mais antigos</option>
                        <option value="cheap" <?= $orderBy==='cheap'?'selected':'' ?>>Mais baratos</option>
                        <option value="expensive" <?= $orderBy==='expensive'?'selected':'' ?>>Mais caros</option>
                        <option value="author" <?= $orderBy==='author'?'selected':'' ?>>Autor A-Z</option>
                    </select>
                </div>
                <div class="col-12 d-grid mt-2">
                    <button class="btn btn-primary btn-hover animate-img">Filtrar</button>
                </div>
            </form>

            <!-- Lista de livros -->
            <?php if (empty($books)): ?>
                <div class="alert alert-warning text-center fade-in animate-img">Nenhum livro encontrado.</div>
            <?php else: ?>
                <div class="row g-4 fade-in">
                    <?php foreach ($books as $book): ?>
                        <?php 
                            $uploadPath = __DIR__ . '/../' . $book['image_path'];
                            $imagePath = !empty($book['image_path']) && file_exists($uploadPath)
                                ? BASE_URL . $book['image_path']
                                : BASE_URL . 'assets/images/placeholder.jpg';
                            $isFav = in_array($book['id'], $wishlistIds);
                        ?>
                        <div class="col-sm-6 col-md-4 col-lg-3 fade-in">
                            <div class="card card-livro h-100 shadow-sm position-relative animate-img">
                                <div class="card-img-wrapper">
                                    <img src="<?= $imagePath ?>" class="card-img-top animate-img" alt="Capa do livro <?= htmlspecialchars($book['title']) ?>">
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-truncate animate-img" title="<?= htmlspecialchars($book['title']) ?>"><?= htmlspecialchars($book['title']) ?></h5>
                                    <p class="card-text text-muted mb-2 animate-img"><?= htmlspecialchars($book['author']) ?></p>
                                    <p class="card-text text-success fw-bold mb-3 animate-img">€ <?= number_format($book['price'], 2, ',', '.') ?></p>
                                    <a href="<?= BASE_URL ?>pages/book_details.php?id=<?= $book['id'] ?>" class="btn btn-primary mb-2 w-100 btn-hover animate-img">Ver detalhes</a>
                                    <form action="<?= BASE_URL ?>pages/add-to-cart.php" method="POST" class="mt-auto animate-img">
                                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                        <button class="btn btn-add-cart w-100 btn-hover animate-img">Adicionar ao Carrinho</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>

