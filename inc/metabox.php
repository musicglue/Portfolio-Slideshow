<?php 

if ( ! ps_plugin_is_active('portfolio-framework') ) {

	add_action( 'add_meta_boxes', 'ps_add_custom_box' );

	function ps_add_custom_box() {
		add_meta_box('ps_custom_post_type_uploads', __('Portfolio Slideshow', 'port_slide'), 'ps_custom_post_type_uploads', 'post', 'normal', 'default');
		add_meta_box('ps_custom_post_type_uploads', __('Portfolio Slideshow', 'port_slide'), 'ps_custom_post_type_uploads', 'page', 'normal', 'default');
	}

	function ps_metabox_extras() {
		global $ps_options;
		wp_register_style( 'ps-custom-post-type', plugins_url( 'admin/css/ps-custom-post-type.css', dirname(__FILE__) ), false, $ps_options['version'], 'screen' ); 
		wp_enqueue_style( 'ps-custom-post-type' );
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_register_script( 'ps-custom-post-type', plugins_url( 'admin/js/ps-custom-post-type.js', dirname(__FILE__) ), false, $ps_options['version'], true); 
		wp_enqueue_script( 'ps-custom-post-type' );	
	}

	if (is_admin() && $pagenow=='post-new.php' OR $pagenow=='post.php') {
		add_action('init', 'ps_metabox_extras');
	}
		
	function ps_custom_post_type_uploads() {
		global $post;
	 
		echo '<p>

		 <a href="media-upload.php?post_id='.$post->ID.'&#038;type=image&#038;TB_iframe=1"  class="thickbox" ><input type="submit" name="Save" value="' . __( 'Upload and manage images', 'portfolio-slideshow-pro' ) . '" class="button-secondary" /></a></p><br />';

		$attachments = get_posts( array(
			'order'          => 'ASC',
			'orderby' 		 => 'menu_order ID',
			'post_type'      => 'attachment',
			'post_parent'    => $post->ID,
			'post_mime_type' => 'image',
			'post_status'    => null,
			'numberposts'    => -1,
			'size'			 => 'thumbnail') 
		);

	  if ( $attachments ) {
				echo '<ul id="images">';
			$i = 0;
			foreach ( $attachments as $attachment ) {
				echo '<li id="'. $attachment->ID .'">' . wp_get_attachment_image( $attachment->ID, array(50,50), false, false) . '</li>';
				$i++;
			}
			echo '</ul><div class="instructions"><small>' . __( 'Drag and drop to re-order', 'portfolio-slideshow-pro' ) . '</small><p>' . __( '<strong>Instructions</strong><br />To add this slideshow to a post, use the shortcode <code>[portfolio_slideshow id=', 'portfolio-slideshow-pro' ) . $post->ID . __( ']</code>.<br />To add it directly to your template (header, sidebar, homepage, etc.), add this PHP to your template:<br /><code>&lt;?php echo do_shortcode(\'[portfolio_slideshow id=', 'portfolio-slideshow-pro' ) . $post->ID . __( ']\');?&gt;</code></p>' ) . '</div>';
		} else { echo '<div class="instructions"><p>' . __( 'Be sure to save your changes in the gallery uploader, then click "Save Draft" to update this page for further instructions.', 'portfolio-slideshow-pro' ) . '</p></div>';}
		/* Allow other plugins to insert content here */
	}
				
	function ps_save_item_order() { //to save when we update the sort order
		global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
		foreach ($order as $item_id) {
	
			$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id) );
			$counter++;
		}
		die(1);
	}
	add_action('wp_ajax_item_sort', 'ps_save_item_order');
	add_action('wp_ajax_nopriv_item_sort', 'ps_save_item_order');
}