<?php
	defined( "ABSPATH" ) or die( "No script kiddies please!" );

	if ( current_user_can("author") || current_user_can("editor") || current_user_can("administrator") ) {
		// Silence is golden
	} else {
		wp_logout();
		die( "No script kiddies please!" );
	}

	global $rakuten_client;

	$rakuten_pageCount_max = (int)get_option( "rakuten_pageCount_max" )+0;
	if ($rakuten_pageCount_max <= 0) {
		$rakuten_pageCount_max = 5;
	}
?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			
			var rakuten_current_page = 0;
			var rakuten_pageCount = 1;
			var rakuten_search_timer;

			function number_format (number, decimals, decPoint, thousandsSep) {

				number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
				var n = !isFinite(+number) ? 0 : +number
				var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
				var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
				var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
				var s = ''

				var toFixedFix = function (n, prec) {
			    	var k = Math.pow(10, prec)
			    	return '' + (Math.round(n * k) / k)
			      	.toFixed(prec)
			  	}

			  	// @todo: for IE parseFloat(0.55).toFixed(0) = 0;
			  	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
			  	if (s[0].length > 3) {
			    	s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
			  	}
			  	if ((s[1] || '').length < prec) {
			    	s[1] = s[1] || ''
			    	s[1] += new Array(prec - s[1].length + 1).join('0')
			  	}

			  	return s.join(dec)
			}

			function insertAtCaret(areaId,text) {
				var txtarea = document.getElementById(areaId);
				var scrollPos = txtarea.scrollTop;
				var strPos = 0;
				var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 
					"ff" : (document.selection ? "ie" : false ) );
				if (br == "ie") { 
					txtarea.focus();
					var range = document.selection.createRange();
					range.moveStart ('character', -txtarea.value.length);
					strPos = range.text.length;
				}
				else if (br == "ff") strPos = txtarea.selectionStart;
			
				var front = (txtarea.value).substring(0,strPos);  
				var back = (txtarea.value).substring(strPos,txtarea.value.length); 
				txtarea.value=front+text+back;
				strPos = strPos + text.length;
				if (br == "ie") { 
					txtarea.focus();
					var range = document.selection.createRange();
					range.moveStart ('character', -txtarea.value.length);
					range.moveStart ('character', strPos);
					range.moveEnd ('character', 0);
					range.select();
				}
				else if (br == "ff") {
					txtarea.selectionStart = strPos;
					txtarea.selectionEnd = strPos;
					txtarea.focus();
				}
				txtarea.scrollTop = scrollPos;
			}


			var rakuten_button = `<div id="rakuten-media-buttons" class="wp-media-buttons"><button type="button" class="button" data-editor="content"><img style="float:left; margin-top:4px;" width="18" height="18" src="<?php echo RAKUTEN_PLUGIN_URL."assets/img/rakuten-20x20.png" ?>" alt=""> <?php echo __( "Rakuten", "add-rakuten" ); ?></button></div>`;

			$( "#wp-content-media-buttons" ).after( rakuten_button );
			$( "#rakuten-media-buttons" ).click( function(e){
				e.preventDefault();
				$( "#__wp-rakuten-uploader" ).show();
			});

			$( "#__wp-rakuten-uploader" ).delegate( ".rakuten-button-insert", "click", function(e) {
            	e.preventDefault();
            	if ( $( "#wp-content-wrap" ).hasClass( "html-active" ) ) {

            		var ids = "";
	                $( "#__wp-rakuten-uploader .selection-view ul li").each(function(){
	                    if ($(this).attr("aria-checked") === "true") {
	                        var item = $( ".json", this ).text();
	                        item = jQuery.parseJSON( item );
	                        if ( ids !== "" ) {
	                            ids += ", ";
	                        }
	                        ids += item.itemCode;
	                    }
	                });

	                shortcode = `[rakuten ids="${ids}"]`;
	                insertAtCaret("content", shortcode);
	                $( "#__wp-rakuten-uploader" ).hide();
	                
            	}
            });
			
			function rakuten_select_attachment( el ) {
				
				$( el ).attr( "aria-checked", "true" );
				$( el ).addClass( "details" ); 
				$( el ).addClass( "selected" );
				$( "button.check", el ).attr( "tabindex", "0" );

				var item = $( ".json", el ).text();
        		item = jQuery.parseJSON( item );

        		var img = "<?php echo RAKUTEN_PLUGIN_URL."assets/img/noimage.jpg" ?>";

				if ( typeof item.smallImageUrls !== "undefined" && typeof item.smallImageUrls[0] !== "undefined" && typeof item.smallImageUrls[0].imageUrl !== "undefined" ) {
					img = item.smallImageUrls[0].imageUrl;
					img = img.split("?");
					img = img[0]+"?_ex=300x300";
				}

				var json = JSON.stringify(item);
				var price = number_format( item.itemPrice );

				var selectionView = `	<li tabindex="0" role="checkbox" aria-label="chaton-qui-miaule" aria-checked="true" data-id="${item.itemCode}" class="attachment selection selected save-ready">
											<div class="json" style="display:none;">${json}</div>
											<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">
												<div class="thumbnail">
													<div class="centered">
														<img src="${img}" draggable="false" alt="">
													</div>
												</div>
											</div>
										</li>`;

				$( "#__wp-rakuten-uploader .selection-view ul" ).append(selectionView);

				var mediaSidebar = `<div tabindex="0" data-id="105" class="attachment-details save-ready">
										<h2>
											<?php echo __( "Product details", "add-rakuten" ); ?>		
											<span class="settings-save-status">
												<span class="spinner"></span>
												<span class="saved">保存しました。</span>
											</span>
										</h2>
										<div class="attachment-info">
											<div class="thumbnail _thumbnail-image">
												<a class="edit-attachment" href="${item.affiliateUrl}" target="_blank"><img src="${img}" draggable="false" alt=""></a>
											</div>
											<div class="details" style="width: calc(100% - 130px);">
												<div class="filename">${item.itemName}</div>
												<div style="color:#bf0000; font-size:16px;" >${price}円</div>
												<!-- <div class="uploaded">2017年11月14日</div>
												<div class="file-size">121 KB</div>
												<div class="dimensions">647 × 420</div> -->
												<a class="edit-attachment" href="${item.affiliateUrl}" target="_blank"><?php echo __( "View on Rakuten", "add-rakuten" ); ?></a>
												<!-- <button type="button" class="button-link delete-attachment">完全に削除する</button> -->
												<div class="compat-meta"></div>
											</div>
										</div>

										<!--
										<label class="setting" data-setting="url">
											<span class="name">URL</span>
											<input type="text" value="${item.itemName}" readonly="">
										</label>

										<label class="setting" data-setting="title">
											<span class="name">タイトル</span>
											<input type="text" value="${item.itemName}">
										</label>
		
										<label class="setting" data-setting="caption">
											<span class="name">キャプション</span>
											<textarea>${item.itemName}</textarea>
										</label>

										<label class="setting" data-setting="alt">
											<span class="name">代替テキスト</span>
											<input type="text" value="jnj">
										</label>
							
										<label class="setting" data-setting="description">
											<span class="name">説明</span>
											<textarea>lnj</textarea>
										</label>

										-->

									</div>`;

				$( "#__wp-rakuten-uploader .media-sidebar" ).html(mediaSidebar);

			}

			function rakuten_search() {

				var rakuten_applicationId = encodeURI( "<?php echo esc_js( get_option( "rakuten_applicationId" ) ); ?>" );
				var rakuten_affiliateId = encodeURI( "<?php echo esc_js( get_option( "rakuten_affiliateId" ) ); ?>" );
				var keyword = encodeURI( $( "#rakuten-search-input" ).val() );
				var type = $( "#__wp-rakuten-uploader .rakuten-search-type input[type=radio]:checked" ).val();

				var urlparams = "";

				if ( type === "genreId" ) {
					urlparams += `&genreId=${keyword}`;
				} else if ( type === "itemCode" ) {
					urlparams += `&itemCode=${keyword}`;
				} else if ( type === "shopCode" ) {
					urlparams += `&shopCode=${keyword}`;
				} else {
					urlparams += `&keyword=${keyword}`;
				}

				rakuten_current_page = rakuten_current_page+1;

				if ( rakuten_applicationId !== "" && rakuten_affiliateId !== "" ) {
					var url = `https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706?format=json${urlparams}&page=${rakuten_current_page}&applicationId=${rakuten_applicationId}&affiliateId=${rakuten_affiliateId}`;
					var milliseconds = 750;
					var rakuten_pageCount_max = <?php echo esc_js( $rakuten_pageCount_max ); ?>;
				} else {
					var url = `https://rakuten.omitsumorikudasai.com/IchibaItemSearch?format=json${urlparams}&page=${rakuten_current_page}`;
					var milliseconds = 950;
					var rakuten_pageCount_max = 5;
				}

				$.getJSON( url, function( response ) {

					rakuten_pageCount = response.pageCount;
					if (rakuten_pageCount > rakuten_pageCount_max) {
						rakuten_pageCount = rakuten_pageCount_max;
					}

					var output = "";
					$.each( response.Items, function() {
						$.each( this, function( k, item ) {
							
							$( "#__wp-rakuten-uploader .spinner" ).css( "visibility", "hidden" );

							var img = "<?php echo RAKUTEN_PLUGIN_URL."assets/img/noimage.jpg" ?>";

							if ( typeof item.smallImageUrls !== "undefined" && typeof item.smallImageUrls[0] !== "undefined" && typeof item.smallImageUrls[0].imageUrl !== "undefined" ) {
								// console.log(item.smallImageUrls[0].imageUrl);
								img = item.smallImageUrls[0].imageUrl;
								img = img.split("?");
								img = img[0]+"?_ex=300x300";
							}

							var json = JSON.stringify( item );
							var price = number_format( item.itemPrice );

							output += `	<li tabindex="0" role="checkbox" aria-label="chaton-qui-miaule" aria-checked="false" data-id="${item.itemCode}" class="attachment save-ready">
											<div class="json" style="display:none;">${json}</div>
											<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">
												<div class="thumbnail">
													<div class="centered">
														<img src="${img}" draggable="false" alt="">
													</div>
												</div>
											</div>
											<div style="margin-top:10px;color: #bf0000;font-size: 18px;">${price}円</div>
											<button type="button" class="check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text">選択を解除</span></button>
										</li>`;

						});
					});

					$( "#__wp-rakuten-uploader .attachments-browser .attachments" ).append(output);


					if ( $( "#__wp-rakuten-uploader" ).is( ":visible" ) ) {
						if ( rakuten_pageCount >= rakuten_current_page ) {
							
							rakuten_search_timer = setTimeout( function(){

								if ( $( "#__wp-rakuten-uploader" ).is( ":visible" ) ) {
									// console.log( `count: ${rakuten_current_page}/${rakuten_pageCount}` );
									rakuten_search();
								} else {
									clearTimeout( rakuten_search_timer );
								}

							}, milliseconds );
						}
					} else {
						clearTimeout( rakuten_search_timer );
					}

				});

			}

			// $( "#__wp-rakuten-uploader .attachments-browser .attachments" )

			$( "#__wp-rakuten-uploader .rakuten-search-type input[type=radio]" ).change(function(){
				$( "#__wp-rakuten-uploader .attachments-browser .attachments li.rakuten-search-type-explain" ).remove();
				var explain = $( this ).attr( "data-explain" );
				var output = `<li class="rakuten-search-type-explain"><p style="padding: 0 10px;">${explain}</p></li>`;
				$( "#__wp-rakuten-uploader .attachments-browser .attachments" ).prepend(output);
			});

			$( "#__wp-rakuten-uploader .media-modal-close, #__wp-rakuten-uploader .media-modal-backdrop" ).click(function(e){
				e.preventDefault();
				$( "#__wp-rakuten-uploader" ).hide();
				clearTimeout( rakuten_search_timer );
			});

			$( "#__wp-rakuten-uploader" ).delegate( ".attachments-browser .attachments .attachment", "click", function(e) {

				e.preventDefault();
				
				var dataID = $(this).attr( "data-id" );
				$( "#__wp-rakuten-uploader .attachment-details" ).remove();

				// check if user clicked with command key (for mac) or ctrl key (for windows)
				if ( e.metaKey || ( navigator.platform.toUpperCase().indexOf( "WIN" )!== -1 && e.ctrlKey ) ) {

					if ( $(this).attr( "aria-checked" ) === "false" ) {

						rakuten_select_attachment( $(this) );

					} else {

						$(this).attr( "aria-checked", "false" );
						$(this).removeClass( "details" ); 
						$(this).removeClass( "selected" );
						$( "button.check", this ).attr( "tabindex", "-1" );

						$( "#__wp-rakuten-uploader .selection-view ul li" ).each(function(){
							if ( $(this).attr( "data-id" ) === dataID ) {
								$(this).remove();
							}
						});

					}

				} else {

					$( "#__wp-rakuten-uploader .selection-view ul" ).html( "" );
					$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment" ).removeClass( "details" );
					$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment" ).attr( "aria-checked", "false" );
					$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment" ).removeClass( "selected" );
					$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment button.check" ).attr( "tabindex", "-1" )

					if ( $(this).attr( "aria-checked" ) === "false" ) {
						rakuten_select_attachment( $(this) );
					}
				}

				var nbr = $( "#__wp-rakuten-uploader .selection-view ul li" ).length;
			
				$( "#__wp-rakuten-uploader .media-selection .count" ).html( `${nbr}<?php echo __( " selected", "add-rakuten" ); ?>` );

				if (nbr > 0) {
					
					$( "#__wp-rakuten-uploader .rakuten-button-insert" ).removeAttr( "disabled" );
					$( "#__wp-rakuten-uploader .media-selection" ).removeClass( "empty" );
					
					if (nbr === 1) {
						$( "#__wp-rakuten-uploader .media-selection" ).addClass( "one" );
					} else {
						$( "#__wp-rakuten-uploader .media-selection").removeClass( "one" );
					}

				} else {

					$( "#__wp-rakuten-uploader .rakuten-button-insert" ).attr( "disabled","disabled" );
					$( "#__wp-rakuten-uploader .media-selection" ).removeClass( "one" );
					$( "#__wp-rakuten-uploader .media-selection" ).addClass( "empty" );

					$( "#__wp-rakuten-uploader .selection-view ul" ).html( "" );

				}

			});

			$( "#__wp-rakuten-uploader .media-button-search" ).click(function(e){
				
				e.preventDefault();

				rakuten_current_page = 0;
				rakuten_pageCount = 1;
				clearTimeout(rakuten_search_timer);

				$( "#__wp-rakuten-uploader .attachments-browser .attachments" ).html( "" );
				if ( $( "#rakuten-search-input" ).val() === "" ) {
					return;
				}

				$( "#__wp-rakuten-uploader .spinner" ).css( "visibility", "visible" );

				rakuten_search();

			});

			$( "#__wp-rakuten-uploader .clear-selection" ).click(function(e){
				
				e.preventDefault();

				$( "#__wp-rakuten-uploader .media-selection .count" ).html( `0<?php echo __( " selected", "add-rakuten" ); ?>` );
				$( "#__wp-rakuten-uploader .rakuten-button-insert" ).attr( "disabled","disabled" );
				$( "#__wp-rakuten-uploader .media-selection" ).removeClass( "one" );
				$( "#__wp-rakuten-uploader .media-selection" ).addClass( "empty" );

				$( "#__wp-rakuten-uploader .selection-view ul" ).html( "" );

				$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment" ).attr( "aria-checked", "false" );
				$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment" ).removeClass( "details" ); 
				$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment" ).removeClass( "selected" );
				$( "#__wp-rakuten-uploader .attachments-browser .attachments .attachment button.check" ).attr( "tabindex", "-1" );

			});

		});		
	</script>