<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="card mx-auto shadow" style="max-width: 600px; position: relative; z-index: 2;">
        <div class="card-body text-center">
            <h2 class="card-title mb-3 text-success">Obrigado pela sua compra!</h2>
            <p class="card-text mb-4">Seu pedido foi realizado com sucesso. Entraremos em contato para a entrega.</p>
            <a href="<?= BASE_URL ?>pages/home.php" 
               class="btn btn-primary" 
               style="position: relative; z-index: 5;">
              Voltar para a Loja
            </a>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


