<?php

// Overriding post thumbnail when necessary
add_filter( 'post_thumbnail_html', 'nelioefi_replace_thumbnail', 10, 3 );
function nelioefi_replace_thumbnail( $html, $post_id, $post_image_id ) {
	$image_url = get_post_meta( $post_id, '_nelioefi_url', true );
	if ( $image_url && strlen( $image_url ) > 0 )
		$html = '<div class="nelioefi"><img src="' . $image_url . '" class="wp-post-image" /></div>';
	return $html;
}

