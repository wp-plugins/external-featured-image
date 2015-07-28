<?php

/* =========================================================================*/
/* =========================================================================*/
/*             SOME USEFUL FUNCTIONS                                        */
/* =========================================================================*/
/* =========================================================================*/

/**
 * This function returns Nelio EFI's post meta key. The key can be changed
 * using the filter `nelioefi_post_meta_key'
 */
function _nelioefi_url() {
	return apply_filters( 'nelioefi_post_meta_key', '_nelioefi_url' );
}

/**
 * This function returns whether the post whose id is $id uses an external
 * featured image or not
 */
function uses_nelioefi( $id ) {
	$image_url = nelioefi_get_thumbnail_src( $id );
	if ( $image_url === false )
		return false;
	else
		return true;
}


/**
 * This function returns the URL of the external featured image (if any), or
 * false otherwise.
 */
function nelioefi_get_thumbnail_src( $id ) {
	$image_url = get_post_meta( $id, _nelioefi_url(), true );
	if ( !$image_url || strlen( $image_url ) == 0 )
		return false;
	return $image_url;
}


$nelioab_first_time = true;
function nelioefi_get_placeholder() {
	$image_id = get_option( 'nelioefi_placeholder_id', false );

	global $nelioab_first_time;
	if ( $image_id && $nelioab_first_time ) {
		$nelioab_first_time = false;
		$aux = get_post( $image_id );
		if ( empty( $aux ) ) {
			update_option( 'nelioefi_placeholder_id', false );
			$image_id = false;
		}
	}

	if ( ! $image_id ) {
		$aux = wp_upload_dir();
		$filename  = 'nelioefi-placeholder.png';
		$src_path  = trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'assets' . DIRECTORY_SEPARATOR . $filename;
		$dest_path = trailingslashit( $aux['basedir'] ) . $filename;
		if ( copy( $src_path, $dest_path ) ) {
			$attachment = array(
				'post_mime_type' => 'image/png',
				'post_title'     => __( 'NelioEFI\'s Placeholder', 'nelioefi' ),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'guid'           => trailingslashit( $aux['baseurl'] ) . $filename
			);
			$url = trailingslashit( $aux['baseurl'] );
			$url = str_replace( 'https://', '', $url );
			$url = str_replace( 'http://',  '', $url );
			$url = substr( $url, strpos( $url, '/' ) );
			$filename = $url . $filename;
			$image_id = wp_insert_attachment( $attachment, $filename );
			if ( ! is_wp_error( $image_id ) ) {
				update_option( 'nelioefi_placeholder_id', $image_id );
			}
		}
	}

	return get_option( 'nelioefi_placeholder_id', false );
}


/* =========================================================================*/
/* =========================================================================*/
/*             ALL HOOKS START HERE                                         */
/* =========================================================================*/
/* =========================================================================*/

// Set the featured image ID of the post to the transparency gif.
add_action( 'the_post', 'nelioefi_fake_featured_image_if_necessary' );
function nelioefi_fake_featured_image_if_necessary( $post ) {
	if ( is_array( $post ) ) $post_ID = $post['ID'];
	else $post_ID = $post->ID;

	$wp_featured_image = get_post_meta( $post_ID, '_thumbnail_id', true );
	$aux = get_post( $wp_featured_image );
	if ( $wp_featured_image && empty( $aux ) ) {
		$wp_featured_image = false;
	}

	if ( uses_nelioefi( $post_ID ) && !$wp_featured_image ) {
		update_post_meta( $post_ID, '_thumbnail_id', nelioefi_get_placeholder() );
	}
	if ( ! uses_nelioefi( $post_ID ) && $wp_featured_image == -1 ) {
		delete_post_meta( $post_ID, '_thumbnail_id' );
	}

}


$nelioefi_images = array();

// Modify the transparency gif.
add_filter( 'image_downsize', 'nelioefi_add_image_in_placeholder', 10, 3 );
function nelioefi_add_image_in_placeholder( $downsize, $id, $size ) {
	if ( nelioefi_get_placeholder() == $id ) {
		remove_filter( 'image_downsize', 'nelioefi_add_image_in_placeholder', 10, 3 );
		$result = wp_get_attachment_image_src( $id, $size );
		add_filter( 'image_downsize', 'nelioefi_add_image_in_placeholder', 10, 3 );

		$nelioefi = get_post_meta( get_the_ID(), _nelioefi_url(), true );
		if ( $nelioefi ) {
			global $nelioefi_images;
			$nelioefi_images[get_the_ID()] = $nelioefi;
			$result[0] = $result[0] . '?id=' . get_the_ID();
			return $result;
		}
	}
	return false;
}


add_action( 'wp_footer', 'nelioefi_print_script' );
function nelioefi_print_script() {
	global $nelioefi_images; ?>
	<script type="text/javascript">
	(function($) {
		var images = <?php echo json_encode( $nelioefi_images ); ?>;
		$('img[src*=nelioefi-placeholder]').each(function() {
			try {
				var src = $(this).attr( 'src' );
				var id = src.substring( src.indexOf( '=' ) + 1 );
				var value = 'url("' + images[id] + '") no-repeat center center';
				$(this).css( 'background', value );
				$(this).css( '-webkit-background-size', 'cover' );
				$(this).css( '-moz-background-size', 'cover' );
				$(this).css( '-o-background-size', 'cover' );
				$(this).css( 'background-size', 'cover' );
			} catch ( e ) {}
		});
	})(jQuery);
	</script>
<?php
}

