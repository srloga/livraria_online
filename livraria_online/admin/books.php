<?php
// Carregar configurações, conexão e autenticação
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// header do admin
include __DIR__ . '/header.php';

$conn = db_connect();
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Deletar livro
if ($action === 'delete' && $id) {
    // apaga imagem também
    $old = $conn->prepare("SELECT image_path FROM books WHERE id=?");
    $old->execute([$id]);
    $oldImage = $old->fetchColumn();
    if ($oldImage && file_exists($oldImage)) unlink($oldImage);

    $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
    $stmt->execute([$id]);
    header("Location: books.php"); exit;
}

// Adicionar / Editar livro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title']; $author = $_POST['author'];
    $description = $_POST['description']; $price = $_POST['price'];
    $category_id = $_POST['category_id']; $subcategory_id = $_POST['subcategory_id'] ?? null;
    $stock = $_POST['stock']; $image = $_FILES['image']['name'] ?? '';
    $target = '';

    // Upload de imagem
    if ($image && $_FILES['image']['tmp_name']) {
        $allowed = ['image/jpeg','image/png','image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        $fileSize = $_FILES['image']['size'];

        if (!in_array($fileType, $allowed)) die("Formato inválido. Use apenas JPG ou PNG.");
        if ($fileSize > $maxSize) die("Arquivo muito grande. Máx: 2MB.");

        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $filename = uniqid('book_') . '.' . strtolower($ext);
        $target = '../assets/uploads/' . $filename;

        if ($id) { // apagar imagem antiga
            $old = $conn->prepare("SELECT image_path FROM books WHERE id=?");
            $old->execute([$id]);
            $oldImage = $old->fetchColumn();
            if ($oldImage && file_exists($oldImage)) unlink($oldImage);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    if ($id) {
        $sql = "UPDATE books SET title=?,author=?,description=?,price=?,category_id=?,subcategory_id=?,stock=?";
        $params = [$title,$author,$description,$price,$category_id,$subcategory_id,$stock];
        if ($target) { $sql .= ", image_path=?"; $params[]=$target; }
        $sql .= " WHERE id=?"; $params[]=$id;
        $stmt=$conn->prepare($sql); $stmt->execute($params);
    } else {
        $stmt=$conn->prepare("INSERT INTO books (title,author,description,price,category_id,subcategory_id,stock,image_path) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$title,$author,$description,$price,$category_id,$subcategory_id,$stock,$target]);
    }
    header("Location: books.php"); exit;
}

// Estou cansadoooooooooooooooo

$book=[];
if ($id && $action==='edit') {
    $stmt=$conn->prepare("SELECT * FROM books WHERE id=?");
    $stmt->execute([$id]);
    $book=$stmt->fetch();
}

// Listagens
$categories=$conn->query("SELECT * FROM categories")->fetchAll();
$subcategories=$conn->query("SELECT * FROM subcategories")->fetchAll();
$books=$conn->query("SELECT b.*,c.name as category_name,s.name as subcategory_name FROM books b JOIN categories c ON b.category_id=c.id LEFT JOIN subcategories s ON b.subcategory_id=s.id ORDER BY b.created_at DESC")->fetchAll();
?>

<div class="container mt-4">
    <h3><?= $id ? 'Editar Livro' : 'Adicionar Novo Livro' ?></h3>
    <form method="POST" enctype="multipart/form-data" class="row g-3 mb-5">
        <input type="hidden" name="id" value="<?= $book['id'] ?? '' ?>">
        <div class="col-md-6"><input type="text" name="title" class="form-control" placeholder="Título" required value="<?= $book['title'] ?? '' ?>"></div>
        <div class="col-md-6"><input type="text" name="author" class="form-control" placeholder="Autor" required value="<?= $book['author'] ?? '' ?>"></div>
        <div class="col-md-12"><textarea name="description" class="form-control" placeholder="Descrição"><?= $book['description'] ?? '' ?></textarea></div>
        <div class="col-md-4"><input type="number" name="price" class="form-control" placeholder="Preço" required value="<?= $book['price'] ?? '' ?>"></div>
        <div class="col-md-4">
            <select name="category_id" class="form-select" required>
                <option value="">Categoria</option>
                <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= isset($book['category_id'])&&$book['category_id']==$cat['id']?'selected':'' ?>><?= $cat['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="subcategory_id" class="form-select">
                <option value="">Subcategoria</option>
                <?php foreach($subcategories as $sub): ?>
                <option value="<?= $sub['id'] ?>" <?= isset($book['subcategory_id'])&&$book['subcategory_id']==$sub['id']?'selected':'' ?>><?= $sub['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3"><input type="number" name="stock" class="form-control" placeholder="Estoque" value="<?= $book['stock'] ?? 0 ?>"></div>
        <div class="col-md-6">
            <input type="file" name="image" class="form-control image-upload">
            <?php if(!empty($book['image_path'])): ?>
                <div class="mt-2">
                    <small>Imagem atual:</small><br>
                    <img src="<?= $book['image_path'] ?>" id="preview" style="max-width:150px;max-height:150px;border:1px solid #ccc;padding:3px;border-radius:5px">
                </div>
            <?php else: ?>
                <img id="preview" style="max-width:150px;max-height:150px;display:none;border:1px solid #ccc;padding:3px;border-radius:5px">
            <?php endif; ?>
        </div>
        <div class="col-md-3"><button type="submit" class="btn btn-success w-100">Salvar</button></div>
    </form>

    <h4>Livros Cadastrados</h4>
    <table class="table table-bordered table-hover datatable">
        <thead><tr><th>ID</th><th>Título</th><th>Autor</th><th>Preço</th><th>Categoria</th><th>Subcategoria</th><th>Estoque</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach($books as $bk): ?>
            <tr>
                <td><?= $bk['id'] ?></td>
                <td><?= $bk['title'] ?></td>
                <td><?= $bk['author'] ?></td>
                <td>€<?= number_format($bk['price'],2,',','.') ?></td>
                <td><?= $bk['category_name'] ?></td>
                <td><?= $bk['subcategory_name'] ?? '-' ?></td>
                <td><?= $bk['stock'] ?></td>
                <td>
                    <a href="books.php?action=edit&id=<?= $bk['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="books.php?action=delete&id=<?= $bk['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>

