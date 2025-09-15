$(document).ready(function(){

    // Carrousel de Imagens
    let current = 0, slides = $('.carousel-slide'), total = slides.length, interval;
    function showSlide(i){ slides.hide().eq(i).fadeIn(); }
    function nextSlide(){ current=(current+1)%total; showSlide(current); }
    function prevSlide(){ current=(current-1+total)%total; showSlide(current); }
    $('#next').click(nextSlide); $('#prev').click(prevSlide);
    $('#start').click(()=>{ interval=setInterval(nextSlide,3000); });
    $('#stop').click(()=>{ clearInterval(interval); });
    showSlide(current);

    // Validação de forms
    $('form.validate').submit(function(e){
        let valid=true;
        $(this).find('input, select, textarea').each(function(){
            if($(this).val()===''){ alert('Preencha todos os campos!'); valid=false; return false; }
        });
        if(!valid) e.preventDefault();
    });

    // Filtros dinâmicos - porcaria do AJAX que demorei um século para entender como funcionava 
    // (deveria ter buscado uma alternativa mais fácil.. talvez?)
    $('.filter').change(function(){
        $.post('ajax/filter_books.php', { category: $('#category').val() }, function(resp){
            $('#book-list').html(resp);
        });
    });

    // Confirmação de Exclusão
    $('.delete-btn').click(function(e){
        e.preventDefault();
        if(confirm('Tem certeza que deseja excluir este item?')) window.location.href=$(this).attr('href');
    });

    // Mostrar ou ocultar senha - top!
    $('#toggle-password').click(function(){
        let input=$('#password');
        input.attr('type', input.attr('type')==='password'?'text':'password');
    });

    // data tables
    if($.fn.DataTable){
        $('.datatable').DataTable({ paging:true, searching:true, ordering:true });
    }

    // select2
    if($.fn.select2){ $('.select2').select2({ width:'100%' }); }

    // Notificações toastr
    function notify(type,msg){ if($.toast){ $.toast({ heading:type, text:msg, showHideTransition:'slide', position:'top-right', icon:type.toLowerCase() }); } }
    window.notify=notify; // torna global

    // Datepicker
    if($.fn.datepicker){ $('.datepicker').datepicker({ format:'yyyy-mm-dd', autoclose:true }); }

    // Upload de imagens com preview - totalmente desnecessário? sim, ficou bom? penso que sim.
    $('.image-upload').change(function(){
        if(this.files && this.files[0]){
            let reader=new FileReader();
            reader.onload=e=>{ $('#preview').attr('src', e.target.result); }
            reader.readAsDataURL(this.files[0]);
        }
    });

     // Animação fade-in
    document.addEventListener('DOMContentLoaded', function() {
    const reveals = document.querySelectorAll('.reveal');

    function revealElements() {
        reveals.forEach(el => {
            const windowHeight = window.innerHeight;
            const elementTop = el.getBoundingClientRect().top;
            const revealPoint = 150; // ajustar o ponto de trigger ******

            if (elementTop < windowHeight - revealPoint) {
                el.classList.add('active-reveal');
            }
        });
    }

    // Inicial
    revealElements();

    // Ao rolar a página
    window.addEventListener('scroll', revealElements);
});
});
