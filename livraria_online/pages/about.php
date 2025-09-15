<?php 
include '../includes/config.php'; 
include '../includes/header.php'; 
?>

<div class="container my-5">

    <!-- Cabeçalho da página -->
    <div class="text-center mb-5 fade-in">
        <h2 class="fw-bold">Sobre Nós</h2>
        <p class="text-muted">Conheça nossa história e missão na Livraria Ramos</p>
    </div>

    <!-- Introdução -->
    <div class="row align-items-center mb-5 fade-in">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card-livro shadow-sm d-flex justify-content-center align-items-center">
                <img src="<?= BASE_URL ?>assets/images/logo.png" class="img-fluid animate-img" alt="Livros">
            </div>
        </div>
        <div class="col-md-6">
            <p class="lead">
                Bem-vindo à <strong>Livraria Ramos</strong>! Somos apaixonados por livros e tecnologia, e nosso objetivo é tornar a leitura acessível a todos.
            </p>
            <p>
                Desde a nossa fundação, oferecemos uma grande variedade de títulos — desde clássicos da literatura até os lançamentos técnicos mais procurados.
            </p>
        </div>
    </div>

    <!-- Diferenciais -->
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-4 fade-in">
            <div class="card card-livro border-0 shadow-sm h-100 py-4 card-hover">
                <div class="card-body">
                    <i class="fas fa-book fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title fw-bold">Variedade de Livros</h5>
                    <p class="card-text">Títulos de todos os gêneros, clássicos e novidades para todos os leitores.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 fade-in" style="animation-delay: 0.2s;">
            <div class="card card-livro border-0 shadow-sm h-100 py-4 card-hover">
                <div class="card-body">
                    <i class="fas fa-shield-alt fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title fw-bold">Compra Segura</h5>
                    <p class="card-text">Transações protegidas e entregas confiáveis no conforto da sua casa.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 fade-in" style="animation-delay: 0.4s;">
            <div class="card card-livro border-0 shadow-sm h-100 py-4 card-hover">
                <div class="card-body">
                    <i class="fas fa-star fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title fw-bold">Suporte e Recomendação</h5>
                    <p class="card-text">Equipe dedicada a ajudar você a encontrar o livro perfeito.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Encerramento -->
    <div class="text-center fade-in" style="animation-delay: 0.6s;">
        <p class="lead mb-1">Agradecemos sua visita e desejamos ótimas leituras!</p>
        <a href="<?= BASE_URL ?>pages/home.php" class="btn btn-primary mt-3 btn-hover">Voltar à Loja</a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
