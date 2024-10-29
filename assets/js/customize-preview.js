jQuery(document).ready(function($){

	if ( typeof wp !== "undefined" && typeof wp.customize !== "undefined" ) {

	    // Model: Custom color hue : twentyseventeen/assets/js/customize-preview.js
		wp.customize( "rakuten_item_price_color", function( value ) {
			value.bind( function( to ) {
				$( ".rakuten_item .rakuten_item_price" ).css( "color", to );
			});
		});

		wp.customize( "rakuten_item_price_size", function( value ) {
			value.bind( function( to ) {
				$( ".rakuten_item .rakuten_item_price" ).css( "font-size", to+"px" );
			});
		});

		wp.customize( "rakuten_item_bgcolor", function( value ) {
			value.bind( function( to ) {
				$( ".rakuten_item .img-container" ).css( "background-color", to );
			});
		});


		wp.customize( 'rakuten_item_max_width', function( value ) {
			value.bind( function( to ) {
				$(".rakuten_item").css("max-width", to+"px");
			});
		});

	}

});