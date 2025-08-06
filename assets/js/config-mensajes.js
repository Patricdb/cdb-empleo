jQuery(document).ready(function($){
    $('.cdb-color-field').on('input change', function(){
        $(this).next('.cdb-color-preview').css('background', $(this).val());
    }).trigger('change');
});
