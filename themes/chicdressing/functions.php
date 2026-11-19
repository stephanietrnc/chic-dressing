<?php 

add_action( 'wp_enqueue_scripts', 'chicdressing_enqueue_styles' );
function chicdressing_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

// (retiré) add_filter( 'big_image_size_threshold', '__return_false' );
// Ce filtre empêchait WordPress de réduire les photos trop grandes à l'envoi (> 2560 px).


// Désactiver les Google Fonts du thème parent
function chic_disable_google_fonts() {
    wp_dequeue_style( 'ashe-playfair-font' );
    wp_dequeue_style( 'ashe-opensans-font' );
    wp_dequeue_style( 'ashe-kalam-font' );
    wp_dequeue_style( 'ashe-rokkitt-font' );
}
add_action( 'wp_enqueue_scripts', 'chic_disable_google_fonts', 20 );


// ---------------------------------------------------------------
// Optimisations complémentaires (novembre 2026)
// ---------------------------------------------------------------

// Émojis WordPress : script et styles inutiles (les navigateurs affichent déjà les émojis)
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );

// Image du header : une taille adaptée à l'écran au lieu de l'image originale (2250 px)
function chic_header_image_urls() {
    $url = get_header_image();
    if ( ! $url ) {
        return array();
    }
    // 1. l'identifiant enregistré avec l'image d'en-tête (le plus fiable)
    $header = get_custom_header();
    $id     = ! empty( $header->attachment_id ) ? (int) $header->attachment_id : 0;
    // 2. sinon, on retrouve l'image à partir de son adresse
    if ( ! $id ) {
        $id = attachment_url_to_postid( $url );
    }
    // 3. sinon, même chose sans le suffixe « -scaled » ajouté par WordPress
    if ( ! $id ) {
        $id = attachment_url_to_postid( str_replace( '-scaled.', '.', $url ) );
    }
    if ( ! $id ) {
        return array();
    }
    $mobile  = wp_get_attachment_image_url( $id, 'medium_large' ); // 768 px
    $desktop = wp_get_attachment_image_url( $id, '1536x1536' );    // 1536 px
    return ( $mobile && $desktop ) ? array( 'mobile' => $mobile, 'desktop' => $desktop ) : array();
}

add_action( 'wp_enqueue_scripts', function () {
    $img = chic_header_image_urls();
    if ( ! $img ) {
        return;
    }
    $css = '.entry-header{background-image:url("' . esc_url( $img['desktop'] ) . '")!important}'
         . '@media (max-width:768px){.entry-header{background-image:url("' . esc_url( $img['mobile'] ) . '")!important}}';
    wp_add_inline_style( 'ashe-style', $css );
}, 30 );

// Préchargement de l'image du header (le navigateur la découvre sinon tard, dans le CSS)
add_action( 'wp_head', function () {
    $img = chic_header_image_urls();
    if ( ! $img ) {
        return;
    }
    echo '<link rel="preload" as="image" href="' . esc_url( $img['mobile'] ) . '" media="(max-width: 768px)">' . "\n";
    echo '<link rel="preload" as="image" href="' . esc_url( $img['desktop'] ) . '" media="(min-width: 769px)">' . "\n";
}, 1 );

// Boutique : titres des produits en H3 (ils sont sous le titre H2 de la section)
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', function () {
    $classes = apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' );
    echo '<h3 class="' . esc_attr( $classes ) . '">' . wp_kses_post( get_the_title() ) /* garde les <br> des titres */ . '</h3>';
}, 10 );

// Font Awesome : la feuille complète (100 Ko) et sa police (153 Ko) sont remplacées
// par une police réduite aux 12 icônes du site (1 Ko), déclarée dans style.css
add_action( 'wp_enqueue_scripts', function () {
    wp_dequeue_style( 'fontawesome' );
}, 20 );

// Images des produits : le navigateur prenait la version 300 px (affichée en ~350 px, donc floue).
// On lui indique la vraie largeur d'affichage pour qu'il choisisse une version plus nette.
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment, $size ) {
    if ( 'woocommerce_thumbnail' === $size ) {
        $attr['sizes']   = '(max-width: 400px) 92vw, 360px';
        $attr['loading'] = 'lazy'; // sous la ligne de flottaison : ne pas concurrencer l'image principale
        // On retire du srcset les versions de plus de 800 px (jusqu'à 1920 px pour une vignette !)
        if ( ! empty( $attr['srcset'] ) ) {
            $keep = array();
            foreach ( explode( ',', $attr['srcset'] ) as $candidate ) {
                $candidate = trim( $candidate );
                if ( preg_match( '/\s(\d+)w$/', $candidate, $m ) && (int) $m[1] <= 800 ) {
                    $keep[] = $candidate;
                }
            }
            if ( $keep ) {
                $attr['srcset'] = implode( ', ', $keep );
            }
        }
    }
    return $attr;
}, 10, 3 );

// Accueil : feuilles de style qui ne servent pas sur cette page (moins de CSS bloquant au chargement)
// - wc-blocks-style : styles des blocs WooCommerce (l'accueil affiche les produits avec un shortcode)
// - social_warfare : boutons de partage, affichés seulement sous les articles
add_action( 'wp_enqueue_scripts', function () {
    if ( is_front_page() || is_home() ) {
        wp_dequeue_style( 'wc-blocks-style' );
        wp_dequeue_style( 'social_warfare' );
    }
}, 100 );

