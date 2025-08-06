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

    var table = $('#cdb-tipos-color tbody');
    $('#cdb-add-color-row').on('click', function(e){
        e.preventDefault();
        var index = table.find('tr').length;
        var row = $('<tr />');
        row.append('<td><input type="text" name="tipos_color[' + index + '][slug]" /></td>');
        row.append('<td><input type="text" name="tipos_color[' + index + '][nombre]" /></td>');
        row.append('<td><input type="text" name="tipos_color[' + index + '][class]" /></td>');
        row.append('<td><input type="color" name="tipos_color[' + index + '][color]" value="#ffffff" /></td>');
        row.append('<td><input type="color" name="tipos_color[' + index + '][text]" value="#000000" /></td>');
        row.append('<td><button type="button" class="button cdb-remove-row">&times;</button></td>');
        table.append(row);
    });

    $('#cdb-tipos-color').on('click', '.cdb-remove-row', function(){
        $(this).closest('tr').remove();
    });
});
