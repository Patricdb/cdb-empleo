jQuery(document).ready(function($) {
    // Flag global para evitar doble envío
    var isSubmitting = false;
    var mensajes = window.cdbEmpleoMensajes || {};
    
    // Inicializar Autocomplete para el campo "Bar"
    if ($('#bar-search').length) {
        var baresData = window.cdbBaresData || [];
        $("#bar-search").autocomplete({
            source: baresData,
            select: function(event, ui) {
                $("#bar_id").val(ui.item.id);
            }
        });
    }
    
    // Manejo del envío del formulario de oferta vía AJAX con validación adicional
    $("#cdb_oferta_form").on("submit", function(e) {
        e.preventDefault();
        
        if (isSubmitting) {
            return;
        }
        isSubmitting = true;
        
        var $btn = $(this).find("button[type='submit']");
        $btn.prop("disabled", true);
        
        // Validar que todos los campos requeridos estén completos.
        var requiredFields = [
            "#bar_id",
            "#posicion_id",
            "#tipo_oferta",
            "#fecha_incorporacion",
            "#fecha_fin",
            "#nivel_salarial",
            "#funciones"
        ];
        var valid = true;
        $.each(requiredFields, function(index, selector) {
            if ($(selector).val() === "" || $(selector).val() === null) {
                valid = false;
                return false;
            }
        });
        if (!valid) {
            $("#cdb_oferta_mensaje")
                .attr('class', 'cdb-empleo-mensaje cdb-empleo-mensaje-error')
                .html("<p>" + (mensajes.campos_requeridos || 'Por favor, completa todos los campos requeridos.') + "</p>");
            isSubmitting = false;
            $btn.prop("disabled", false);
            return;
        }
        
        // Validar que la fecha de incorporación sea anterior a la fecha de fin.
        var fechaIncorporacion = $("#fecha_incorporacion").val();
        var fechaFin = $("#fecha_fin").val();
        var dateIncorporacion = new Date(fechaIncorporacion);
        var dateFin = new Date(fechaFin);
        if (dateIncorporacion >= dateFin) {
            $("#cdb_oferta_mensaje")
                .attr('class', 'cdb-empleo-mensaje cdb-empleo-mensaje-error')
                .html("<p>" + (mensajes.fecha_incorrecta || 'La fecha y hora de incorporación debe ser anterior a la fecha y hora de fin.') + "</p>");
            isSubmitting = false;
            $btn.prop("disabled", false);
            return;
        }
        
        // Preparar datos para el envío vía AJAX.
        var formData = new FormData(this);
        $.ajax({
            url: cdbEmpleo.ajaxurl, // definido mediante wp_localize_script
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                isSubmitting = false;
                $btn.prop("disabled", false);
                if (response.success) {
                    $("#cdb_oferta_mensaje")
                        .attr('class', 'cdb-empleo-mensaje cdb-empleo-mensaje-success')
                        .html("<p>" + response.data.message + "</p>");
                    if (response.data.reload) {
                        window.location.reload();
                    }
                } else {
                    var errorMsg = response.message || (response.data && response.data.message) || mensajes.error_generico || 'Ocurrió un error.';
                    $("#cdb_oferta_mensaje")
                        .attr('class', 'cdb-empleo-mensaje cdb-empleo-mensaje-error')
                        .html("<p>Error: " + errorMsg + "</p>");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                isSubmitting = false;
                $btn.prop("disabled", false);
                $("#cdb_oferta_mensaje")
                    .attr('class', 'cdb-empleo-mensaje cdb-empleo-mensaje-error')
                    .html("<p>" + (mensajes.error_solicitud || 'Error en la solicitud.') + "</p>");
            }
        });
    });
});
