<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Encola los estilos y scripts necesarios para el frontend del plugin.
 */
function cdb_empleo_enqueue_scripts() {
    // Encolar la hoja de estilos del plugin.
    wp_enqueue_style(
        'cdb-empleo-style',
        CDB_EMPLEO_URL . 'assets/css/estilo-ofertas.css',
        array(),
        '1.0.0'
    );

    // Encolar el script para funcionalidades personalizadas, como el autocomplete.
    wp_enqueue_script(
        'cdb-empleo-script',
        CDB_EMPLEO_URL . 'assets/js/script-ofertas.js',
        array( 'jquery', 'jquery-ui-autocomplete' ),
        '1.0.0',
        true
    );

    wp_localize_script(
        'cdb-empleo-script',
        'ajaxurl',
        admin_url( 'admin-ajax.php' )
    );

}
add_action( 'wp_enqueue_scripts', 'cdb_empleo_enqueue_scripts' );
