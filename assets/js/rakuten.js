jQuery(document).ready(function($){ 

	function rinh() {
		
		$( ".rakuten_item_name" ).height( "auto" );
		$( ".rakuten_group_item" ).each(function(){
			var h = 0;
			$( ".rakuten_item_name", this ).each(function(){
				if ( h < $(this).height() ) {
					h = $(this).height();
				}
			});

			$( ".rakuten_item_name", this ).height(h);
			
		});

		$( ".rakuten_item" ).matchHeight();

	}

	rinh();
	var rinh_timer;
	$(window).resize(function(){
		clearTimeout(rinh_timer);
		rinh_timer = setTimeout(function(){
			rinh();
		}, 80);
	});

});