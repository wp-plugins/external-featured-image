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
 * This function returns Nelio EFI's post meta key. The key can be changed
 * using the filter `nelioefi_post_meta_key'
 */
function _nelioefi_aspect_ratio() {
	return apply_filters( 'nelioefi_aspect_ratio_meta_key', '_nelioefi_aspect_ratio' );
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


function nelioefi_get_default_placeholder() {
	return apply_filters( 'nelioefi_default_placeholder',
		untrailingslashit( plugin_dir_url( dirname( __FILE__ ) ) ) .
			'/assets/default-placeholder.png' );
}


function nelioefi_regenerate_thumbnails( $aspect_ratio = 'all' ) {

	$thumbnails = get_option( 'nelioefi_thumbnails', array(
		'16-9-nelioefi-placeholder.png' => 0,
		'4-3-nelioefi-placeholder.png'  => 0,
		'1-1-nelioefi-placeholder.png'  => 0,
		'3-4-nelioefi-placeholder.png'  => 0,
		'9-16-nelioefi-placeholder.png' => 0,
		'16-9-nelioefi-placeholder.png' => 0
	) );

	switch ( $aspect_ratio ) {
		case '16:9':
			$name = '16-9-nelioefi-placeholder.png';
			$aux[$name] = $thumbnails[$name];
			break;
		case '4:3':
			$name = '4-3-nelioefi-placeholder.png';
			$aux[$name] = $thumbnails[$name];
			break;
		case '1:1':
			$name  = '1-1-nelioefi-placeholder.png';
			$aux[$name] = $thumbnails[$name];
			break;
		case '3:4':
			$name = '3-4-nelioefi-placeholder.png';
			$aux[$name] = $thumbnails[$name];
			break;
		case '9:16':
			$name = '9-16-nelioefi-placeholder.png';
			$aux[$name] = $thumbnails[$name];
			break;
		case 'all':
		default:
			$aux = $thumbnails;
	}

	foreach ( $aux as $filename => $id ) {
		$aux = wp_upload_dir();
		$src_path = dirname( dirname( __FILE__ ) );
		$src_path  = trailingslashit( trailingslashit( $src_path ) . 'assets' ) . $filename;
		$dest_path = trailingslashit( trailingslashit( $aux['basedir'] ) . 'nelioefi' );
		if ( file_exists( $dest_path ) ) {
			$dir_available = true;
		} else {
			$dir_available = mkdir( $dest_path );
		}
		$dest_path = $dest_path . $filename;

		// And, if we have been able to copy it there, we can create the attachment
		if ( $dir_available && copy( $src_path, $dest_path ) ) {
			$file_created = true;
		} else {
			$file_created = false;
		}

		if ( $file_created && ! get_post( $id ) ) {
			$ar = substr( $filename, 0, 4 );
			$ar = str_replace( '-', ':', $ar );
			if ( strpos( $ar, ':', 3 ) !== false ) {
				$ar = substr( $ar, 0, 3 );
			}
			$attachment = array(
				'post_title'     => 'Nelio EFI Placeholder (' . $ar . ')',
				'post_content'   => '',
				'post_excerpt'   => $filename,
				'post_status'    => 'inherit',
				'post_mime_type' => 'image/png',
				'guid'           => trailingslashit( $aux['baseurl'] ) . $filename
			);
			$feat_image_id = wp_insert_attachment( $attachment, $dest_path );

			if ( ! is_wp_error( $feat_image_id ) ) {
				$id = $feat_image_id;
			} else {
				$id = 0;
			}
		}

		if ( $id ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $id, $dest_path );
			wp_update_attachment_metadata( $id, $attach_data );
		}

		$aux[$filename] = $id;
		$thumbnails[$filename] = $id;
		// Since this is a fake featured image, we have to hide it.
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array( 'post_status' => 'nelioefi_hidden' ),
			array( 'ID' => $id )
		);

	}

	update_option( 'nelioefi_thumbnails', $thumbnails );
}


function nelioefi_set_external_featured_image(
		$post_id, $feat_image_url, $aspect_ratio = '16:9', $title = false, $descr = false, $caption = false ) {

	switch ( $aspect_ratio ) {
		case '16:9':
			$filename  = '16-9-nelioefi-placeholder.png';
			break;
		case '4:3':
			$filename  = '4-3-nelioefi-placeholder.png';
			break;
		case '1:1':
			$filename  = '1-1-nelioefi-placeholder.png';
			break;
		case '3:4':
			$filename  = '3-4-nelioefi-placeholder.png';
			break;
		case '9:16':
			$filename  = '9-16-nelioefi-placeholder.png';
			break;
		default:
			$filename  = '4-3-nelioefi-placeholder.png';
	}

	$feat_image_id = get_post_meta( $post_id, '_thumbnail_id', true );
	if ( $feat_image_id ) {
		$attachment = get_post( $feat_image_id );
		if ( $attachment ) {
			if ( 'nelioefi_hidden' === $attachment->post_status ) {
				$related_post_id = get_post_meta( $feat_image_id, '_nelioefi_related_post', true );
				if ( $related_post_id == $post_id ) {
					$old_aspect_ratio = get_post_meta( $post_id, _nelioefi_aspect_ratio(), true );
					if ( $old_aspect_ratio !== $aspect_ratio ) {
						wp_delete_post( $feat_image_id );
						delete_post_meta( $post_id, '_thumbnail_id' );
						$feat_image_id = false;
						nelioefi_regenerate_thumbnails( $old_aspect_ratio );
					}
				} else {
					$feat_image_id = false;
				}
			} else {
				delete_post_meta( $post_id, '_thumbnail_id' );
				$feat_image_id = false;
			}
		} else {
			delete_post_meta( $post_id, '_thumbnail_id' );
			$feat_image_id = false;
		}
	}

	if ( ! $feat_image_id ) {
		// We now create the placeholder in the media library
		$aux = wp_upload_dir();
		$src_path = dirname( dirname( __FILE__ ) );
		$src_path  = trailingslashit( trailingslashit( $src_path ) . 'assets' ) . $filename;
		$dest_path = trailingslashit( trailingslashit( $aux['basedir'] ) . 'nelioefi' ) . $filename;

		if ( false === $title ) $title = '';
		if ( false === $descr ) $descr = '';
		if ( false === $caption ) $caption = '';

		$attachment = array(
			'post_title'     => $title,
			'post_content'   => $descr,
			'post_excerpt'   => $caption,
			'post_status'    => 'inherit',
			'post_mime_type' => 'image/png',
			'guid'           => trailingslashit( $aux['baseurl'] ) . $filename
		);
		$feat_image_id = wp_insert_attachment( $attachment, $dest_path );

		if ( ! is_wp_error( $feat_image_id ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $feat_image_id, $dest_path );
			wp_update_attachment_metadata( $feat_image_id, $attach_data );
		} else {
			$feat_image_id = false;
		}
	}

	if ( $feat_image_id ) {
		$args = array( 'ID' => $feat_image_id );
		if ( $title !== false ) {
			$args['post_title'] = $title;
		}
		if ( $descr !== false ) {
			$args['post_content'] = $descr;
		}
		if ( $caption !== false ) {
			$args['post_excerpt'] = $caption;
		}
		if ( count( $args ) > 1 ) {
			wp_update_post( $args );
		}
		update_post_meta( $post_id, '_thumbnail_id', $feat_image_id );
		update_post_meta( $post_id, _nelioefi_url(), $feat_image_url );
		update_post_meta( $post_id, _nelioefi_aspect_ratio(), $aspect_ratio );
		update_post_meta( $feat_image_id, '_nelioefi_related_post', $post_id );

		// Since this is a fake featured image, we have to hide it.
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array( 'post_status' => 'nelioefi_hidden' ),
			array( 'ID' => $feat_image_id )
		);

	}

	return $feat_image_id;
}




/* =========================================================================*/
/* =========================================================================*/
/*             ALL HOOKS START HERE                                         */
/* =========================================================================*/
/* =========================================================================*/

// Modify the transparency gif.
add_filter( 'image_downsize', 'nelioefi_add_image_in_placeholder', 10, 3 );
function nelioefi_add_image_in_placeholder( $downsize, $attachment_id, $size ) {
	$result = false;

	$image = get_post( $attachment_id );
	if ( 'nelioefi_hidden' == $image->post_status ) {
		// Retrieve the proper thumbnail version
		remove_filter( 'image_downsize', 'nelioefi_add_image_in_placeholder', 10, 3 );
		$result = wp_get_attachment_image_src( $attachment_id, $size );
		add_filter( 'image_downsize', 'nelioefi_add_image_in_placeholder', 10, 3 );

		$post_id = get_post_meta( $attachment_id, '_nelioefi_related_post', true );
		$external_url = get_post_meta( $post_id, _nelioefi_url(), true );
		if ( empty( $external_url ) ) {
			$external_url = nelioefi_get_default_placeholder();
		}
		$result[0] = add_query_arg( 'nelioefi', urlencode( $external_url ), $result[0] );
	}

	return $result;
}


add_action( 'wp_footer', 'nelioefi_print_script' );
function nelioefi_print_script() {
	$default_image = nelioefi_get_default_placeholder();
	?>
	<script type="text/javascript">
	(function($) {
		var key = 'nelioefi';

		function setImage( elem, image ) {
			var value = 'url("' + decodeURIComponent( image ) + '") no-repeat center center';
			elem.css( 'background', value );
			elem.css( '-webkit-background-size', 'cover' );
			elem.css( '-moz-background-size', 'cover' );
			elem.css( '-o-background-size', 'cover' );
			elem.css( 'background-size', 'cover' );
		};

		function fixImages( elem ) {
			// Regular images that use the placeholder in the SRC
			$( 'img[src*=nelioefi-placeholder]', elem ).each( function() {
				try {
					var src = $( this ).attr( 'src' );
					var imageUrl = src.substring( src.indexOf( key + '=' ) + key.length + 1 );
					if ( imageUrl.indexOf( '&' ) > 0 ) {
						imageUrl = imageUrl.substring( 0, imageUrl.indexOf( '&' ) );
					}
					setImage( $( this ), imageUrl );
				} catch ( e ) {}
			});
			<?php
			$elements = apply_filters( 'nelioefi_background_elements', array() );
			if ( count( $elements ) > 0 ) {
			$rule = implode( ', ', $elements );
			?>
				// Other elements that might use the placeholder as in the background CSS property
				try {
					$( '<?php echo $rule; ?>', elem ).each(function() {
						if ( $( this ).css( 'background' ).indexOf( 'nelioefi-placeholder' ) > 0 ) {
							var bg = $( this ).css( 'background' );
							imageUrl = bg.substring( bg.indexOf( key + '=' ) + key.length + 1 );
							try {
								imageUrl = imageUrl.match( /^[0-9]+/ )[0];
								setImage( $( this ), imageUrl );
							} catch ( e ) {}
						}
					});
				} catch ( e ) {}
			<?php
			} ?>
		}

		// Replacing images
		fixImages( $( 'body' ) );
		$( 'body' ).bind( 'DOMNodeInserted', function( ev ) {
			try {
				fixImages( $( ev.srcElement ) );
			} catch ( e ) {}
		});

	})(jQuery);
	</script>
<?php
}

