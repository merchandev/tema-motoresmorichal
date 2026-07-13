<?php
/**
 * Theme Customizer
 */

function toyota_monagas_customize_register( $wp_customize ) {

    // Hero Slider Section
    $wp_customize->add_section( 'hero_slider_section', array(
        'title'       => __( 'Hero Slider (Inicio)', 'toyota-monagas' ),
        'description' => __( 'Configura los videos e imágenes del slider principal de la página de inicio.', 'toyota-monagas' ),
        'priority'    => 30,
    ) );

    // Generamos controles para 7 slides
    for ( $i = 1; $i <= 7; $i++ ) {

        // DESKTOP MEDIA
        $wp_customize->add_setting( 'home_video_' . $i . '_desktop', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'home_video_' . $i . '_desktop', array(
            'label'       => sprintf( __( 'Slide %d - Multimedia Escritorio (Video/Imagen)', 'toyota-monagas' ), $i ),
            'description' => __( 'Recomendado formato horizontal (ej. 1920x1080).', 'toyota-monagas' ),
            'section'     => 'hero_slider_section',
        ) ) );

        // MOBILE MEDIA
        $wp_customize->add_setting( 'home_video_' . $i . '_mobile', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'home_video_' . $i . '_mobile', array(
            'label'       => sprintf( __( 'Slide %d - Multimedia Móvil (Video/Imagen)', 'toyota-monagas' ), $i ),
            'description' => __( 'Recomendado formato vertical (ej. 1080x1920). Si se deja vacío, se usará el de escritorio.', 'toyota-monagas' ),
            'section'     => 'hero_slider_section',
        ) ) );
    }

}
add_action( 'customize_register', 'toyota_monagas_customize_register' );
