<?php
	defined( "ABSPATH" ) or die( "No script kiddies please!" );
?>
<div id="__wp-rakuten-uploader" class="supports-drag-drop" style="position: relative; display: none;">
	
	<div tabindex="0" class="media-modal wp-core-ui">
		<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">メディアパネルを閉じる<!-- Fermer le panneau des médias --></span></span></button>
		<div class="media-modal-content">
			<div class="media-frame mode-select wp-core-ui" id="__wp-uploader-id-0">
				
				<!-- Menu -->
				<div class="media-frame-menu">
					<div class="media-menu">
						<a href="#" class="media-menu-item active"><?php echo __( "Add a Product", "add-rakuten" ); ?></a>
					</div>
				</div>

				<!-- Title -->
				<div class="media-frame-title">
					<h1>
						<img src="<?php echo esc_attr( RAKUTEN_PLUGIN_URL."assets/img/rakuten-20x20.png" ); ?>" alt=""> <?php echo __( "Add a Product", "add-rakuten" ); ?> <span class="dashicons dashicons-arrow-down"></span>
					</h1>
				</div>

				<!-- Router -->
				<div class="media-frame-router">
					<div class="media-router">
						<a href="#" class="media-menu-item active"><?php echo __( "Media Library", "add-rakuten" ); ?></a>
					</div>
				</div>

				<!-- Content -->
				<div class="media-frame-content" data-columns="7">
					<div class="attachments-browser">
						
						<div class="media-toolbar" style="height: 80px;">

							<?php
								// 検索キーワード
								if ( get_user_locale() == "ja" ) {
									$rakuten_type_keyword = __( "検索キーワード", "add-rakuten" );
								} else {
									$rakuten_type_keyword = __( "Keyword", "add-rakuten" );
								}

								$rakuten_type_keyword_explain = __( "<strong style='color:#bf0000;'>Search Keyword</strong>", "add-rakuten" );

								// ジャンルID
								$rakuten_type_genreId = __( "Genre ID", "add-rakuten" );
								$rakuten_type_genreId_explain = __( "<strong style='color:#bf0000;'>Genre ID</strong><br>ID used to specify the genre used on Rakuten Ichiba", "add-rakuten" );

								// アイテムコード
								$rakuten_type_itemCode = __( "Item code", "add-rakuten" );
								$rakuten_type_itemCode_explain = __( '<strong style="color:#bf0000;">Item code</strong><br>A value included in the output parameters of Item Search API. It takes the form of "shop:1234"', "add-rakuten" );

								// ショップコード
								$rakuten_type_shopCode = __( "Shop code", "add-rakuten" );
								$rakuten_type_shopCode_explain = __( '<strong style="color:#bf0000;">Shop code</strong><br> The shop code is found in each merchant&apos;s URL as the "xyz" in this example URL: http://www.rakuten.co.jp/[xyz]', "add-rakuten" );
							
							?>

							<div class="media-toolbar-secondary search-form" style="width:70%;">
								<div class="rakuten-search-type" style="padding-top: 20px;">
									<input type="radio" name="rakuten-search-type" checked="checked" value="keyword" data-explain="<?php echo esc_attr( $rakuten_type_keyword_explain ); ?>" /> <?php echo $rakuten_type_keyword; ?> &nbsp;
									<input type="radio" name="rakuten-search-type" value="genreId" data-explain="<?php echo esc_attr( $rakuten_type_genreId_explain ); ?>" /> <?php echo $rakuten_type_genreId; ?> &nbsp;
									<input type="radio" name="rakuten-search-type" value="itemCode" data-explain="<?php echo esc_attr( $rakuten_type_itemCode_explain ); ?>" /> <?php echo $rakuten_type_itemCode; ?> &nbsp;
									<input type="radio" name="rakuten-search-type" value="shopCode" data-explain="<?php echo esc_attr( $rakuten_type_shopCode_explain ); ?>" /> <?php echo $rakuten_type_shopCode; ?>
								</div>
								<label for="rakuten-search-input" class="screen-reader-text"><?php echo __( "Search product items", "add-rakuten" ); ?></label>
								<input type="search" placeholder="<?php echo __( "Search product items…", "add-rakuten" ); ?>" id="rakuten-search-input" class="search">

							</div>

							<button type="button" class="button media-button-search" style="float:left;margin-top:47px;margin-left:8px;"><?php echo __( "Search", "add-rakuten" ); ?></button>

							<span class="spinner" style="float: left; margin-top:51px;margin-left: 5px;"></span>

						</div>
						<ul tabindex="-1" class="attachments ui-sortable ui-sortable-disabled" id="__attachments-view-98" style="top: 80px;">
							<li class="rakuten-search-type-explain">
								<p style="padding: 0 10px;"><?php echo $rakuten_type_keyword_explain; ?></p>
							</li>
						</ul>
						<div class="media-sidebar">
						
						</div>
						<!-- media-sidebar -->

					</div>
				</div>

				<!-- Toolbar -->
				<div class="media-frame-toolbar">
					<div class="media-toolbar">
						<div class="media-toolbar-secondary">
							<div class="media-selection empty">
								<div class="selection-info">

									<span class="count">0<?php echo __( " selected", "add-rakuten" ); ?></span>
									<button type="button" class="button-link edit-selection"><?php echo __( "Edit Selection", "add-rakuten" ); ?></button>
									<button type="button" class="button-link clear-selection"><?php echo __( "Clear", "add-rakuten" ); ?></button>
	
								</div>
								<div class="selection-view">
									<ul tabindex="-1" class="attachments" id="__attachments-view-75"></ul>
								</div>
							</div>
						</div>
						<div class="media-toolbar-primary search-form">
							<button type="button" class="button media-button button-primary button-large rakuten-button-insert" disabled="disabled"><?php echo __( "Insert into post", "add-rakuten" ); ?></button>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="media-modal-backdrop"></div>
</div>

		