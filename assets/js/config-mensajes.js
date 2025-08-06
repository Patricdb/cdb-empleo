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

    $('#cdb-add-tipo-color').on('click', function(e){
        e.preventDefault();
        var idx = $('#cdb-tipos-color .cdb-tipo-color-row').length + 1;
        var row = $('<div class="cdb-tipo-color-row"></div>');
        row.append('<span class="cdb-color-swatch" style="background-color:#000000"></span>');
        row.append('<input type="text" name="tipos_color[new_'+idx+'][nombre]" placeholder="'+cdbEmpleoMensajes.nuevoNombre+'" />');
        row.append('<input type="text" name="tipos_color[new_'+idx+'][class]" placeholder="'+cdbEmpleoMensajes.nuevaClase+'" />');
        row.append('<input type="color" name="tipos_color[new_'+idx+'][color]" value="#000000" />');
        row.append('<input type="color" name="tipos_color[new_'+idx+'][text]" value="#ffffff" />');
        row.append('<label><input type="checkbox" name="tipos_color[new_'+idx+'][delete]" value="1" /> '+cdbEmpleoMensajes.eliminar+'</label>');
        $('#cdb-tipos-color').append(row);
    });

    $('#cdb-tipos-color').on('change', 'input[type="checkbox"]', function(){
        $(this).closest('.cdb-tipo-color-row').toggleClass('deleting', this.checked);
    });

    $('#cdb-tipos-color').on('input', 'input[type="color"]', function(){
        var swatch = $(this).closest('.cdb-tipo-color-row').find('.cdb-color-swatch');
        if(swatch.length){
            swatch.css('background-color', $(this).val());
        }
    });
});
