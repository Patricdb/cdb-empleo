<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Default messages and helpers for configurable notices.
 */

function cdb_empleo_get_mensajes_defaults() {
    return array(
        'login_requerido' => array(
            'texto'      => __( 'Debes iniciar sesión para realizar esta acción.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'ya_suscrito' => array(
            'texto'      => __( 'Ya estás suscrito a esta oferta.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'info',
            'mostrar'    => 1,
        ),
        'suscripcion_ok' => array(
            'texto'      => __( '¡Te has suscrito correctamente a la oferta!', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'exito',
            'mostrar'    => 1,
        ),
        'suscripcion_eliminada' => array(
            'texto'      => __( 'Has eliminado tu suscripción a la oferta.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'info',
            'mostrar'    => 1,
        ),
        'no_suscrito' => array(
            'texto'      => __( 'No estás suscrito a ninguna oferta de empleo.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'info',
            'mostrar'    => 1,
        ),
        'sin_ofertas' => array(
            'texto'      => __( 'No hay ofertas disponibles.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'sin_suscripciones' => array(
            'texto'      => __( 'No hay suscripciones para esta oferta.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'sin_permiso_form' => array(
            'texto'      => __( 'No tienes permisos para realizar esta acción.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'campos_requeridos' => array(
            'texto'      => __( 'Por favor, completa todos los campos requeridos.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'fecha_invalida' => array(
            'texto'      => __( 'La fecha y hora de incorporación debe ser anterior a la fecha y hora de fin.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'error_generico' => array(
            'texto'      => __( 'Ocurrió un error.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'error_solicitud' => array(
            'texto'      => __( 'Error en la solicitud.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
        'oferta_registrada' => array(
            'texto'      => __( 'Oferta de empleo registrada exitosamente.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'exito',
            'mostrar'    => 1,
        ),
        'error_crear_oferta' => array(
            'texto'      => __( 'Error al crear la oferta.', 'cdb-empleo' ),
            'secundario' => '',
            'tipo'       => 'aviso',
            'mostrar'    => 1,
        ),
    );
}

/**
 * Calculate a readable text color (black/white) for a given background.
 */
function cdb_empleo_get_contrasting_text_color( $hex ) {
    $hex = ltrim( $hex, '#' );
    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $r   = hexdec( substr( $hex, 0, 2 ) );
    $g   = hexdec( substr( $hex, 2, 2 ) );
    $b   = hexdec( substr( $hex, 4, 2 ) );
    $luma = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
    return ( $luma > 0.5 ) ? '#000000' : '#ffffff';
}

/**
 * Obtain all registered notice types.
 */
function cdb_empleo_get_tipos_color() {
    $defaults = array(
        'aviso' => array(
            'nombre' => __( 'Aviso', 'cdb-empleo' ),
            'class'  => 'cdb-aviso-aviso',
            'color'  => '#f0ad4e',
            'text'   => '#000000',
        ),
        'info' => array(
            'nombre' => __( 'Info', 'cdb-empleo' ),
            'class'  => 'cdb-aviso-info',
            'color'  => '#5bc0de',
            'text'   => '#ffffff',
        ),
        'exito' => array(
            'nombre' => __( 'Éxito', 'cdb-empleo' ),
            'class'  => 'cdb-aviso-exito',
            'color'  => '#5cb85c',
            'text'   => '#ffffff',
        ),
    );

    $tipos = get_option( 'cdb_empleo_tipos_color', array() );
    $tipos = wp_parse_args( $tipos, $defaults );

    foreach ( $tipos as $slug => &$datos ) {
        if ( empty( $datos['class'] ) ) {
            $datos['class'] = 'cdb-aviso-' . $slug;
        }
        if ( empty( $datos['nombre'] ) ) {
            $datos['nombre'] = $slug;
        }
        if ( empty( $datos['text'] ) && ! empty( $datos['color'] ) ) {
            $datos['text'] = cdb_empleo_get_contrasting_text_color( $datos['color'] );
        }
    }

    return $tipos;
}

/**
 * Register a new type/color programmatically.
 */
function cdb_empleo_register_tipo_color( $slug, $args ) {
    if ( empty( $slug ) ) {
        return;
    }
    $tipos = cdb_empleo_get_tipos_color();
    $nuevo = wp_parse_args(
        $args,
        array(
            'nombre' => $slug,
            'class'  => 'cdb-aviso-' . $slug,
            'color'  => '#cccccc',
            'text'   => '',
        )
    );
    if ( empty( $nuevo['text'] ) ) {
        $nuevo['text'] = cdb_empleo_get_contrasting_text_color( $nuevo['color'] );
    }
    $tipos[ $slug ] = $nuevo;
    update_option( 'cdb_empleo_tipos_color', $tipos );
}

/**
 * Get CSS class for a type/color.
 */
function cdb_empleo_get_tipo_color_class( $slug ) {
    $tipos = cdb_empleo_get_tipos_color();
    return isset( $tipos[ $slug ] ) ? $tipos[ $slug ]['class'] : $tipos['aviso']['class'];
}

/**
 * Return only the text of a message, without markup.
 */
function cdb_empleo_get_mensaje_text( $clave, $default = '' ) {
    $defaults = cdb_empleo_get_mensajes_defaults();
    if ( '' === $default && isset( $defaults[ $clave ] ) ) {
        $default = $defaults[ $clave ]['texto'];
    }
    return get_option( 'cdb_empleo_mensaje_' . $clave, $default );
}

/**
 * Build HTML for a message.
 */
function cdb_empleo_get_mensaje( $clave ) {
    $defaults = cdb_empleo_get_mensajes_defaults();
    if ( ! isset( $defaults[ $clave ] ) ) {
        return '';
    }
    $def = $defaults[ $clave ];

    $texto      = get_option( 'cdb_empleo_mensaje_' . $clave, $def['texto'] );
    $secundario = get_option( 'cdb_empleo_mensaje_' . $clave . '_secundario', $def['secundario'] );
    $tipo       = get_option( 'cdb_empleo_color_' . $clave, $def['tipo'] );
    $mostrar    = get_option( 'cdb_empleo_mensaje_' . $clave . '_mostrar', $def['mostrar'] );

    if ( ! $mostrar ) {
        return '';
    }

    $class = cdb_empleo_get_tipo_color_class( $tipo );

    $html  = '<div class="cdb-aviso ' . esc_attr( $class ) . '">';
    $html .= '<span class="cdb-mensaje-principal">' . esc_html( $texto ) . '</span>';
    if ( ! empty( $secundario ) ) {
        $html .= ' <span class="cdb-mensaje-secundario">' . esc_html( $secundario ) . '</span>';
    }
    $html .= '</div>';

    return $html;
}

function cdb_empleo_render_mensaje( $clave ) {
    echo cdb_empleo_get_mensaje( $clave );
}

?>
