<?php
require_once __DIR__ . '/../includes/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= SITE_NAME ?> - Painel Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/vendor/fontawesome.css">
<style>
  .navbar-nav .nav-link:hover {
      transform: translateY(-2px);
      transition: transform 0.3s ease;
  }
  .dropdown-menu a.dropdown-item:hover {
      background-color: #f0f0f0;
      transform: translateX(3px);
      transition: all 0.3s ease;
  }
  .navbar .dropdown-menu {
      position: absolute !important;
      z-index: 3000 !important;
  }
</style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="books.php"><?= SITE_NAME ?> Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

        <!-- Link Loja -->
        <li class="nav-item">
          <a class="nav-link" href="../pages/home.php">Loja</a>
        </li>

        <!-- Dropdown Admin -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Olá, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="books.php"><i class="fa fa-book me-2"></i> Livros</a></li>
            <li><a class="dropdown-item" href="categories.php"><i class="fa fa-tags me-2"></i> Categorias</a></li>
            <li><a class="dropdown-item" href="subcategories.php"><i class="fa fa-layer-group me-2"></i> Subcategorias</a></li>
            <li><a class="dropdown-item" href="orders.php"><i class="fa fa-shopping-cart me-2"></i> Pedidos</a></li>
            <li><a class="dropdown-item" href="messages.php"><i class="fa fa-envelope me-2"></i> Mensagens</a></li>
            <li><a class="dropdown-item" href="users.php"><i class="fa fa-users me-2"></i> Usuários</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../pages/logout.php"><i class="fa fa-sign-out-alt me-2"></i> Sair</a></li>
          </ul>
        </li>

      </ul>
    </div>
  </div>
</nav>

<main class="flex-grow-1 py-4 container">
