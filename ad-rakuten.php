<?php
/*
Plugin Name: Ad Rakuten
Plugin URI: https://wordpress.org/plugins/add-rakuten/
Description: With this plugin you can add Rakuten products in your website.
Version: 0.0.11
Author: FALEME Jonathan
Author URI: https://profiles.wordpress.org/scion-lucifer/
License: GPLv2 or later
Text Domain: add-rakuten
Domain Path: /languages/
*/

/*
	SDK:
		https://webservice.rakuten.co.jp/sdk/
		https://github.com/rakuten-ws/rws-php-sdk
	
	Create a buttons:
		https://code.tutsplus.com/tutorials/guide-to-creating-your-own-wordpress-editor-buttons--wp-30182

	http://demo.wp-affiliate-store.com/
*/

defined( "ABSPATH" ) or die( "No script kiddies please!" );

define( "RAKUTEN_PLUGIN_VERSION", "0.0.11" );
define( "RAKUTEN_PLUGIN_DIR", plugin_dir_path( __FILE__ ) );
define( "RAKUTEN_PLUGIN_URL", plugin_dir_url( __FILE__ ) );

add_action( "init", "rakuten_init" );

global $rakuten_client;

function rakuten_init() {

	require_once RAKUTEN_PLUGIN_DIR."sdk/autoload.php";

	global $rakuten_client;
	$rakuten_client = new RakutenRws_Client();

	$rakuten_applicationId = get_option( "rakuten_applicationId" );
	$rakuten_application_secret = get_option( "rakuten_application_secret" );
	$rakuten_affiliateId = get_option( "rakuten_affiliateId" );

	if ( !empty( $rakuten_applicationId ) ) {
		$rakuten_client->setApplicationId($rakuten_applicationId);
	}

	if ( !empty( $rakuten_application_secret ) ) {
		$rakuten_client->setSecret($rakuten_application_secret);
	}

	if ( !empty( $rakuten_affiliateId ) ) {
		$rakuten_client->setAffiliateId($rakuten_affiliateId);
	}

	if ( is_admin() ) {
		
		if ( current_user_can( "administrator" ) ) {
			add_action( "admin_menu", "rakuten_menu" );
		}

		add_filter( "mce_external_plugins", "rakuten_add_buttons" );
    	add_filter( "mce_buttons", "rakuten_register_buttons" );

    	global $pagenow;
    	if ( ( $pagenow == "post.php" || $pagenow == "post-new.php" ) ) {
    		add_action( "admin_footer", "rakuten_admin_footer");
    		add_action( "admin_print_scripts", "rakuten_admin_footer_script", 90 );
    	}

    	if ( current_user_can( "administrator" ) ) {
	    	
	    	if ( $pagenow == "admin.php" && !empty( $_GET["page"] ) && $_GET["page"] == "rakuten_settings" && !empty($_POST["action"]) && $_POST["action"] == "update" ) {

	    		if ( !isset( $_POST["name_of_nonce_field"] ) || !wp_verify_nonce( $_POST["name_of_nonce_field"], "name_of_my_action" ) ) {
	    			// Silence
	    		} else {

	    			if ( isset( $_POST["rakuten_applicationId"] ) ) {
						$rakuten_applicationId = sanitize_text_field( $_POST["rakuten_applicationId"] );
						update_option( "rakuten_applicationId", $rakuten_applicationId );
					}

					if ( isset( $_POST["rakuten_application_secret"] ) ) {
						$rakuten_application_secret = sanitize_text_field( $_POST["rakuten_application_secret"] );
						update_option( "rakuten_application_secret", $rakuten_application_secret );
					}

					if ( isset( $_POST["rakuten_affiliateId"] ) ) {
						$rakuten_affiliateId = sanitize_text_field( $_POST["rakuten_affiliateId"] );
						update_option( "rakuten_affiliateId", $rakuten_affiliateId );
					}

					if ( isset( $_POST["rakuten_pageCount_max"] ) && is_numeric($_POST["rakuten_pageCount_max"]) ) {
						$rakuten_pageCount_max = sanitize_text_field( $_POST["rakuten_pageCount_max"] );
						$rakuten_pageCount_max = (int)$rakuten_pageCount_max+0;

						if ($rakuten_pageCount_max <= 0) {
							$rakuten_pageCount_max = 1;
						}

						update_option( "rakuten_pageCount_max", $rakuten_pageCount_max );
					}

					$url = admin_url( "admin.php?page=rakuten_settings&rakuten_message=update" );
					wp_redirect( $url );
					exit;
				}
				
	    	} 

    	}

    	if ( empty( $rakuten_applicationId ) || empty( $rakuten_application_secret ) || empty( $rakuten_affiliateId ) ) {
	    	function sample_admin_notice__error() {
				$class = "error notice notice-error";
				$title = __( "Plugin: Ad Rakuten", "add-rakuten" );
				$message = __( "Do not forget to configure the Ad Rakuten Plugin.", "add-rakuten" );
				$link = admin_url( "admin.php?page=rakuten_settings");
				$link_text = __( "Settings", "add-rakuten" );
				printf( '<div class="%1$s"><h2>'.$title.'</h2><p>%2$s <a href="%3$s">'.$link_text.'</a></p></div>', esc_attr( $class ), esc_html( $message ), esc_attr( $link ) ); 
			}
			add_action( "admin_notices", "sample_admin_notice__error" );
		}

	} else {
    	add_shortcode( "rakuten", "rakuten_shortcode" );
	}

	// require_once get_template_directory()."/inc/customizer.php";

	function rakuten_customize( $wp_customize ) {

		// Add section
			$wp_customize->add_section( "rakuten_section", array(
				"title" => __( "Rakuten", "add-rakuten"),
				"priority" => 999
			));

		// https://codex.wordpress.org/Class_Reference/WP_Customize_Color_Control
			$wp_customize->add_setting( "rakuten_item_bgcolor", array(
				"default"     	=> "#e4e4e4",
				"transport"		=> "postMessage",
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( 
				$wp_customize, 
				"rakuten_item_bgcolor_id", 
				array(
					"label"    => __( "Background Color", "add-rakuten" ),
					"section"  => "rakuten_section",
					"priority" => 1,
					"settings" => "rakuten_item_bgcolor"
			) ) );

		// https://codex.wordpress.org/Class_Reference/WP_Customize_Control
			$wp_customize->add_setting( "rakuten_item_max_width", array(
				"default" 		=> 280,
				"transport"  	=> "postMessage",
			));

			$wp_customize->add_control( new WP_Customize_Control( 
				$wp_customize, 
				"rakuten_item_max_width_id",
				array(
					"label" 	=> __( "Width", "add-rakuten" ), // 商品価格
					'type'      => 'number',
					"priority" 	=> 2,
					"section" 	=> "rakuten_section",
					"settings" 	=> "rakuten_item_max_width",
				)
			));	

		// https://codex.wordpress.org/Class_Reference/WP_Customize_Color_Control
			$wp_customize->add_setting( "rakuten_item_price_color", array(
				"default"     	=> "#bf0000",
				"transport"		=> "postMessage",
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( 
				$wp_customize, 
				"rakuten_item_color_id", 
				array(
					"label"    => __( "Price (Color)", "add-rakuten" ), // 商品価格
					"section"  => "rakuten_section",
					"priority" => 6,
					"settings" => "rakuten_item_price_color"
			) ) );

		// https://codex.wordpress.org/Class_Reference/WP_Customize_Control
			$wp_customize->add_setting( "rakuten_item_price_size", array(
				"default" 		=> 20,
				"transport"  	=> "postMessage",
			));

			$wp_customize->add_control( new WP_Customize_Control( 
				$wp_customize, 
				"rakuten_item_price_id",
				array(
					"label" 	=> __( "Price (Size)", "add-rakuten" ), // 商品価格
					'type'      => 'number',
					"priority" 	=> 7,
					"section" 	=> "rakuten_section",
					"settings" 	=> "rakuten_item_price_size",
				)
			));

	}

	add_action( "customize_register", "rakuten_customize", 99 );

}

function rakuten_settings_page() {
	require_once RAKUTEN_PLUGIN_DIR."rakuten_settings_page.php";
}

function rakuten_menu() {

    add_menu_page( 
        __( "Rakuten", "add-rakuten" ),
        __( "Rakuten", "add-rakuten" ),
        "manage_options",
        "rakuten_settings",
        "rakuten_settings_page",
        RAKUTEN_PLUGIN_URL."assets/img/rakuten-20x20.png",
        20
    );
}

function rakuten_add_buttons( $plugin_array ) {
    $plugin_array["rakuten_tinymce"] = RAKUTEN_PLUGIN_URL."/assets/js/rakuten-plugin.js";
    return $plugin_array;
}

function rakuten_register_buttons( $buttons ) {
    array_push( $buttons, "rakuten" );
    return $buttons;
}

function get_rakuten_item_tpl() {
	
	$tpl = "<a href='{item.url}' target='_blank'>
	<div class='img-container'>
		<img class='rakuten_item_thumbnail' src='{item.thumbnail}' />
	</div>	
	<dl>
		<dt class='rakuten_item_name'>{item.name}</dt>
		<dd class='rakuten_item_price'>{item.price} 円</dd>
	</dl>
</a>";

	return apply_filters( "rakuten_item_tpl", $tpl );
}

function get_rakuten_item_price_color() {

	$color = get_theme_mod( "rakuten_item_price_color" );
    if ( empty($color) || substr( $color, 0, 1 ) != "#" || strlen($color) != "7" || strpos($color, " ") > 0 ) {
    	$color = "#bf0000";
    }

    return apply_filters( "rakuten_item_price_color", $color );
}

function get_rakuten_item_bgcolor() {

	$color = get_theme_mod( "rakuten_item_bgcolor" );
    if ( empty($color) || substr( $color, 0, 1 ) != "#" || strlen($color) != "7" || strpos($color, " ") > 0 ) {
    	$color = "#e4e4e4";
    }

    return apply_filters( "rakuten_item_bgcolor", $color );
}

function get_rakuten_item_price_size() {

	$price = get_theme_mod( "rakuten_item_price_size" );
    if ( empty( $price ) ) {
    	$price = 20;
    } elseif ( !is_numeric( $price ) && !is_float( $price ) ) {
    	$price = 20;
    }

    return apply_filters( "rakuten_item_price_size", $price );
}

function get_rakuten_item_max_width() {

	$width = get_theme_mod( "rakuten_item_max_width" );
    if ( empty( $width ) ) {
    	$width = 280;
    } elseif ( !is_numeric( $width ) ) {
    	$width = 280;
    }

    return apply_filters( "rakuten_item_max_width", $width );
}




function rakuten_shortcode( $atts ) {

	$a = shortcode_atts( array(
        'ids' => ""
    ), $atts );

	if ( !empty( $a['ids'] ) ) {
		$ids = explode( ",", $a['ids'] );
	} else {
		return false;
	}

	
	$rakuten_item_tpl = get_rakuten_item_tpl();

	$rakuten_applicationId = get_option( "rakuten_applicationId" );
	$rakuten_application_secret = get_option( "rakuten_application_secret" );
	$rakuten_affiliateId = get_option( "rakuten_affiliateId" );

	if (!empty($ids) && is_array($ids)) {
		
		global $rakuten_client;
		$output = "";
		$rakuten_group_item = "rakuten_group_item_".uniqid();

    	foreach ($ids as $k => $itemCode) {

    		if ( empty( $rakuten_applicationId ) || empty( $rakuten_application_secret ) || empty( $rakuten_affiliateId ) ) {

	    		$_itemCode = urlencode( trim( $itemCode ) );
	    		$response = file_get_contents("https://rakuten.omitsumorikudasai.com/IchibaItemSearch?itemCode={$_itemCode}");
	    		$response = json_decode($response);
	    		
				foreach ( $response->Items as $v ) {

					$item = $v->Item;
					
			    	$img = "";
			    	if ( !empty( $item->smallImageUrls[0]->imageUrl ) ) {
			    		$img = $item->smallImageUrls[0]->imageUrl;
			    		@list( $img ) =  explode( "?", $img );
						//　$img = "{$img}?_ex=200x200";
			    	}
			    	
			    	$price = number_format( $item->itemPrice );

			    	$items = array(
			    		"{item.url}" => $item->affiliateUrl, // itemUrl
			    		"{item.shopUrl}" => $item->shopAffiliateUrl, // shopUrl
			    		"{item.name}" => $item->itemName,
			    		"{item.itemPrice}" => $item->itemPrice,
			    		"{item.price}" => $price,
			    		"{item.thumbnail}" => $img,
			    		"{item.caption}" => $item->itemCaption,
			    		"{item.rating}" => $item->reviewAverage,
			    		"{item.reviewCount}" => $item->reviewCount,
			    		"{item.shopCode}" => $item->shopCode,
			    		"{item.genreId}" => $item->genreId,
			    	);

			    	$tpl = $rakuten_item_tpl;
			    	foreach ( $items as $search => $replace ) {
			    		$tpl = str_replace( $search, esc_attr( $replace ), $tpl );
			    	}

			    	$output .= "<div class='rakuten_item'>\n{$tpl}\n</div>";
			    	
		    	}

	    	} else {

				$response = $rakuten_client->execute( "IchibaItemSearch", array(
				  "itemCode" => trim($itemCode)
				) );

				if ( $response->isOk() ) {

					foreach ( $response as $item ) {

				    	$img = "";
				    	if ( !empty( $item["smallImageUrls"][0]["imageUrl"] ) ) {
				    		$img = $item["smallImageUrls"][0]["imageUrl"];
				    		@list( $img ) =  explode( "?", $img );
							//　$img = "{$img}?_ex=200x200";
				    	}

				    	$price = number_format( $item["itemPrice"] );

				    	$items = array(
				    		"{item.url}" => $item["affiliateUrl"], // itemUrl
				    		"{item.shopUrl}" => $item["shopAffiliateUrl"], // shopUrl
				    		"{item.name}" => $item["itemName"],
				    		"{item.itemPrice}" => $item["itemPrice"],
				    		"{item.price}" => $price,
				    		"{item.thumbnail}" => $img,
				    		"{item.caption}" => $item["itemCaption"],
				    		"{item.rating}" => $item["reviewAverage"],
				    		"{item.reviewCount}" => $item["reviewCount"],
				    		"{item.shopCode}" => $item["shopCode"],
				    		"{item.genreId}" => $item["genreId"],
				    	);

				    	$tpl = $rakuten_item_tpl;
				    	foreach ( $items as $search => $replace ) {
				    		$tpl = str_replace( $search, esc_attr( $replace ), $tpl );
				    	}

				    	$output .= "<div class='rakuten_item'>\n{$tpl}\n</div>";
			    	}

				} else {
					usleep( 250000 ); // 250ms
					// continue;
					//// echo "<li><strong>Error:</strong> ".$response->getMessage()."</li>";
				}

				// If there are many items
				if ( !empty( $ids[$k+1] ) ) {
					usleep( 250000 ); // 250ms
				}

			}
    	}

    	if ( !empty( $output ) ) {
	    	return "<section class='rakuten_group_item {$rakuten_group_item}'>{$output}</section>";
	    } else {
	    	return false;
	    }
	}
}


function rakuten_head() {
    
    // CSS
    wp_enqueue_style( "rakuten-style", RAKUTEN_PLUGIN_URL."assets/css/style.css" );

    // Customize
    $rakuten_style = "";

	$rakuten_style .= sprintf( ".rakuten_item .rakuten_item_price { color: %s; }", esc_attr( get_rakuten_item_price_color() ) );
	$rakuten_style .= sprintf( ".rakuten_item .rakuten_item_price { font-size: %s; }", esc_attr( get_rakuten_item_price_size() )."px" );
	$rakuten_style .= sprintf( ".rakuten_item .img-container { background-color: %s; }", esc_attr( get_rakuten_item_bgcolor() ) );
	$rakuten_style .= sprintf( "@media screen and (min-width: 48em) { .rakuten_item { max-width: %s; } }", esc_attr( get_rakuten_item_max_width() )."px" );
	
	wp_add_inline_style( "rakuten-style", $rakuten_style);

	// Javascript
	wp_enqueue_script( "jquery" ); // Load jQuery Only Not Present
    wp_enqueue_script( "jquery-match-height", RAKUTEN_PLUGIN_URL."assets/js/jquery.matchHeight.js" );   
    // version 0.0.7 : wp_add_inline_script( "jquery-match-height", 'jQuery(document).ready(function($){ $( ".rakuten_item" ).matchHeight(); });' );
    wp_enqueue_script( "ad-rakuten", RAKUTEN_PLUGIN_URL."assets/js/rakuten.js" ); 

    // Customize
    wp_enqueue_script( "rakuten-customize-preview", RAKUTEN_PLUGIN_URL."assets/js/customize-preview.js" );
}
add_action( "wp_enqueue_scripts", "rakuten_head", 90 );


function rakuten_loaded() {
  load_plugin_textdomain( "add-rakuten", false, RAKUTEN_PLUGIN_DIR."/languages" ); 
}
add_action( "plugins_loaded", "rakuten_loaded" );


function rakuten_admin_footer() {
	require_once RAKUTEN_PLUGIN_DIR."rakuten-admin-media-modal.php";
}

function rakuten_admin_footer_script() {
	require_once RAKUTEN_PLUGIN_DIR."admin-footer-script.php";
}
