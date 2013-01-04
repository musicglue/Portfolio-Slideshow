<?php 

$ps_options =  get_option( "portfolio_slideshow_options" );

if ( $ps_options['version']  < PORTFOLIO_SLIDESHOW_VERSION ) { // If the version numbers don't match, run the upgrade script
	require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/upgrader.php' );
}


/* A small function to determine if a particular plugin is active */
if ( ! function_exists( 'ps_plugin_is_active' ) ) {
	function ps_plugin_is_active($plugin_var) {
		return in_array( $plugin_var. '/' .$plugin_var. '.php', apply_filters( 'active_plugins',get_option( 'active_plugins' ) ) );
	}
}

require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/metabox.php' );

$ps_options = get_option( 'portfolio_slideshow_options' ); 

//lets set up the shortcode
require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/shortcode.php' );

//allows us to run the shortcode in widgets
add_filter( 'widget_text', 'do_shortcode' );

if ( ! function_exists( 'add_post_id' ) ) {
	// put the attachment ID on the media page
	function add_post_id( $content ) { 
		$showlink = "Attachment ID:" . get_the_ID( $post->ID, true );
		$content[] = $showlink;
		return $content;
	}
	add_filter ( 'media_row_actions', 'add_post_id' );
}

	if ( ! function_exists( 'ps_action_links' ) ) {
	//action link http://www.wpmods.com/adding-plugin-action-links
	function ps_action_links( $links, $file ) {
	 	static $this_plugin;

	    if ( !$this_plugin ) {
	        $this_plugin = PORTFOLIO_SLIDESHOW_LOCATION;
	    }

	    // check to make sure we are on the correct plugin
	    if ( $file == $this_plugin ) {
	        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
	        $settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=portfolio_slideshow">Settings</a>';
	        // add the link to the list
	        array_unshift( $links, $settings_link );
	    }
	    return $links;
	}
	add_filter( 'plugin_action_links', 'ps_action_links', 10, 2 );
}

if ( ! function_exists( 'ps_image_attachment_fields_to_edit' ) ) {
	//Adds custom fields to attachment page http://wpengineer.com/2076/add-custom-field-attachment-in-wordpress/
	function ps_image_attachment_fields_to_edit( $form_fields, $post) {  
		$form_fields['ps_image_link'] = array(  
			"label" => __( "<span style='color:#c43; padding:0'>Portfolio Slideshow<br />Slide link URL</span>" ),  
			"input" => "text",
			"value" => get_post_meta( $post->ID, "_ps_image_link", true )  
		);        
		return $form_fields;  
	}  
	add_filter( "attachment_fields_to_edit", "ps_image_attachment_fields_to_edit", null, 2 ); 
}

if ( ! function_exists( 'ps_image_attachment_fields_to_save' ) ) {
	function ps_image_attachment_fields_to_save( $post, $attachment) {    
		if( isset( $attachment['ps_image_link'] ) ){  
			update_post_meta( $post['ID'], '_ps_image_link', $attachment['ps_image_link'] );  
		}  
		return $post;  
	}  
	add_filter( "attachment_fields_to_save", "ps_image_attachment_fields_to_save", null, 2 );
}	

if ( ! function_exists( 'ps_get_image_sizes' ) ) {
	//Create a list of image sizes to use in the dropdown
	function ps_get_image_sizes() {
		global $ps_options;

		// Get the intermediate image sizes, add full & custom sizes size to the array.
		$sizes = get_intermediate_image_sizes();
		$sizes[] = 'full';

		// Loop through each of the image sizes.
		foreach ( $sizes as $size ) {
			echo "<option value='$size'";
			if ( $ps_options['size'] == $size ){
				echo " selected='selected'"; 
			}
			echo ">$size</option>";
		}
	}
}


add_action('init', 'ps_setup');

if ( !function_exists('ps_setup') ) {

	function ps_setup() {
		global $ps_options;
		
		if( ! is_admin() ){
		  // Output the javascript & css here
		   
		   // jQuery  
			if ( $ps_options['jquery'] == "force" ) {
				wp_deregister_script( 'jquery' ); 
				wp_register_script( 'jquery', ( "http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" ), false, '1', false); 
				wp_enqueue_script( 'jquery' );
			} elseif ( $ps_options['jquery'] == "true" || $ps_options['jquery'] == "wp" ) {
				wp_enqueue_script( 'jquery' );
			}
			
			//malsup cycle script
			wp_register_script( 'cycle', plugins_url( 'js/jquery.cycle.all.min.js', dirname(__FILE__) ), false, '2.99', true ); 
			wp_enqueue_script( 'cycle' );
			
		
			if ( $ps_options['debug'] == "true" ) {
				//our script
				wp_enqueue_script( 'portfolio-slideshow', plugins_url( 'js/portfolio-slideshow.js', dirname(__FILE__) ), false, $ps_options['version'], true ); 
				//our style 
				wp_enqueue_style( 'portfolio_slideshow', plugins_url( "css/portfolio-slideshow.css", dirname(__FILE__) ), false, $ps_options['version'], 'screen' );
			} else {
				wp_enqueue_script( 'portfolio-slideshow', plugins_url( 'js/portfolio-slideshow.min.js', dirname(__FILE__) ), false, $ps_options['version'], true ); 
				wp_enqueue_style( 'portfolio_slideshow', plugins_url( "css/portfolio-slideshow.min.css", dirname(__FILE__) ), false, $ps_options['version'], 'screen' ); 
			}
			
		} else { /* If we're on the admin page */
		
			if ( isset( $_GET['page'] ) && $_GET['page'] == "portfolio_slideshow" ) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_script( 'portfolio-slideshow-admin', PORTFOLIO_SLIDESHOW_URL . '/admin/js/portfolio-slideshow-admin.js', false, $ps_options['version'], true); 
				wp_enqueue_style( 'portfolio-slideshow-admin', PORTFOLIO_SLIDESHOW_URL . '/admin/css/portfolio-slideshow-admin.css', false, $ps_options['version'], 'screen' ); 
			}
		}	
	}
}

if ( ! function_exists( 'portfolio_slideshow_head' ) ) {
	function portfolio_slideshow_head() {
		global $ps_count, $ps_options;
		echo '
<!-- Portfolio Slideshow-->
<noscript><link rel="stylesheet" type="text/css" href="' .  plugins_url( "css/portfolio-slideshow-noscript.css?ver=" . $ps_options['version'], dirname(__FILE__) ) . '" /></noscript>';

	echo '<script type="text/javascript">/* <![CDATA[ */var psTimeout = new Array();  var psAutoplay = new Array();  var psFluid = new Array(); var psTrans = new Array(); var psSpeed = new Array(); var psLoop = new Array();/* ]]> */</script>
<!--//Portfolio Slideshow-->
';
	} // end portfolio_head 
}

add_action( 'wp_head', 'portfolio_slideshow_head' );

if ( ! function_exists( 'portfolio_slideshow_foot' ) ) {
	function portfolio_slideshow_foot() {
		global $ps_options;
		// Set up js variables
		//$ps_showhash should always be false on any non-singular page
		if ( !is_singular() ) { $ps_options['showhash'] = 0; }
echo "<script type='text/javascript'>/* <![CDATA[ */ var portfolioSlideshowOptions = {  psHash:$ps_options[showhash], psLoader:$ps_options[showloader], psFluid:$ps_options[allowfluid] };/* ]]> */</script>";
	}    
}

add_action( 'wp_footer', 'portfolio_slideshow_foot' );
?>
