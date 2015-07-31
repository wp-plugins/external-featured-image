<?php

add_action( 'admin_menu', 'nelioefi_add_page' );
function nelioefi_add_page() {
	add_submenu_page( 'upload.php',
		__( 'NelioEFI', 'nelioab' ),
		__( 'NelioEFI', 'nelioab' ),
		'manage_options',
		'nelioefi-regenerate',
		'nelioefi_page' );
}

// THE PAGE STARTS HERE
// =========================================================
function nelioefi_page() { ?>

<h2><?php _e( 'NelioEFI &mdash; Regenerate Thumbnails', 'nelioefi' ); ?></h2>

<div class="wrap">
<p><?php
	_e( 'Click on the button «Fix External Images» for generating virtual attachments for all posts in your blog that use external featured images.', 'nelioefi' );
	echo ' ';
	_e( 'On the other hand, click on «Regenerate Thumbnails» if you\'ve changed your theme and you want the sizes of Nelio\'s external featured images to match the new theme.', 'nelioefi' );
?></p>


<a id="nelioefi_create" class="button button-primary" href="#"><?php _e( 'Fix External Images', 'nelioefi' ); ?></a>
<a id="nelioefi_regenerate" class="button" href="#"><?php _e( 'Regenerate Thumbnails', 'nelioefi' ); ?></a>
<div id="nelioefi_progress" style="width:100%;height:30px;padding-top:1em;">
<div class="nelioefi_percentage"></div>
<div class="nelioefi_bar" style="background-color:#555;height:20px;width:0%;">
</div>
<div id="nelioefi_messages" style="padding-top:1em;"></div>
</div>

<script type="text/javascript">
	(function($) {
		var generateButton = $( '#nelioefi_regenerate' );
		var createButton = $( '#nelioefi_create' );
		var messageArea = $( '#nelioefi_messages' );

		var percentage = $( '#nelioefi_progress .nelioefi_percentage' );
		var bar = $( '#nelioefi_progress .nelioefi_bar' );

		function regenerateThumbnails() {
			messageArea.text( 'Generating Thumbnails...' );
			generateButton.addClass( 'disabled' );
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'nelioefi_regenerate_thumbnails'
				},
				success: function() {
					generateButton.removeClass( 'disabled' );
					messageArea.text( 'Done!' );
				},
				error: function() {
					generateButton.removeClass( 'disabled' );
					messageArea.text( 'ERROR.' );
				}
			});
		}

		function fixImage( id, elem, total ) {
			var aux = Math.floor( elem * 100 / total ) + '%';
			percentage.text( elem + '/' + total + ' (' + aux + ')' );
			bar.css( 'width', aux );
			messageArea.append( '<div>Creating virtual attachment for post ' + id + '... </div>' );
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				async: false,
				data: {
					action: 'nelioefi_create_efi_attachment',
					post_id: id
				},
				success: function( msg ) {
					var aux = messageArea.find('div:last-child');
					aux.html( aux.html() + msg + '<br>\n' );
				},
				error: function() {
					var aux = messageArea.find('div:last-child');
					aux.html( aux.html() + 'ERROR' + '<br>\n' );
				}
			});
		}

		function process() {
			bar.css( 'width', '0' );
			percentage.text( '' );
			messageArea.text( 'Generating thumbnails...' );
			createButton.addClass( 'disabled' );
			generateButton.addClass( 'disabled' );
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'nelioefi_regenerate_thumbnails'
				},
				success: function() {
					messageArea.html( messageArea.text() + ' Done!<br>' );
					$.ajax({
						url: ajaxurl,
						data: {
							action: 'nelioefi_posts'
						},
						success: function( data ) {
							var i = 0;
							var len = data.length;
							var processNextElement = function() {
								if ( i < len ) {
									setTimeout( function() {
										fixImage( data[i], i + 1, len );
										++i;
										processNextElement();
									}, 0 );
								} else {
									createButton.removeClass( 'disabled' );
									generateButton.removeClass( 'disabled' );
								}
							}
							processNextElement();
						},
						error: function() {
							createButton.removeClass( 'disabled' );
							generateButton.removeClass( 'disabled' );
						}
					});
				},
				error: function() {
					messageArea.text( 'Something went wrong...' );
				}
			});
		}

		generateButton.on( 'click', regenerateThumbnails );
		createButton.on( 'click', process );
	})(jQuery);
</script>

<?php }



// AJAX CALLBACKS
// =========================================================

add_action( 'wp_ajax_nelioefi_regenerate_thumbnails', 'nelioefi_regenerate_thumbnails_ajax' );
function nelioefi_regenerate_thumbnails_ajax() {
	nelioefi_regenerate_thumbnails();
}

add_action( 'wp_ajax_nelioefi_posts', 'nelioefi_get_posts_with_nelioefi_ajax' );
function nelioefi_get_posts_with_nelioefi_ajax() {
	global $wpdb;
	$post_ids = $wpdb->get_col( $wpdb->prepare( "
		SELECT pm.post_id
		FROM   $wpdb->postmeta pm
		WHERE  pm.meta_key = %s AND
		       TRIM(pm.meta_value) <> ''
		",
		_nelioefi_url()
	) );
	wp_send_json( $post_ids );
}


add_action( 'wp_ajax_nelioefi_create_efi_attachment', 'nelioefi_create_efi_attachment_ajax' );
function nelioefi_create_efi_attachment_ajax() {
	if ( ! isset( $_POST['post_id'] ) ) {
		wp_send_json( 'Done!' );
	}
	$post_id = $_POST['post_id'];
	$attachment_id = get_post_meta( $post_id, '_thumbnail_id', true );
	$attachment = get_post( $attachment_id );
	if ( ! $attachment || $attachment->post_status !== 'nelioefi_hidden' ) {
		$url = get_post_meta( $post_id, _nelioefi_url(), true );
		if ( empty( $url ) ) {
			wp_send_json( 'Skip.' );
		}
		$aspect_ratio = get_post_meta( $post_id, _nelioefi_aspect_ratio(), true );
		if ( empty( $aspect_ratio ) ) {
			$aspect_ratio = '4:3';
		}
		$alt_text = get_post_meta( $post_id, '_nelioefi_alt', true );
		if ( empty( $alt_text ) ) {
			$alt_text = '';
		}
		$res = nelioefi_set_external_featured_image( $post_id, $url, $aspect_ratio, $alt_text, '', $alt_text );
		if ( $res ) {
			wp_send_json( 'Done!' );
		} else {
			wp_send_json( 'ERROR (attachment could not be created).' );
		}
	}
	wp_send_json( 'Skip (attachment already exists).' );
}


