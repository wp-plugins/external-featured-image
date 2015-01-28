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


/**
 * This function prints an image tag with the external featured image (if any).
 * This tag, in fact, has a 1x1 px transparent gif image as its src, and
 * includes the external featured image via inline CSS styling.
 */
function nelioefi_the_html_thumbnail( $id, $size = false, $attr = array() ) {
	if ( uses_nelioefi( $id ) )
		echo nelioefi_get_html_thumbnail( $id );
}


/**
 * This function returns the image tag with the external featured image (if
 * any). This tag, in fact, has a 1x1 px transparent gif image as its src,
 * and includes the external featured image via inline CSS styling.
 */
function nelioefi_get_html_thumbnail( $id, $size = false, $attr = array() ) {
	if ( uses_nelioefi( $id ) === false )
		return false;

	$image_url = nelioefi_get_thumbnail_src( $id );

	$width = false;
	$height = false;
	$additional_classes = '';

	global $_wp_additional_image_sizes;
	if ( is_array( $size ) ) {
		$width = $size[0];
		$height = $size[1];
	}
	else if ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
		$width = $_wp_additional_image_sizes[ $size ]['width'];
		$height = $_wp_additional_image_sizes[ $size ]['height'];
		$additional_classes = 'attachment-' . $size . ' ';
	}

	if ( $width && $width > 0 ) $width = "width:${width}px;";
	else $width = '';

	if ( $height && $height > 0 ) $height = "height:${height}px;";
	else $height = '';

	if ( isset( $attr['class'] ) )
		$additional_classes .= $attr['class'];

	$alt = get_post_meta( $id, '_nelioefi_alt', true );
	if ( isset( $attr['alt'] ) )
		$alt = $attr['alt'];
	if ( !$alt )
		$alt = '';

	if ( is_feed() ) {
		$style = '';
		if ( isset( $attr['style'] ) )
			$style = 'style="' . $attr['style'] . '" ';
		$html = sprintf(
			'<img src="%s" %s' .
			'class="%s wp-post-image nelioefi" '.
			'alt="%s" />',
			$image_url, $style, $additional_classes, $alt );
	}
	else {
		$html = sprintf(
			'<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" ' .
			'style="background:url(\'%s\') no-repeat center center;' .
			'-webkit-background-size:cover;' .
			'-moz-background-size:cover;' .
			'-o-background-size:cover;' .
			'background-size:cover;' .
			'%s%s" class="%s wp-post-image nelioefi" '.
			'alt="%s" />',
			$image_url, $width, $height, $additional_classes, $alt );
	}

	return $html;
}


/* =========================================================================*/
/* =========================================================================*/
/*             ALL HOOKS START HERE                                         */
/* =========================================================================*/
/* =========================================================================*/

// Overriding post thumbnail when necessary
add_filter( 'genesis_pre_get_image', 'nelioefi_genesis_thumbnail', 10, 3 );
function nelioefi_genesis_thumbnail( $unknown_param, $args, $post ) {
	$image_url = get_post_meta( $post->ID, _nelioefi_url(), true );

	if ( !$image_url || strlen( $image_url ) == 0 ) {
		return false;
	}

	if ( $args['format'] == 'html' ) {
		$html = nelioefi_replace_thumbnail( '', $post->ID, 0, $args['size'], $args['attr'] );
		$html = str_replace( 'style="', 'style="min-width:150px;min-height:150px;', $html );
		return $html;
	}
	else {
		return $image_url;
	}
}


// Overriding post thumbnail when necessary
add_filter( 'post_thumbnail_html', 'nelioefi_replace_thumbnail', 10, 5 );
function nelioefi_replace_thumbnail( $html, $post_id, $post_image_id, $size, $attr ) {
	if ( uses_nelioefi( $post_id ) )
		$html = nelioefi_get_html_thumbnail( $post_id, $size, $attr );
	return $html;
}


add_action( 'the_post', 'nelioefi_fake_featured_image_if_necessary' );
function nelioefi_fake_featured_image_if_necessary( $post ) {
	if ( is_array( $post ) ) $post_ID = $post['ID'];
	else $post_ID = $post->ID;

	$has_nelioefi = strlen( get_post_meta( $post_ID, _nelioefi_url(), true ) ) > 0;
	$wordpress_featured_image = get_post_meta( $post_ID, '_thumbnail_id', true );

	if ( $has_nelioefi && !$wordpress_featured_image )
		update_post_meta( $post_ID, '_thumbnail_id', -1 );
	if ( !$has_nelioefi && $wordpress_featured_image == -1 )
		delete_post_meta( $post_ID, '_thumbnail_id' );
}



