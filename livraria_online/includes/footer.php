</main>

<footer class="site-footer text-center py-3">
  <div class="container">
    <small>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Todos os direitos reservados.</small>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery e Bibliotecas -->
<script src="<?= BASE_URL ?>/assets/js/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/datatables.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script src="<?= BASE_URL ?>/assets/js/admin.js"></script>

<!-- Scroll reveal automático -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const reveals = document.querySelectorAll('.reveal');
  const revealOnScroll = () => {
    const windowHeight = window.innerHeight;
    reveals.forEach(el => {
      const top = el.getBoundingClientRect().top;
      if(top < windowHeight - 50) {
        el.style.opacity = 1;
        el.style.transform = 'translateY(0)';
      }
    });
  };
  window.addEventListener('scroll', revealOnScroll);
  revealOnScroll();
});
</script>
</body>
</html>

