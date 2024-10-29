<?php
	defined( "ABSPATH" ) or die( "No script kiddies please!" );

	if ( current_user_can("administrator") ) {
		// Silence is golden
	} else {
		wp_logout();
		die( "No script kiddies please!" );
	}

	$rakuten_applicationId = get_option( "rakuten_applicationId" );
	$rakuten_application_secret = get_option( "rakuten_application_secret" );
	$rakuten_affiliateId = get_option( "rakuten_affiliateId" );

	$rakuten_pageCount_max = (int)get_option( "rakuten_pageCount_max" )+0;
	if ($rakuten_pageCount_max <= 0) {
		$rakuten_pageCount_max = 5;
	}

?>

<div class="wrap">

	<h1>
		<img src="<?php echo RAKUTEN_PLUGIN_URL."assets/img/rakuten-20x20.png" ?>" alt=""> <?php echo __( "Rakuten Settings", "add-rakuten" ); ?>
	</h1>

	<?php 
		if ( !empty($_GET["rakuten_message"]) && $_GET["rakuten_message"] == "update" ) {
	?>
			<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
				<p><strong><?php echo __( "Settings saved.", "add-rakuten" ); ?></strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo __( "Dismiss this notice.", "add-rakuten" ); ?></span></button>
			</div>
	<?php 
		}
	?>

	<form method="post" action="<?php echo admin_url( "admin.php?page=rakuten_settings" ); ?>" novalidate="novalidate">
		
		<input type="hidden" name="action" value="update">
		<?php wp_nonce_field( "name_of_my_action", "name_of_nonce_field" ); ?>
		
		<table class="form-table">

			<tbody>
				<tr>
					<th scope="row">
						<label for="rakuten_applicationId"><?php echo __( "Application ID / Developer ID", "add-rakuten" ); ?></label>
					</th>
					<td>
						<input name="rakuten_applicationId" type="text" id="rakuten_applicationId" value="<?php echo esc_attr( $rakuten_applicationId ); ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="rakuten_application_secret"><?php echo __( "Secret Key", "add-rakuten" ); ?></label>
					</th>
					<td>
						<input name="rakuten_application_secret" type="text" id="rakuten_application_secret" aria-describedby="rakuten_application_secret-description" value="<?php echo esc_attr( $rakuten_application_secret ); ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="rakuten_affiliateId"><?php echo __( "Affiliate ID", "add-rakuten" ); ?></label>
					</th>
					<td>
						<input name="rakuten_affiliateId" type="url" id="rakuten_affiliateId" value="<?php echo esc_attr( $rakuten_affiliateId ); ?>" class="regular-text code">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="rakuten_pageCount_max"><?php echo __( "Maximum number of pages", "add-rakuten" ); ?></label>
					</th>
					<td>
						<input name="rakuten_pageCount_max" type="number" min="1" id="rakuten_pageCount_max" value="<?php echo esc_attr( $rakuten_pageCount_max ); ?>" class="small-text">
					</td>
				</tr>

			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __( "Save Changes", "add-rakuten" ); ?>">
		</p>
	</form>

</div>
