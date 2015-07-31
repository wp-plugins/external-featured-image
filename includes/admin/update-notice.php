<?php

if ( ! function_exists( 'nelioefi_update_notice' ) ) {

	add_action( 'wp_ajax_nelioefi_dismiss_update_notice', 'nelioefi_dismiss_update_notice' );
	function nelioefi_dismiss_update_notice() {
		update_option( 'nelioefi_dismiss_update_notice', NELIOEFI_PLUGIN_VERSION );
		die();
	}

	$old_version = get_option( 'nelioefi_last_version', 0 );
	if ( version_compare( $old_version, '1.3.1' ) <= 0 ) {
		add_action( 'admin_notices', 'nelioefi_update_notice' );
	}

	function nelioefi_update_notice() {
		$message = __( '<strong>IMPORTANT!</strong> You recently installed/updated Nelio External Featured Images. If you used the plugin before, please make sure to <a href="%s">regenerate EFI attachments</a>.', 'nelioefi' );
		$message = sprintf( $message, admin_url( '/upload.php?page=nelioefi-regenerate' ) );
		?>
		<div class="updated">
			<p style="float:right;font-size:10px;text-align:right;">
				<a id="dismiss-nelioab-campaign" href="#"><?php _e( 'Dismiss' ); ?></a>
			</p>
			<p style="font-size:15px;"><?php echo $message; ?></p>
			<script style="display:none;" type="text/javascript">
			(function($) {
				$('a#dismiss-nelioab-campaign').on('click', function() {
					$.post( ajaxurl, {action:'nelioefi_dismiss_update_notice'} );
					$(this).parent().parent().fadeOut();
				});
			})(jQuery);
			</script>
		</div>
		<?php
	}

}

