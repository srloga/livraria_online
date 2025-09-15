<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= SITE_NAME ?></title>
  <!-- Poppin font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Favicon -->
  <link rel="shortcut icon" href="<?= BASE_URL ?>assets/images/icones.png" type="image/png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- CSS Custom -->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/site-style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <script src="<?= BASE_URL ?>/assets/js/script.js" defer></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    /* Navbar hover efeito extra */
    .navbar-nav .nav-link:hover {
        transform: translateY(-2px);
        transition: transform 0.3s ease;
    }
    .dropdown-menu a.dropdown-item:hover {
        background-color: #f0f0f0;
        transform: translateX(3px);
        transition: all 0.3s ease;
    }

    /* Ajuste de dropdown para ficar acima do carrossel */
    .navbar .dropdown-menu {
        position: absolute !important;
        z-index: 3000 !important;
    }

    /* Carrossel abaixo do dropdown */
    .carousel {
        position: relative;
        z-index: 1;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary reveal">
  <div class="container">
    <a class="navbar-brand animate-img" href="<?= BASE_URL ?>pages/home.php"><?= SITE_NAME ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link reveal" href="<?= BASE_URL ?>pages/home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link reveal" href="<?= BASE_URL ?>pages/search.php">Buscar</a></li>
        <li class="nav-item"><a class="nav-link reveal" href="<?= BASE_URL ?>pages/about.php">Sobre</a></li>
        <li class="nav-item"><a class="nav-link reveal" href="<?= BASE_URL ?>pages/contact.php">Contato</a></li>
      </ul>

      <ul class="navbar-nav mb-2 mb-lg-0">
        <?php if(isset($_SESSION['user_id'])): ?>
          <li class="nav-item position-relative reveal">
            <a class="nav-link animate-img" href="<?= BASE_URL ?>pages/cart.php">
              <i class="bi bi-cart-fill"></i> Carrinho
              <?php 
                $cartCount = $_SESSION['cart_count'] ?? 0;
                if($cartCount > 0): ?>
                  <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                    <?= $cartCount ?>
                  </span>
              <?php endif; ?>
            </a>
          </li>

          <li class="nav-item dropdown reveal">
            <a class="nav-link dropdown-toggle animate-img" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Olá, <?= htmlspecialchars($_SESSION['username']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/user_profile.php">
                <i class="fa fa-user me-2"></i> Perfil
              </a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/wishlist.php">
                <i class="fa fa-list me-2"></i> Wishlist
              </a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/user_orders.php">
                <i class="fa fa-list me-2"></i> Meus Pedidos
              </a></li>
              <?php if(!empty($_SESSION['is_admin'])): ?>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/categories.php">
                  <i class="fa fa-cogs me-2"></i> Painel Admin
                </a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>pages/logout.php">
                <i class="fa fa-sign-out-alt me-2"></i> Sair
              </a></li>
            </ul>
          </li>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link reveal" href="<?= BASE_URL ?>pages/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link reveal" href="<?= BASE_URL ?>pages/register.php">Registrar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main>
