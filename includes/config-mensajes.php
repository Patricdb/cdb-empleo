<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registro de tipos de avisos con color asociado.
 */
$__cdb_empleo_tipos_color = array();

/**
 * Devuelve los tipos de avisos registrados.
 *
 * @return array
 */
function cdb_empleo_get_tipos_color() {
    global $__cdb_empleo_tipos_color;
    return $__cdb_empleo_tipos_color;
}

/**
 * Registra un nuevo tipo de aviso con color.
 *
 * @param string $slug Identificador del tipo.
 * @param array  $args Argumentos del tipo (label y color por defecto).
 */
function cdb_empleo_register_tipo_color( $slug, $args = array() ) {
    global $__cdb_empleo_tipos_color;

    $defaults = array(
        'label' => ucfirst( $slug ),
        'color' => '#000000',
    );

    $args = wp_parse_args( $args, $defaults );

    $__cdb_empleo_tipos_color[ $slug ] = $args;
}

/**
 * Recupera un mensaje configurable.
 *
 * @param string $slug    Clave del mensaje.
 * @param string $default Valor por defecto.
 *
 * @return string
 */
function cdb_empleo_get_mensaje( $slug, $default = '' ) {
    $mostrar = get_option( 'cdb_empleo_mostrar_' . $slug, '1' );
    if ( '1' !== $mostrar ) {
        return '';
    }

    return get_option( 'cdb_empleo_mensaje_' . $slug, $default );
}

/**
 * Renderiza un mensaje configurado.
 *
 * @param string  $slug    Clave del mensaje.
 * @param string  $tipo    Tipo/color del mensaje.
 * @param string  $default Texto por defecto.
 * @param boolean $echo    Si se imprime directamente.
 *
 * @return string|null
 */
function cdb_empleo_render_mensaje( $slug, $tipo = 'info', $default = '', $echo = true ) {
    $mensaje = cdb_empleo_get_mensaje( $slug, $default );
    if ( '' === $mensaje ) {
        return null;
    }

    $html = '<div class="cdb-empleo-mensaje cdb-empleo-mensaje-' . esc_attr( $tipo ) . '">' . esc_html( $mensaje ) . '</div>';
    if ( $echo ) {
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    return $html;
}

/**
 * Página de configuración de mensajes.
 */
function cdb_empleo_config_mensajes_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $tipos = cdb_empleo_get_tipos_color();

    if (
        isset( $_POST['cdb_empleo_config_mensajes_nonce'] ) &&
        wp_verify_nonce( $_POST['cdb_empleo_config_mensajes_nonce'], 'cdb_empleo_config_mensajes' )
    ) {
        foreach ( $tipos as $slug => $args ) {
            $mensaje = isset( $_POST[ 'cdb_empleo_mensaje_' . $slug ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'cdb_empleo_mensaje_' . $slug ] ) ) : '';
            update_option( 'cdb_empleo_mensaje_' . $slug, $mensaje );

            $color = isset( $_POST[ 'cdb_empleo_color_' . $slug ] ) ? sanitize_hex_color( $_POST[ 'cdb_empleo_color_' . $slug ] ) : '';
            if ( empty( $color ) && isset( $args['color'] ) ) {
                $color = $args['color'];
            }
            update_option( 'cdb_empleo_color_' . $slug, $color );

            $mostrar = isset( $_POST[ 'cdb_empleo_mostrar_' . $slug ] ) ? '1' : '0';
            update_option( 'cdb_empleo_mostrar_' . $slug, $mostrar );
        }
        echo '<div class="updated"><p>' . esc_html__( 'Configuración guardada.', 'cdb-empleo' ) . '</p></div>';
    }

    echo '<div class="wrap cdb-empleo-config-mensajes">';
    echo '<h1>' . esc_html__( 'Configuración de mensajes', 'cdb-empleo' ) . '</h1>';
    echo '<form method="post">';
    wp_nonce_field( 'cdb_empleo_config_mensajes', 'cdb_empleo_config_mensajes_nonce' );
    foreach ( $tipos as $slug => $args ) {
        $mensaje = get_option( 'cdb_empleo_mensaje_' . $slug, '' );
        $color   = get_option( 'cdb_empleo_color_' . $slug, isset( $args['color'] ) ? $args['color'] : '' );
        $mostrar = get_option( 'cdb_empleo_mostrar_' . $slug, '1' );

        echo '<div class="cdb-mensaje-row">';
        echo '<h2>' . esc_html( $args['label'] ) . '</h2>';
        echo '<p><label><input type="checkbox" name="cdb_empleo_mostrar_' . esc_attr( $slug ) . '" value="1" ' . checked( $mostrar, '1', false ) . ' /> ' . esc_html__( 'Mostrar mensaje', 'cdb-empleo' ) . '</label></p>';
        echo '<p><label>' . esc_html__( 'Mensaje', 'cdb-empleo' ) . '</label><br />';
        echo '<input type="text" class="regular-text" name="cdb_empleo_mensaje_' . esc_attr( $slug ) . '" value="' . esc_attr( $mensaje ) . '" /></p>';
        echo '<p><label>' . esc_html__( 'Color', 'cdb-empleo' ) . '</label><br />';
        echo '<input type="text" class="cdb-color-field" name="cdb_empleo_color_' . esc_attr( $slug ) . '" value="' . esc_attr( $color ) . '" />';
        echo '<span class="cdb-color-preview" style="background:' . esc_attr( $color ) . '"></span></p>';
        echo '</div>';
    }
    submit_button();
    echo '</form></div>';
}

/**
 * Registra la página de configuración.
 */
function cdb_empleo_config_mensajes_menu() {
    add_options_page(
        __( 'Mensajes CdB Empleo', 'cdb-empleo' ),
        __( 'Mensajes CdB Empleo', 'cdb-empleo' ),
        'manage_options',
        'cdb-empleo-config-mensajes',
        'cdb_empleo_config_mensajes_page'
    );
}
add_action( 'admin_menu', 'cdb_empleo_config_mensajes_menu' );

/**
 * Encola los recursos de la página de configuración.
 */
function cdb_empleo_config_mensajes_admin_assets( $hook ) {
    if ( 'settings_page_cdb-empleo-config-mensajes' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'cdb-empleo-config-mensajes', CDB_EMPLEO_URL . 'assets/css/config-mensajes.css', array(), '1.0.0' );
    wp_enqueue_script( 'cdb-empleo-config-mensajes', CDB_EMPLEO_URL . 'assets/js/config-mensajes.js', array( 'jquery' ), '1.0.0', true );

    $css = cdb_empleo_generate_colors_css();
    if ( $css ) {
        wp_add_inline_style( 'cdb-empleo-config-mensajes', $css );
    }
}
add_action( 'admin_enqueue_scripts', 'cdb_empleo_config_mensajes_admin_assets' );

/**
 * Genera CSS para los tipos de aviso configurados.
 *
 * @return string
 */
function cdb_empleo_generate_colors_css() {
    $tipos = cdb_empleo_get_tipos_color();
    $css   = '';
    foreach ( $tipos as $slug => $args ) {
        $color = get_option( 'cdb_empleo_color_' . $slug, isset( $args['color'] ) ? $args['color'] : '#000' );
        $css  .= '.cdb-empleo-mensaje-' . $slug . '{border-left:4px solid ' . esc_attr( $color ) . ';background-color:' . esc_attr( $color ) . ';color:#fff;}' . "\n";
    }
    if ( $css ) {
        $css = '.cdb-empleo-mensaje{margin-top:20px;padding:10px;}' . "\n" . $css;
    }
    return $css;
}

/**
 * Encola el CSS dinámico y expone mensajes al frontend.
 */
function cdb_empleo_mensajes_frontend_assets() {
    $css = cdb_empleo_generate_colors_css();
    if ( $css ) {
        wp_add_inline_style( 'cdb-empleo-style', $css );
    }

    $mensajes = array(
        'campos_requeridos' => cdb_empleo_get_mensaje( 'campos_requeridos', __( 'Por favor, completa todos los campos requeridos.', 'cdb-empleo' ) ),
        'fecha_incorrecta'  => cdb_empleo_get_mensaje( 'fecha_incorrecta', __( 'La fecha y hora de incorporación debe ser anterior a la fecha y hora de fin.', 'cdb-empleo' ) ),
        'error_solicitud'   => cdb_empleo_get_mensaje( 'error_solicitud', __( 'Error en la solicitud.', 'cdb-empleo' ) ),
    );

    wp_localize_script( 'cdb-empleo-script', 'cdbEmpleoMensajes', $mensajes );
}
add_action( 'wp_enqueue_scripts', 'cdb_empleo_mensajes_frontend_assets', 20 );

// Registrar algunos tipos por defecto.
cdb_empleo_register_tipo_color(
    'exito',
    array(
        'label' => __( 'Éxito', 'cdb-empleo' ),
        'color' => '#28a745',
    )
);
cdb_empleo_register_tipo_color(
    'aviso',
    array(
        'label' => __( 'Aviso', 'cdb-empleo' ),
        'color' => '#ffc107',
    )
);
cdb_empleo_register_tipo_color(
    'error',
    array(
        'label' => __( 'Error', 'cdb-empleo' ),
        'color' => '#dc3545',
    )
);

?>
