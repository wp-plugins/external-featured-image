<?php

// Creating box
add_action( 'add_meta_boxes', 'nelioefi_add_url_metabox' );
function nelioefi_add_url_metabox() {

	$excluded_post_types = array(
		'attachment', 'revision', 'nav_menu_item', 'wpcf7_contact_form',
	);

	foreach ( get_post_types( '', 'names' ) as $post_type ) {
		if ( in_array( $post_type, $excluded_post_types ) )
			continue;
		add_meta_box(
			'nelioefi_url_metabox',
			'External Featured Image',
			'nelioefi_url_metabox',
			$post_type,
			'side',
			'default'
		);
	}

}


function nelioefi_url_metabox( $post ) {
	$nelioefi_url = get_post_meta( $post->ID, _nelioefi_url(), true );

	$nelioefi_title = '';
	$nelioefi_caption = '';
	$nelioefi_aspect_ratio = '16:9';

	$attachment_id = get_post_meta( $post->ID, '_thumbnail_id', true );
	$attachment = get_post( $attachment_id );
	if ( $attachment && 'nelioefi_hidden' == $attachment->post_status ) {
		$nelioefi_title = $attachment->post_title;
		$nelioefi_caption = $attachment->post_excerpt;
		$nelioefi_aspect_ratio = get_post_meta( $post->ID, _nelioefi_aspect_ratio(), true );
	}

	$has_img = strlen( $nelioefi_url ) > 0;
	if ( $has_img ) {
		$hide_if_img = 'display:none;';
		$show_if_img = '';
	}
	else {
		$hide_if_img = '';
		$show_if_img = 'display:none;';
	}
	?>
	<div id="nelioefi_meta_options" style="<?php echo $show_if_img; ?>">
		<strong><?php _e( 'Title', 'nelioefi' )?></strong><br>
		&nbsp;&nbsp;<input type="text" id="nelioefi_title" name="nelioefi_title"
			value="<?php echo esc_attr( $nelioefi_title ); ?>" /><br><br>

		<strong><?php _e( 'Caption', 'nelioefi' )?></strong><br>
		&nbsp;&nbsp;<input type="text" id="nelioefi_caption" name="nelioefi_caption"
			value="<?php echo esc_attr( $nelioefi_caption ); ?>" /><br><br>

		<strong><?php _e( 'Aspect Ratio', 'nelioefi' )?></strong><br>
		&nbsp;&nbsp;<select id="nelioefi_aspect_ratio" name="nelioefi_aspect_ratio">
			<option <?php if ( '16:9' == $nelioefi_aspect_ratio ) echo 'selected="selected"'; ?> value="16:9"><?php _e( 'Super Panoramic', 'nelioefi' ); ?></option>
			<option <?php if ( '4:3' == $nelioefi_aspect_ratio ) echo 'selected="selected"'; ?> value="4:3"><?php _e( 'Panoramic', 'nelioefi' ); ?></option>
			<option <?php if ( '1:1' == $nelioefi_aspect_ratio ) echo 'selected="selected"'; ?> value="1:1"><?php _e( 'Squared', 'nelioefi' ); ?></option>
			<option <?php if ( '3:4' == $nelioefi_aspect_ratio ) echo 'selected="selected"'; ?> value="3:4"><?php _e( 'Portrait', 'nelioefi' ); ?></option>
			<option <?php if ( '9:16' == $nelioefi_aspect_ratio ) echo 'selected="selected"'; ?> value="9:16"><?php _e( 'Super Portrait', 'nelioefi' ); ?></option>
		</select>
	</div><?php
	if ( $has_img ) { ?>
	<div id="nelioefi_preview_block"><?php
	} else { ?>
	<div id="nelioefi_preview_block" style="display:none;"><?php
	} ?>
		<div id="nelioefi_image_wrapper" style="<?php
			echo (
				'width:100%;' .
				'max-width:300px;' .
				'height:200px;' .
				'margin-top:10px;' .
				'background:url(' . $nelioefi_url . ') no-repeat center center; ' .
				'-webkit-background-size:cover;' .
				'-moz-background-size:cover;' .
				'-o-background-size:cover;' .
				'background-size:cover;' );
			?>">
		</div>

	<a id="nelioefi_remove_button" href="#" onClick="javascript:nelioefiRemoveFeaturedImage();" style="<?php echo $show_if_img; ?>">Remove featured image</a>
	<script>
	function nelioefiRemoveFeaturedImage() {
		jQuery("#nelioefi_preview_block").hide();
		jQuery("#nelioefi_image_wrapper").hide();
		jQuery("#nelioefi_remove_button").hide();
		jQuery("#nelioefi_meta_options").hide();
		jQuery("#nelioefi_title").val('');
		jQuery("#nelioefi_caption").val('');
		jQuery("#nelioefi_url").val('');
		jQuery("#nelioefi_url").show();
		jQuery("#nelioefi_preview_button").parent().show();
	}
	function nelioefiPreview() {
		jQuery("#nelioefi_preview_block").show();
		jQuery("#nelioefi_image_wrapper").css('background-image', "url('" + jQuery("#nelioefi_url").val() + "')" );
		jQuery("#nelioefi_image_wrapper").show();
		jQuery("#nelioefi_remove_button").show();
		jQuery("#nelioefi_meta_options").show();
		jQuery("#nelioefi_url").hide();
		jQuery("#nelioefi_preview_button").parent().hide();
	}
	<?php if ( $has_img ) { ?> jQuery("head").append("<style>#postimagediv{display:none}</style>"); <?php } ?>
	</script>
	</div>
	<input type="text" placeholder="Image URL" style="width:100%;margin-top:10px;<?php echo $hide_if_img; ?>"
		id="nelioefi_url" name="nelioefi_url"
		value="<?php echo esc_attr( $nelioefi_url ); ?>" />
	<div style="text-align:right;margin-top:10px;<?php echo $hide_if_img; ?>">
		<a class="button" id="nelioefi_preview_button" onClick="javascript:nelioefiPreview();">Preview</a>
	</div>
	<?php
}

add_action( 'save_post', 'nelioefi_save_efi_information', 1, 99 );
function nelioefi_save_efi_information( $post_id ) {

	// Sometimes, we save a revision (for previewing).
	// We just need to make sure that we always edit the original attachment.
	if ( $aux = wp_is_post_revision( $post_id ) ) {
		$post_id = $aux;
	}

	if ( isset( $_POST['nelioefi_url'] ) ) {
		$url = strip_tags( $_POST['nelioefi_url'] );
	} else {
		$url = '';
	}

	if ( empty( $url ) ) {

		// Remove
		$feat_image_id = get_post_meta( $post_id, '_thumbnail_id', true );
		if ( $feat_image_id ) {
			$attachment = get_post( $feat_image_id );
			if ( $attachment && 'nelioefi_hidden' === $attachment->post_status ) {
				wp_delete_post( $feat_image_id );
				delete_post_meta( $post_id, '_thumbnail_id' );
				delete_post_meta( $post_id, _nelioefi_url() );
				delete_post_meta( $post_id, _nelioefi_aspect_ratio() );
			}
		}

	} else {

		if ( isset( $_POST['nelioefi_aspect_ratio'] ) ) {
			$aspect_ratio = $_POST['nelioefi_aspect_ratio'];
		} else {
			$aspect_ratio = '4:3';
		}

		if ( isset( $_POST['nelioefi_title'] ) ) {
			$title = $_POST['nelioefi_title'];
		} else {
			$title = '';
		}

		if ( isset( $_POST['nelioefi_caption'] ) ) {
			$caption = $_POST['nelioefi_caption'];
		} else {
			$caption = '';
		}

		nelioefi_set_external_featured_image( $post_id, $url, $aspect_ratio, $title, '', $caption );
	}

}

