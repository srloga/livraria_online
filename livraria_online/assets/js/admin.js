// assets/js/admin.js
(function($){

    // Sidebar treeview
    function initSidebarNav(){
      $('.sidebar-menu li.treeview > a').click(function(e){
        e.preventDefault();
        const $li = $(this).parent();
        $li.toggleClass('active')
           .find('.treeview-menu').slideToggle();
      });
    }
  
    // Inicializar DataTables nas tabelas do admin
    function initDataTables(){
      $('.datatable').DataTable({
        pageLength: 10,
        lengthChange: false,
        ordering:  true,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json'
        }
      });
    }
  
    // Inicializar Select2 em selects - provavelmente vou esquecer disto..
    function initSelect2(){
      $('.select2').select2({
        width: '100%',
        placeholder: 'Selecione…',
        allowClear: true
      });
    }
  
    // Confirm antes de eliminar (categorias, livros, usuários, etc)
    function bindAdminDelete(){
      $(document).on('click', '.btn-delete', function(e){
        if(!confirm('Tem a certeza que quer eliminar este registo?')){
          e.preventDefault();
        }
      });
    }
  
    $(function(){
      initSidebarNav();
      initDataTables();
      initSelect2();
      bindAdminDelete();
    });
  
  })(jQuery);
  