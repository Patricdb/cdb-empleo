<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register admin menu and page for message configuration.
 */
function cdb_empleo_mensajes_admin_menu() {
    // Ensure main menu exists
    add_menu_page(
        __( 'CdB Empleo', 'cdb-empleo' ),
        __( 'CdB Empleo', 'cdb-empleo' ),
        'manage_options',
        'cdb-empleo',
        '__return_null',
        'dashicons-id',
        26
    );

    add_submenu_page(
        'cdb-empleo',
        __( 'Configuración de Mensajes y Avisos', 'cdb-empleo' ),
        __( 'Mensajes', 'cdb-empleo' ),
        'manage_options',
        'cdb-empleo-config-mensajes',
        'cdb_empleo_config_mensajes_page'
    );
}
add_action( 'admin_menu', 'cdb_empleo_mensajes_admin_menu' );

/**
 * Enqueue assets for the configuration page.
 */
function cdb_empleo_mensajes_admin_enqueue( $hook ) {
    if ( 'cdb-empleo_page_cdb-empleo-config-mensajes' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'cdb-empleo-mensajes', CDB_EMPLEO_URL . 'assets/css/config-mensajes.css', array(), '1.0.0' );
    wp_enqueue_script( 'cdb-empleo-config-mensajes', CDB_EMPLEO_URL . 'assets/js/config-mensajes.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'cdb_empleo_mensajes_admin_enqueue' );

/**
 * Render the configuration page.
 */
function cdb_empleo_config_mensajes_page() {
    $mensajes_defaults = cdb_empleo_get_mensajes_defaults();
    $tipos             = cdb_empleo_get_tipos_color();

    // Map of keys to labels
    $mensajes = array(
        'login_requerido'     => __( 'Login requerido', 'cdb-empleo' ),
        'ya_suscrito'         => __( 'Ya suscrito', 'cdb-empleo' ),
        'suscripcion_ok'      => __( 'Suscripción correcta', 'cdb-empleo' ),
        'suscripcion_eliminada' => __( 'Suscripción eliminada', 'cdb-empleo' ),
        'sin_ofertas'         => __( 'Sin ofertas', 'cdb-empleo' ),
        'no_suscrito'         => __( 'Sin suscripción', 'cdb-empleo' ),
        'sin_suscripciones'   => __( 'Sin suscripciones', 'cdb-empleo' ),
        'sin_permiso_form'    => __( 'Sin permisos', 'cdb-empleo' ),
        'campos_requeridos'   => __( 'Campos requeridos', 'cdb-empleo' ),
        'fecha_invalida'      => __( 'Fecha inválida', 'cdb-empleo' ),
        'error_generico'      => __( 'Error genérico', 'cdb-empleo' ),
        'error_solicitud'     => __( 'Error de solicitud', 'cdb-empleo' ),
        'oferta_registrada'   => __( 'Oferta registrada', 'cdb-empleo' ),
        'error_crear_oferta'  => __( 'Error al crear oferta', 'cdb-empleo' ),
    );

    if ( isset( $_POST['cdb_empleo_config_mensajes_nonce'] ) && wp_verify_nonce( $_POST['cdb_empleo_config_mensajes_nonce'], 'cdb_empleo_config_mensajes_save' ) ) {
        foreach ( $mensajes as $clave => $label ) {
            $texto      = isset( $_POST['cdb_empleo_mensaje_' . $clave] ) ? sanitize_text_field( $_POST['cdb_empleo_mensaje_' . $clave] ) : '';
            $secundario = isset( $_POST['cdb_empleo_mensaje_' . $clave . '_secundario'] ) ? sanitize_text_field( $_POST['cdb_empleo_mensaje_' . $clave . '_secundario'] ) : '';
            $tipo       = isset( $_POST['cdb_empleo_color_' . $clave] ) ? sanitize_text_field( $_POST['cdb_empleo_color_' . $clave] ) : 'aviso';
            $mostrar    = isset( $_POST['cdb_empleo_mensaje_' . $clave . '_mostrar'] ) ? 1 : 0;

            update_option( 'cdb_empleo_mensaje_' . $clave, $texto );
            update_option( 'cdb_empleo_mensaje_' . $clave . '_secundario', $secundario );
            update_option( 'cdb_empleo_color_' . $clave, $tipo );
            update_option( 'cdb_empleo_mensaje_' . $clave . '_mostrar', $mostrar );
        }
        echo '<div class="updated"><p>' . esc_html__( 'Ajustes guardados.', 'cdb-empleo' ) . '</p></div>';
    }

    echo '<div class="wrap"><h1>' . esc_html__( 'Configuración de Mensajes y Avisos', 'cdb-empleo' ) . '</h1>';
    echo '<form method="post">';
    wp_nonce_field( 'cdb_empleo_config_mensajes_save', 'cdb_empleo_config_mensajes_nonce' );

    foreach ( $mensajes as $clave => $label ) {
        $def   = $mensajes_defaults[ $clave ];
        $texto = get_option( 'cdb_empleo_mensaje_' . $clave, $def['texto'] );
        $sec   = get_option( 'cdb_empleo_mensaje_' . $clave . '_secundario', $def['secundario'] );
        $tipo  = get_option( 'cdb_empleo_color_' . $clave, $def['tipo'] );
        $mostrar = get_option( 'cdb_empleo_mensaje_' . $clave . '_mostrar', $def['mostrar'] );
        echo '<div class="cdb-mensaje-row">';
        echo '<h2>' . esc_html( $label ) . '</h2>';
        echo '<div class="cdb-aviso-preview" id="preview_' . esc_attr( $clave ) . '">' . cdb_empleo_get_mensaje( $clave ) . '</div>';
        echo '<p><label>' . esc_html__( 'Texto principal', 'cdb-empleo' ) . '<br><input type="text" class="cdb-mensaje-input" data-preview="preview_' . esc_attr( $clave ) . '" name="cdb_empleo_mensaje_' . esc_attr( $clave ) . '" value="' . esc_attr( $texto ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Texto secundario', 'cdb-empleo' ) . '<br><input type="text" class="cdb-mensaje-input" data-preview="preview_' . esc_attr( $clave ) . '" name="cdb_empleo_mensaje_' . esc_attr( $clave ) . '_secundario" value="' . esc_attr( $sec ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Tipo / Color', 'cdb-empleo' ) . '<br><select name="cdb_empleo_color_' . esc_attr( $clave ) . '">';
        foreach ( $tipos as $slug => $t ) {
            echo '<option value="' . esc_attr( $slug ) . '" ' . selected( $tipo, $slug, false ) . '>' . esc_html( $t['nombre'] ) . '</option>';
        }
        echo '</select></label></p>';
        echo '<p><label><input type="checkbox" class="cdb-mostrar-checkbox" data-preview="preview_' . esc_attr( $clave ) . '" name="cdb_empleo_mensaje_' . esc_attr( $clave ) . '_mostrar" value="1" ' . checked( $mostrar, 1, false ) . ' /> ' . esc_html__( 'Mostrar aviso', 'cdb-empleo' ) . '</label></p>';
        echo '</div>';
    }

    submit_button();
    echo '</form></div>';
}
?>
