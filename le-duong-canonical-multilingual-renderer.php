<?php
/**
 * Plugin Name: Le Duong Canonical Multilingual Renderer
 * Description: Canonical EN-structure renderer for multilingual Le Duong public pages. v0.1.0 provides admin-only Products previews and makes no live-route changes.
 * Version: 0.1.0
 * Author: Le Duong Cashew
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LDCMR_VERSION', '0.1.0' );

function ldcmr_preview_locales() {
	return array( 'zh', 'ar', 'tr', 'th' );
}

function ldcmr_b2b_url( $lang, $category = '' ) {
	$base = home_url( '/' . $lang . '/b2b-quote/' );
	return $category ? add_query_arg( 'category', $category, $base ) : $base;
}

function ldcmr_product_url( $lang, $slug ) {
	if ( ! $slug ) {
		return ldcmr_b2b_url( $lang, 'pieces' );
	}
	if ( function_exists( 'ld_ml_route_url' ) ) {
		return ld_ml_route_url( $lang, 'product', $slug );
	}
	return home_url( '/' . $lang . '/products/' . sanitize_title( $slug ) . '/' );
}

function ldcmr_product_image( $index ) {
	$theme = trailingslashit( get_template_directory_uri() ) . 'assets/images/products/';
	$map = array(
		0  => $theme . 'ww320.png',
		1  => $theme . 'roasted.png',
		2  => home_url( '/wp-content/uploads/2026/08/3be8d294-216c-459a-bb3d-c9b12d1cb001-768x768.png' ),
		3  => $theme . 'raw.png',
		4  => $theme . 'sw.jpg',
		5  => $theme . 'sk.jpg',
		6  => $theme . 'dw.jpg',
		7  => $theme . 'wb.jpg',
		8  => $theme . 'sb.jpg',
		9  => $theme . 'ws.png',
		10 => $theme . 'ss.jpeg',
		11 => $theme . 'lp-sp.png',
		12 => $theme . 'sp.jpg',
		13 => $theme . 'bb.jpg',
	);
	return isset( $map[ $index ] ) ? $map[ $index ] : $theme . 'ww320.png';
}

function ldcmr_category_for_index( $index ) {
	if ( 0 === $index ) return 'whole-white';
	if ( 1 === $index ) return 'salt-roasted';
	if ( 2 === $index ) return 'unsalted-roasted';
	if ( 3 === $index ) return 'raw-testa';
	return 'pieces';
}

function ldcmr_products_main( $lang ) {
	if ( ! in_array( $lang, ldcmr_preview_locales(), true ) ) {
		return '';
	}
	if ( ! function_exists( 'ld_ml_language' ) || ! function_exists( 'ld_ml_deep_content' ) ) {
		return '';
	}

	$base = ld_ml_language( $lang );
	$deep = ld_ml_deep_content( $lang );
	$products = isset( $base['products'] ) && is_array( $base['products'] ) ? $base['products'] : array();
	$d = isset( $deep['products_deep'] ) && is_array( $deep['products_deep'] ) ? $deep['products_deep'] : array();
	if ( ! $products || ! $d || empty( $d['categories'] ) ) {
		return '';
	}

	$dir = 'ar' === $lang ? 'rtl' : 'ltr';
	$quote_label = ! empty( $base['quote'] ) ? $base['quote'] : 'Request a quotation';
	$nav = isset( $base['nav'] ) && is_array( $base['nav'] ) ? $base['nav'] : array();

	ob_start();
	?>
	<main dir="<?php echo esc_attr( $dir ); ?>" data-ldcmr="products-v010">
		<header class="page-hero product-hero"><div class="container">
			<p class="eyebrow"><?php echo esc_html( $products['eyebrow'] ?? '' ); ?></p>
			<h1><?php echo esc_html( $products['title'] ?? '' ); ?></h1>
			<p><?php echo esc_html( $products['lead'] ?? '' ); ?></p>
		</div></header>

		<section class="section">
			<div class="container section-heading">
				<p class="eyebrow"><?php echo esc_html( $products['eyebrow'] ?? '' ); ?></p>
				<h2><?php echo esc_html( $d['catalog_title'] ?? '' ); ?></h2>
				<p><?php echo esc_html( $d['catalog_intro'] ?? '' ); ?></p>
			</div>

			<div class="container product-visual-grid">
				<?php foreach ( $d['categories'] as $index => $item ) :
					$code = isset( $item[0] ) ? $item[0] : '';
					$name = isset( $item[1] ) ? $item[1] : '';
					$copy = isset( $item[2] ) ? $item[2] : '';
					$category = ldcmr_category_for_index( (int) $index );
					$quote_url = ldcmr_b2b_url( $lang, $category );
					?>
					<article class="product-visual-card">
						<div class="product-visual-image">
							<img src="<?php echo esc_url( ldcmr_product_image( (int) $index ) ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async">
						</div>
						<div class="product-visual-copy">
							<span><?php echo esc_html( $code ); ?></span>
							<h2><?php echo esc_html( $name ); ?></h2>
							<p><?php echo esc_html( $copy ); ?></p>
							<a href="<?php echo esc_url( $quote_url ); ?>"><?php echo esc_html( $quote_label ); ?> →</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="section product-pillar" aria-labelledby="ldcmr-products-guide-<?php echo esc_attr( $lang ); ?>">
			<div class="container section-heading">
				<p class="eyebrow"><?php echo esc_html( $d['sourcing_eyebrow'] ?? '' ); ?></p>
				<h2 id="ldcmr-products-guide-<?php echo esc_attr( $lang ); ?>"><?php echo esc_html( $d['sourcing_title'] ?? '' ); ?></h2>
				<p><?php echo esc_html( $d['sourcing_intro'] ?? '' ); ?></p>
			</div>

			<div class="container ld-grid">
				<?php foreach ( (array) ( $d['sourcing_sections'] ?? array() ) as $section ) : ?>
					<article>
						<h3><?php echo esc_html( $section[0] ?? '' ); ?></h3>
						<p><?php echo esc_html( $section[1] ?? '' ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="container ld-section">
				<h2><?php echo esc_html( $d['confirm_title'] ?? '' ); ?></h2>
				<div class="ld-table-wrap">
					<table class="ld-spec"><tbody>
						<?php foreach ( (array) ( $d['confirm_rows'] ?? array() ) as $row ) : ?>
							<tr><th scope="row"><?php echo esc_html( $row[0] ?? '' ); ?></th><td><?php echo esc_html( $row[1] ?? '' ); ?></td></tr>
						<?php endforeach; ?>
					</tbody></table>
				</div>
			</div>

			<div class="container ld-section">
				<h2><?php echo esc_html( $d['flow_title'] ?? '' ); ?></h2>
				<p><?php echo esc_html( $d['flow_copy'] ?? '' ); ?></p>
				<div class="ld-related" aria-label="<?php echo esc_attr( $d['flow_title'] ?? '' ); ?>">
					<?php
					$related = array(
						array( 0, 'whole-white-cashew-kernels' ),
						array( 9, 'splits-and-pieces' ),
						array( 2, 'unsalted-roasted-cashew-kernels' ),
					);
					foreach ( $related as $pair ) :
						$idx = $pair[0];
						$slug = $pair[1];
						$label = isset( $d['categories'][ $idx ][1] ) ? $d['categories'][ $idx ][1] : $slug;
						?>
						<a href="<?php echo esc_url( ldcmr_product_url( $lang, $slug ) ); ?>"><?php echo esc_html( $label ); ?> <span>→</span></a>
					<?php endforeach; ?>
					<a href="<?php echo esc_url( home_url( '/' . $lang . '/certificates/' ) ); ?>"><?php echo esc_html( $nav['certificates'] ?? 'Certificates' ); ?> <span>→</span></a>
					<a href="<?php echo esc_url( ldcmr_b2b_url( $lang ) ); ?>"><?php echo esc_html( $quote_label ); ?> <span>→</span></a>
				</div>
			</div>
		</section>

		<section class="section section-dark"><div class="container split">
			<div>
				<p class="eyebrow"><?php echo esc_html( $d['packing_title'] ?? '' ); ?></p>
				<h2><?php echo esc_html( $d['packing_title'] ?? '' ); ?></h2>
				<p><?php echo esc_html( $d['packing_copy'] ?? '' ); ?></p>
			</div>
			<div><ul class="check-list light">
				<?php foreach ( (array) ( $d['packing_bullets'] ?? array() ) as $bullet ) : ?>
					<li><?php echo esc_html( $bullet ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		</div></section>
	</main>
	<?php
	return (string) ob_get_clean();
}

function ldcmr_metrics( $html ) {
	return array(
		'sections'      => preg_match_all( '#<section\b#i', $html ),
		'product_cards' => preg_match_all( '#class="product-visual-card"#i', $html ),
		'h2'            => preg_match_all( '#<h2\b#i', $html ),
		'h3'            => preg_match_all( '#<h3\b#i', $html ),
		'contact_links' => preg_match_all( '#/contact/?["?]#i', $html ),
		'b2b_links'     => preg_match_all( '#/b2b-quote/#i', $html ),
		'ldml_classes'  => preg_match_all( '#\bldml-#i', $html ),
	);
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'ld-canonical/v1', '/preview/products', array(
		'methods'  => 'GET',
		'permission_callback' => function () { return current_user_can( 'manage_options' ); },
		'args' => array(
			'lang' => array(
				'required' => true,
				'type' => 'string',
				'enum' => ldcmr_preview_locales(),
			),
		),
		'callback' => function ( WP_REST_Request $request ) {
			$lang = sanitize_key( (string) $request['lang'] );
			$html = ldcmr_products_main( $lang );
			if ( '' === $html ) {
				return new WP_Error( 'ldcmr_preview_unavailable', 'Canonical Products preview could not be generated.', array( 'status' => 500 ) );
			}
			return rest_ensure_response( array(
				'version' => LDCMR_VERSION,
				'lang'    => $lang,
				'metrics' => ldcmr_metrics( $html ),
				'html'    => $html,
			) );
		},
	) );

	register_rest_route( 'ld-canonical/v1', '/health', array(
		'methods' => 'GET',
		'permission_callback' => function () { return current_user_can( 'manage_options' ); },
		'callback' => function () {
			return rest_ensure_response( array(
				'ok'      => true,
				'version' => LDCMR_VERSION,
				'mode'    => 'preview-only',
				'live_route_changes' => false,
			) );
		},
	) );
} );
