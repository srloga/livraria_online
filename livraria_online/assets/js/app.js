// assets/js/app.js
(function($){

    // Debounce helper
    function debounce(fn, delay){
      let timer = null;
      return function(){
        const args = arguments, ctx = this;
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(ctx, args), delay);
      };
    }
  
    // Atualiza contador do carrinho no header
    function updateCartCount(count){
      $('#cart-count').text(count);
    }
  
    // Adicionar ao carrinho via AJAX
    function bindAddToCart(){
      $('.add-to-cart-form').on('submit', function(e){
        e.preventDefault();
        const $form = $(this);
        const bookId = $form.find('input[name="book_id"]').val();
        $.post('<?= BASE_URL ?>/pages/cart.php', { book_id: bookId })
         .done((res) => {
           // espera que o PHP devolva JSON { success, count }
           if(res.success) updateCartCount(res.count);
           alert('Livro adicionado ao carrinho!');
         })
         .fail(() => alert('Erro ao adicionar ao carrinho.'));
      });
    }
  
    // Confirma remoção do carrinho
    function bindRemoveFromCart(){
      $(document).on('click', '.btn-remove-item', function(e){
        if(!confirm('Tem a certeza que quer remover este item?')){
          e.preventDefault();
        }
      });
    }
  
    // Atualiza quantidade via AJAX
    function bindQtyChange(){
      $(document).on('change', '.cart-qty', function(){
        const $input = $(this);
        const itemId = $input.data('item-id');
        const qty    = $input.val();
        $.post('<?= BASE_URL ?>/pages/cart.php', { update_id: itemId, quantity: qty })
         .done((res)=>{
           if(res.success){
             $('#cart-total').text(res.total.toFixed(2)+' €');
           }
         });
      });
    }
  
    // Pesquisa instantânea (filtra os .card na home/search)
    function bindLiveSearch(){
      $('#search-input').on('input', debounce(function(){
        const term = $(this).val().toLowerCase();
        $('.card').each(function(){
          const title = $(this).find('.card-body h2').text().toLowerCase();
          $(this)[ title.indexOf(term) > -1 ? 'show' : 'hide' ]();
        });
      }, 300));
    }
  
    // Alterna visibilidade de password - simples e é algo que eu curto
    function bindTogglePassword(){
      $(document).on('click', '.toggle-password', function(){
        const $pwd = $($(this).data('target'));
        const type = $pwd.attr('type') === 'password' ? 'text' : 'password';
        $pwd.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
      });
    }
  
    // Inicializações gerais
    $(function(){
      bindAddToCart();
      bindRemoveFromCart();
      bindQtyChange();
      bindLiveSearch();
      bindTogglePassword();
    });
  
  })(jQuery);
  