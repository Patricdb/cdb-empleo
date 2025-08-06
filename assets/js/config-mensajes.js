jQuery(document).ready(function($){
    $('.cdb-mensaje-input').on('input', function(){
        var target = $('#' + $(this).data('preview'));
        var html = '<div class="cdb-aviso">' + $(this).val() + '</div>';
        if(target.length){
            var existing = target.find('.cdb-aviso');
            if(existing.length){
                existing.find('.cdb-mensaje-principal').text($(this).val());
            } else {
                target.html(html);
            }
        }
    });
    $('.cdb-mostrar-checkbox').on('change', function(){
        var target = $('#' + $(this).data('preview'));
        if(target.length){
            target.toggle(this.checked);
        }
    });
});
