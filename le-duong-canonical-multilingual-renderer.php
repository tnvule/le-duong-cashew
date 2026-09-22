<?php
/**
 * Plugin Name: Le Duong Canonical Multilingual Renderer
 * Description: Canonical EN-structure renderer for multilingual Le Duong public pages. v0.1.2 provides admin-only Products previews and makes no live-route changes.
 * Version: 0.1.2
 * Author: Le Duong Cashew
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LDCMR_VERSION', '0.1.2' );

function ldcmr_preview_locales() {
	return array( 'en', 'zh', 'ar', 'tr', 'th', 'fr', 'es', 'de', 'ja', 'ko' );
}

function ldcmr_legacy_locales() {
	return array( 'zh', 'ar', 'tr', 'th' );
}

function ldcmr_ldx_locales() {
	return array( 'en', 'fr', 'es', 'de', 'ja', 'ko' );
}

function ldcmr_b2b_url( $lang, $category = '' ) {
	$prefix = 'en' === $lang ? '' : '/' . $lang;
	$base = home_url( $prefix . '/b2b-quote/' );
	return $category ? add_query_arg( 'category', $category, $base ) : $base;
}

function ldcmr_product_url( $lang, $slug ) {
	if ( ! $slug ) {
		return ldcmr_b2b_url( $lang, 'pieces' );
	}
	if ( function_exists( 'ld_ml_route_url' ) && in_array( $lang, ldcmr_legacy_locales(), true ) ) {
		return ld_ml_route_url( $lang, 'product', $slug );
	}
	if ( function_exists( 'ldx_url' ) && in_array( $lang, ldcmr_ldx_locales(), true ) ) {
		return ldx_url( $lang, $slug, 0, false );
	}
	$prefix = 'en' === $lang ? '' : '/' . $lang;
	return home_url( $prefix . '/products/' . sanitize_title( $slug ) . '/' );
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

function ldcmr_category_for_code( $code ) {
	$code = strtoupper( trim( wp_strip_all_tags( html_entity_decode( (string) $code, ENT_QUOTES, 'UTF-8' ) ) ) );
	if ( 0 === strpos( $code, 'WW' ) ) return 'whole-white';
	if ( 0 === strpos( $code, 'A' ) ) return 'salt-roasted';
	if ( 0 === strpos( $code, 'UW' ) || 0 === strpos( $code, 'DR-NS' ) ) return 'unsalted-roasted';
	if ( 0 === strpos( $code, 'WA' ) ) return 'raw-testa';
	return 'pieces';
}

function ldcmr_category_for_index( $index ) {
	if ( 0 === $index ) return 'whole-white';
	if ( 1 === $index ) return 'salt-roasted';
	if ( 2 === $index ) return 'unsalted-roasted';
	if ( 3 === $index ) return 'raw-testa';
	return 'pieces';
}

function ldcmr_quote_label( $lang ) {
	$map = array(
		'en' => 'Request a quotation',
		'zh' => '获取报价',
		'ar' => 'اطلب عرض سعر',
		'tr' => 'Teklif isteyin',
		'th' => 'ขอใบเสนอราคา',
		'fr' => 'Demander un devis',
		'es' => 'Solicitar una cotización',
		'de' => 'Angebot anfordern',
		'ja' => '見積を依頼する',
		'ko' => '견적 요청',
	);
	if ( in_array( $lang, ldcmr_legacy_locales(), true ) && function_exists( 'ld_ml_language' ) ) {
		$base = ld_ml_language( $lang );
		if ( ! empty( $base['quote'] ) ) return $base['quote'];
	}
	return isset( $map[ $lang ] ) ? $map[ $lang ] : $map['en'];
}

function ldcmr_availability_note( $lang ) {
	$map = array(
		'zh' => '最终供应情况与验收标准以报价、买方批准的规格、样品及该批次商业文件为准。',
		'ar' => 'يتم تأكيد التوفر النهائي ومعايير القبول في عرض السعر والمواصفة المعتمدة من المشتري والعينة والمستندات التجارية الخاصة بالشحنة.',
		'tr' => 'Nihai mevcudiyet ve kabul kriterleri teklif, alıcı onaylı şartname, numune ve sevkiyata ait ticari belgelerde teyit edilir.',
		'th' => 'ความพร้อมขั้นสุดท้ายและเกณฑ์การยอมรับจะยืนยันในใบเสนอราคา ข้อกำหนดที่ผู้ซื้ออนุมัติ ตัวอย่าง และเอกสารการค้าสำหรับการจัดส่ง',
	);
	return isset( $map[$lang] ) ? $map[$lang] : '';
}

function ldcmr_flow_copy( $lang ) {
	$map = array(
		'zh' => array('先查看相关越南腰果仁产品页面，比较用途和需确认事项，然后查看我们的 ','质量文件和产品规格','。','订购流程',' 说明等级、包装、报价、质量检查、文件和装柜如何协调。为获得准确的商业报价，请通过 ','Le Duong B2B 报价平台',' 提交等级、数量、包装、目的港和目标装运时间。'),
		'ar' => array('ابدأ بصفحة حبات الكاجو الفيتنامي المناسبة، وقارن الاستخدام ونقاط التأكيد، ثم راجع ','مستندات الجودة ومواصفات المنتج',' الخاصة بنا. تشرح ','عملية الطلب',' كيفية تنسيق الدرجة والتعبئة وعرض السعر وفحوص الجودة والمستندات والتحميل. للحصول على عرض تجاري دقيق، استخدم ','منصة Le Duong B2B',' لإرسال الدرجة والكمية والتعبئة وميناء الوصول وموعد الشحن المستهدف.'),
		'tr' => array('İlgili Vietnam kaju çekirdeği sayfasından başlayın, kullanım ve teyit noktalarını karşılaştırın, ardından ','kalite belgeleri ve ürün şartnamelerimizi',' inceleyin. ','Sipariş süreci',' kalite, ambalaj, teklif, kalite kontrolleri, belgeler ve yüklemenin nasıl koordine edildiğini açıklar. Doğru bir ticari teklif için ','Le Duong B2B Teklif platformunu',' kullanarak kalite, miktar, ambalaj, varış limanı ve hedef sevkiyat dönemini paylaşın.'),
		'th' => array('เริ่มจากหน้าผลิตภัณฑ์เม็ดมะม่วงหิมพานต์เวียดนามที่เกี่ยวข้อง เปรียบเทียบการใช้งานและประเด็นที่ต้องยืนยัน แล้วตรวจสอบ ','เอกสารคุณภาพและข้อกำหนดผลิตภัณฑ์',' ของเรา ','ขั้นตอนการสั่งซื้อ',' อธิบายการประสานเกรด บรรจุภัณฑ์ ใบเสนอราคา การตรวจคุณภาพ เอกสาร และการโหลดสินค้า สำหรับข้อเสนอทางการค้าที่แม่นยำ ให้ใช้ ','แพลตฟอร์ม B2B Quote ของ Le Duong',' เพื่อระบุเกรด ปริมาณ บรรจุภัณฑ์ ท่าเรือปลายทาง และช่วงเวลาจัดส่งเป้าหมาย'),
	);
	return isset( $map[$lang] ) ? $map[$lang] : array('','','','','','','');
}

function ldcmr_dom_inner_html( DOMNode $node ) {
	$html = '';
	foreach ( $node->childNodes as $child ) {
		$html .= $node->ownerDocument->saveHTML( $child );
	}
	return $html;
}

function ldcmr_clean_ldx_products_main( $html, $lang ) {
	if ( ! is_string( $html ) || '' === $html ) return '';
	if ( 'en' !== $lang && function_exists( 'ld_catalog_clean_index_html' ) ) {
		$html = ld_catalog_clean_index_html( $html, $lang );
	}
	if ( ! class_exists( 'DOMDocument' ) ) {
		$html = preg_replace( '#<div class="ldps-story"[^>]*>.*?</div>#is', '', $html );
		$html = preg_replace( '#<a[^>]*class="ldps-specification-link"[^>]*>.*?</a>#is', '', $html );
		return is_string( $html ) ? $html : '';
	}

	$previous = libxml_use_internal_errors( true );
	$doc = new DOMDocument( '1.0', 'UTF-8' );
	$wrapped = '<!doctype html><html><body><div id="ldcmr-root">' . $html . '</div></body></html>';
	$doc->loadHTML( '<?xml encoding="UTF-8">' . $wrapped, LIBXML_HTML_NODEFDTD );
	$xpath = new DOMXPath( $doc );

	$nodes = $xpath->query( '//*[@class and contains(@class,"ldps-")]' );
	if ( $nodes ) {
		for ( $i = $nodes->length - 1; $i >= 0; $i-- ) {
			$node = $nodes->item( $i );
			if ( $node && $node->parentNode ) $node->parentNode->removeChild( $node );
		}
	}

	$quote_label = ldcmr_quote_label( $lang );
	$canonical_codes = array();
	if ( function_exists( 'ld_ml_deep_content' ) ) {
		$zh_deep = ld_ml_deep_content( 'zh' );
		foreach ( (array) ( $zh_deep['products_deep']['categories'] ?? array() ) as $cat ) $canonical_codes[] = $cat[0] ?? '';
	}
	$copies = $xpath->query( '//*[@class and contains(concat(" ", normalize-space(@class), " "), " product-visual-copy ")]' );
	if ( $copies ) {
		$copy_index = 0;
		foreach ( $copies as $copy ) {
			$code = '';
			foreach ( $copy->childNodes as $child ) {
				if ( XML_ELEMENT_NODE === $child->nodeType && 'span' === strtolower( $child->nodeName ) ) {
					$code = trim( $child->textContent );
					break;
				}
			}
			if ( isset( $canonical_codes[$copy_index] ) && '' !== $canonical_codes[$copy_index] ) {
				foreach ( $copy->childNodes as $child ) {
					if ( XML_ELEMENT_NODE === $child->nodeType && 'span' === strtolower( $child->nodeName ) ) { $child->nodeValue = $canonical_codes[$copy_index]; $code = $canonical_codes[$copy_index]; break; }
				}
			}
			if ( 'en' === $lang && in_array( $copy_index, array(0,1), true ) ) {
				$paragraphs = $copy->getElementsByTagName('p');
				if ( $paragraphs->length ) $paragraphs->item(0)->nodeValue = 0 === $copy_index ? 'Seven whole-white sizes offered under Orchid, Lotus, Jasmine and Daisy quality levels, subject to the approved specification.' : 'Salt-roasted cashew kernels with natural testa for wholesale, food service and selected consumer formats.';
			}
			$remove = array();
			foreach ( $copy->childNodes as $child ) {
				if ( XML_ELEMENT_NODE === $child->nodeType && 'a' === strtolower( $child->nodeName ) ) $remove[] = $child;
			}
			foreach ( $remove as $child ) $copy->removeChild( $child );
			$a = $doc->createElement( 'a' );
			$a->setAttribute( 'href', ldcmr_b2b_url( $lang, ldcmr_category_for_code( $code ) ) );
			$a->appendChild( $doc->createTextNode( $quote_label . ' →' ) );
			$copy->appendChild( $a );
			$copy_index++;
		}
	}

	$anchors = $xpath->query( '//*[@id="ldcmr-root"]//a[@href]' );
	if ( $anchors ) {
		foreach ( $anchors as $anchor ) {
			$href = html_entity_decode( $anchor->getAttribute( 'href' ), ENT_QUOTES, 'UTF-8' );
			if ( preg_match( '#/(?:fr|es|de|ja|ko)?/?contact/?(?:$|[?#])#i', $href ) ) {
				$anchor->setAttribute( 'href', ldcmr_b2b_url( $lang ) );
			}
		}
	}

	$root = $xpath->query( '//*[@id="ldcmr-root"]' )->item( 0 );
	$out = $root ? ldcmr_dom_inner_html( $root ) : '';
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	$out = preg_replace( '#https://leduongcashew\\.com/(?:fr/|es/|de/|ja/|ko/)?contact/#i', ldcmr_b2b_url( $lang ), $out );
	$out = preg_replace( '#href="/(?:fr/|es/|de/|ja/|ko/)?contact/"#i', 'href="' . esc_url( ldcmr_b2b_url( $lang ) ) . '"', $out );
	if ( function_exists( 'ldx_localize_links' ) && 'en' !== $lang ) {
		$out = ldx_localize_links( $out, $lang );
	}
	if ( 'ar' === $lang ) {
		$out = preg_replace( '#<main\b#i', '<main dir="rtl"', $out, 1 );
	}
	return is_string( $out ) ? $out : '';
}

function ldcmr_products_main_ldx( $lang ) {
	if ( ! function_exists( 'ldx_json' ) ) return '';
	$pages = ldx_json( 'pages/' . $lang . '.json' );
	if ( empty( $pages['products']['main'] ) || ! is_string( $pages['products']['main'] ) ) return '';
	return ldcmr_clean_ldx_products_main( $pages['products']['main'], $lang );
}

function ldcmr_products_main_legacy( $lang ) {
	if ( ! in_array( $lang, ldcmr_legacy_locales(), true ) ) return '';
	if ( ! function_exists( 'ld_ml_language' ) || ! function_exists( 'ld_ml_deep_content' ) ) return '';

	$base = ld_ml_language( $lang );
	$deep = ld_ml_deep_content( $lang );
	$products = isset( $base['products'] ) && is_array( $base['products'] ) ? $base['products'] : array();
	$d = isset( $deep['products_deep'] ) && is_array( $deep['products_deep'] ) ? $deep['products_deep'] : array();
	if ( ! $products || ! $d || empty( $d['categories'] ) ) return '';

	$dir = 'ar' === $lang ? 'rtl' : 'ltr';
	$quote_label = ldcmr_quote_label( $lang );
	$nav = isset( $base['nav'] ) && is_array( $base['nav'] ) ? $base['nav'] : array();

	ob_start();
	?>
	<main dir="<?php echo esc_attr( $dir ); ?>" data-ldcmr="products-v011">
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
					?>
					<article class="product-visual-card">
						<div class="product-visual-image"><img src="<?php echo esc_url( ldcmr_product_image( (int) $index ) ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async"></div>
						<div class="product-visual-copy">
							<span><?php echo esc_html( $code ); ?></span><h2><?php echo esc_html( $name ); ?></h2><p><?php echo esc_html( $copy ); ?></p>
							<a href="<?php echo esc_url( ldcmr_b2b_url( $lang, $category ) ); ?>"><?php echo esc_html( $quote_label ); ?> →</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<section class="section product-pillar" aria-labelledby="vietnam-cashew-kernel-guide">
			<div class="container section-heading">
				<p class="eyebrow"><?php echo esc_html( $d['sourcing_eyebrow'] ?? '' ); ?></p>
				<h2 id="vietnam-cashew-kernel-guide"><?php echo esc_html( $d['sourcing_title'] ?? '' ); ?></h2>
				<p><?php echo esc_html( $d['sourcing_intro'] ?? '' ); ?></p>
			</div>
			<div class="container ld-grid">
				<?php foreach ( (array) ( $d['sourcing_sections'] ?? array() ) as $index => $section ) : ?>
					<article><h3><?php echo esc_html( $section[0] ?? '' ); ?></h3><p><?php echo esc_html( $section[1] ?? '' ); ?>
					<?php if ( 0 === $index ) : ?> <a href="<?php echo esc_url( ldcmr_product_url($lang,'whole-white-cashew-kernels') ); ?>"><?php echo esc_html($d['categories'][0][1] ?? 'Whole White'); ?></a>, <a href="<?php echo esc_url( ldcmr_product_url($lang,'ww240-cashew-kernels') ); ?>">WW240</a> &amp; <a href="<?php echo esc_url( ldcmr_product_url($lang,'ww320-cashew-kernels') ); ?>">WW320</a>.
					<?php elseif ( 1 === $index ) : ?> <a href="<?php echo esc_url( ldcmr_product_url($lang,'splits-and-pieces') ); ?>"><?php echo esc_html($d['categories'][9][1] ?? 'Splits & Pieces'); ?></a>.
					<?php elseif ( 2 === $index ) : ?> <a href="<?php echo esc_url( ldcmr_product_url($lang,'unpeeled-raw-cashews') ); ?>"><?php echo esc_html($d['categories'][3][1] ?? 'Raw with testa'); ?></a>, <a href="<?php echo esc_url( ldcmr_product_url($lang,'salt-roasted-cashews') ); ?>"><?php echo esc_html($d['categories'][1][1] ?? 'Salt-roasted'); ?></a>, <a href="<?php echo esc_url( ldcmr_product_url($lang,'unsalted-roasted-cashew-kernels') ); ?>"><?php echo esc_html($d['categories'][2][1] ?? 'Unsalted roasted'); ?></a>.
					<?php endif; ?></p></article>
				<?php endforeach; ?>
			</div>
			<div class="container ld-section">
				<h2><?php echo esc_html( $d['confirm_title'] ?? '' ); ?></h2>
				<div class="ld-table-wrap"><table class="ld-spec"><tbody>
					<?php foreach ( (array) ( $d['confirm_rows'] ?? array() ) as $row ) : ?>
						<tr><th scope="row"><?php echo esc_html( $row[0] ?? '' ); ?></th><td><?php echo esc_html( $row[1] ?? '' ); ?></td></tr>
					<?php endforeach; ?>
				</tbody></table></div>
				<p class="ld-note"><?php echo esc_html( ldcmr_availability_note( $lang ) ); ?></p>
			</div>
			<div class="container ld-section">
				<h2><?php echo esc_html( $d['flow_title'] ?? '' ); ?></h2>
				<?php $flow = ldcmr_flow_copy( $lang ); ?>
				<p><?php echo esc_html($flow[0]); ?><a href="<?php echo esc_url(home_url('/'.$lang.'/certificates/')); ?>"><?php echo esc_html($flow[1]); ?></a><?php echo esc_html($flow[2]); ?><a href="<?php echo esc_url(home_url('/'.$lang.'/how-to-order/')); ?>"><?php echo esc_html($flow[3]); ?></a><?php echo esc_html($flow[4]); ?><a href="<?php echo esc_url(ldcmr_b2b_url($lang)); ?>"><?php echo esc_html($flow[5]); ?></a><?php echo esc_html($flow[6]); ?></p>
				<div class="ld-related" aria-label="<?php echo esc_attr( $d['flow_title'] ?? '' ); ?>">
					<a href="<?php echo esc_url(ldcmr_product_url($lang,'whole-white-cashew-kernels')); ?>"><?php echo esc_html($d['categories'][0][1] ?? 'Whole White'); ?> <span>→</span></a>
					<a href="<?php echo esc_url(ldcmr_product_url($lang,'ww240-cashew-kernels')); ?>">WW240 <span>→</span></a>
					<a href="<?php echo esc_url(ldcmr_product_url($lang,'ww320-cashew-kernels')); ?>">WW320 <span>→</span></a>
					<a href="<?php echo esc_url(ldcmr_product_url($lang,'splits-and-pieces')); ?>"><?php echo esc_html($d['categories'][9][1] ?? 'Splits & Pieces'); ?> <span>→</span></a>
					<a href="<?php echo esc_url(ldcmr_product_url($lang,'unsalted-roasted-cashew-kernels')); ?>"><?php echo esc_html($d['categories'][2][1] ?? 'Unsalted Roasted'); ?> <span>→</span></a>
					<a href="<?php echo esc_url(home_url('/'.$lang.'/certificates/')); ?>"><?php echo esc_html($nav['certificates'] ?? 'Certificates'); ?> <span>→</span></a>
					<a href="<?php echo esc_url(ldcmr_b2b_url($lang)); ?>"><?php echo esc_html($quote_label); ?> <span>→</span></a>
				</div>
			</div>
		</section>
		<section class="section section-dark"><div class="container split">
			<div><p class="eyebrow"><?php echo esc_html( $d['packing_title'] ?? '' ); ?></p><h2><?php echo esc_html( $d['packing_title'] ?? '' ); ?></h2><p><?php echo esc_html( $d['packing_copy'] ?? '' ); ?></p></div>
			<div><ul class="check-list light"><?php foreach ( (array) ( $d['packing_bullets'] ?? array() ) as $bullet ) : ?><li><?php echo esc_html( $bullet ); ?></li><?php endforeach; ?></ul></div>
		</div></section>
	</main>
	<?php
	return (string) ob_get_clean();
}

function ldcmr_products_main( $lang ) {
	if ( in_array( $lang, ldcmr_legacy_locales(), true ) ) return ldcmr_products_main_legacy( $lang );
	if ( in_array( $lang, ldcmr_ldx_locales(), true ) ) return ldcmr_products_main_ldx( $lang );
	return '';
}

function ldcmr_metrics( $html ) {
	return array(
		'sections'      => preg_match_all( '#<section\b#i', $html ),
		'product_cards' => preg_match_all( '#class="product-visual-card"#i', $html ),
		'h2'            => preg_match_all( '#<h2\b#i', $html ),
		'h3'            => preg_match_all( '#<h3\b#i', $html ),
		'ldps_classes'  => preg_match_all( '#\bldps-#i', $html ),
		'contact_links' => preg_match_all( '#/contact/?["?]#i', $html ),
		'b2b_links'     => preg_match_all( '#/b2b-quote/#i', $html ),
		'ldml_classes'  => preg_match_all( '#\bldml-#i', $html ),
	);
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'ld-canonical/v1', '/preview/products', array(
		'methods' => 'GET',
		'permission_callback' => function () { return current_user_can( 'manage_options' ); },
		'args' => array( 'lang' => array( 'required'=>true, 'type'=>'string', 'enum'=>ldcmr_preview_locales() ) ),
		'callback' => function ( WP_REST_Request $request ) {
			$lang = sanitize_key( (string) $request['lang'] );
			$html = ldcmr_products_main( $lang );
			if ( '' === $html ) return new WP_Error( 'ldcmr_preview_unavailable', 'Canonical Products preview could not be generated.', array( 'status'=>500 ) );
			return rest_ensure_response( array( 'version'=>LDCMR_VERSION, 'lang'=>$lang, 'metrics'=>ldcmr_metrics($html), 'html'=>$html ) );
		},
	) );
	register_rest_route( 'ld-canonical/v1', '/health', array(
		'methods'=>'GET',
		'permission_callback'=>function(){return current_user_can('manage_options');},
		'callback'=>function(){return rest_ensure_response(array('ok'=>true,'version'=>LDCMR_VERSION,'mode'=>'preview-only','live_route_changes'=>false));}
	) );
} );
