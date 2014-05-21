<?php

// Creating box
add_action( 'add_meta_boxes', 'nelioefi_add_url_metabox' );
function nelioefi_add_url_metabox() {

	add_meta_box(
		'nelioefi_url_metabox',
		'External Featured Image',
		'nelioefi_url_metabox',
		'post',
		'side',
		'default'
	);

	add_meta_box(
		'nelioefi_url_metabox',
		'External Featured Image',
		'nelioefi_url_metabox',
		'page',
		'side',
		'default'
	);

}

function nelioefi_url_metabox( $post ) {
	$nelioefi_url = get_post_meta( $post->ID, '_nelioefi_url', true );
	$has_img = strlen( $nelioefi_url ) > 0;

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

	<?php
	if ( strlen( $nelioefi_url ) > 0 ) { ?>
		<a id="nelioefi_remove_button" href="#" onClick="javascript:nelioefiRemoveFeaturedImage();">Remove featured image</a>
		<script>
		function nelioefiRemoveFeaturedImage() {
			jQuery("#nelioefi_preview_block").hide();
			jQuery("#nelioefi_image_wrapper").hide();
			jQuery("#nelioefi_remove_button").hide();
			jQuery("#nelioefi_url").val('');
			jQuery("#nelioefi_controls").show();
		}
		</script>
		<?php
	} ?>
	</div><?php

	if ( $has_img ) { ?>
	<div id="nelioefi_controls" style="display:none;"><?php
	} else { ?>
	<div id="nelioefi_controls"><?php
	} ?>
		<input type="text" placeholder="Image URL" style="width:100%;margin-top:10px;"
			id="nelioefi_url" name="nelioefi_url"
			value="<?php echo esc_attr( $nelioefi_url ); ?>" />
		<div style="text-align:right;margin-top:10px;">
			<a class="button" id="ext_feat_img-preview" onClick="javascript:nelioefiPreview();">Preview</a>
			<script>
			function nelioefiPreview() {
				jQuery("#nelioefi_preview_block").show();
				jQuery("#nelioefi_image_wrapper").css('background-image', "url('" + jQuery("#nelioefi_url").val() + "')" );
				jQuery("#nelioefi_image_wrapper").show();
			}
			</script>
		</div>
	</div>
	<?php
}

add_action( 'save_post', 'nelioefi_save_url' );
function nelioefi_save_url( $post_ID ) {
	if ( isset( $_POST['nelioefi_url'] ) )
		update_post_meta( $post_ID, '_nelioefi_url', strip_tags( $_POST['nelioefi_url'] ) );
}


add_action( 'the_post', 'nelioefi_fake_featured_image_if_necessary' );
function nelioefi_fake_featured_image_if_necessary( $post ) {
	if ( is_array( $post ) ) $post_ID = $post['ID'];
	else $post_IDid = $post->ID;
	
	$has_nelioefi = strlen( get_post_meta( $post_ID, '_nelioefi_url', true ) ) > 0;
	$wordpress_featured_image = get_post_meta( $post_ID, '_thumbnail_id', true );

	if ( $has_nelioefi && !$wordpress_featured_image )
		update_post_meta( $post_ID, '_thumbnail_id', -1 );
	if ( !$has_nelioefi && $wordpress_featured_image == -1 )
		delete_post_meta( $post_ID, '_thumbnail_id' );

}

