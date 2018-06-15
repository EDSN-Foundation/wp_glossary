<?php
global $post;

// Get WordPress' media upload URL
$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );

// See if there's a media id already saved as post meta
$wp_glossary_img_id = get_post_meta( $post->ID, 'wp_glossary_custom_thumbnail', true );

// Get the image src
$wp_glossary_img_src = wp_get_attachment_image_src( $wp_glossary_img_id, 'full' );

// For convenience, see if the array is valid
$have_img = is_array( $wp_glossary_img_src );
?>

<!-- Your image container, which can be manipulated with js -->
<div class="custom-img-container">
    <?php if ( $have_img ) : ?>
        <img src="<?php echo $wp_glossary_img_src[0] ?>" alt="" style="max-width:100%;" />
    <?php endif; ?>
</div>

<!-- Your add & remove image links -->
<p class="hide-if-no-js">
    <a class="upload-custom-img <?php if ( $have_img  ) { echo 'hidden'; } ?>" 
       href="<?php echo $upload_link ?>">
        <?php _e('Set custom image',WPG_TEXT_DOMAIN) ?>
    </a>
    <a class="delete-custom-img <?php if ( ! $have_img  ) { echo 'hidden'; } ?>" 
      href="#">
        <?php _e('Remove this image',WPG_TEXT_DOMAIN) ?>
    </a>
</p>

<!-- A hidden input to set and post the chosen image id -->
<input class="custom-img-id" name="custom-img-id" type="hidden" value="<?php echo esc_attr( $wp_glossary_img_id ); ?>" />
<!-- Manually create the nonce. First field should be your meta box id, second appends _nonce to your meta box id -->
<?php wp_nonce_field('wpg-thumbnail-meta-box', 'wp_glossary_img_nonce' ); ?>