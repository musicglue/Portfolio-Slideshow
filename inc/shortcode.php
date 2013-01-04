<?php

// create the shortcode
add_shortcode( 'portfolio_slideshow', 'portfolio_slideshow_shortcode' );

// define the shortcode function
function portfolio_slideshow_shortcode( $atts ) {
	
	STATIC $i=0;

	global $ps_options;

	extract( shortcode_atts( array(
		'size' => $ps_options['size'],
		'nowrap' => '',
		'loop' => $ps_options['loop'],
		'speed' => $ps_options['speed'],
		'trans' => $ps_options['trans'],
		'timeout' => $ps_options['timeout'],
		'exclude_featured'	=> $ps_options['exclude_featured'],
		'autoplay' => $ps_options['autoplay'],
		'pagerpos' => $ps_options['pagerpos'],
		'navpos' => $ps_options['navpos'],
		'showcaps' => $ps_options['showcaps'],
		'showtitles' => $ps_options['showtitles'],
		'showdesc' => $ps_options['showdesc'],
		'click' =>	$ps_options['click'],
		'thumbs' => '',
		'fluid'	=>	$ps_options['allowfluid'],
		'slideheight' => '',
		'id' => '',
		'exclude' => '',
		'include' => ''
	), $atts ) );
	
	//mapping for people using older versions of the plugin
	if ( $thumbs == "true" ) $pagerpos = "bottom";

	/* Preserve the nowrap option if people are still using it */
	if ( $nowrap == "false" || $loop == "true" ) { $loop = "true"; } else { $loop = "false"; }

	//has a custom post id been declared or should we use current page ID?
	if ( ! $id ) { $id = get_the_ID(); }

	//if the exclude_featured attribute is set, get the featured thumb ID and add it to the $exclude string
	if ( $exclude_featured == "true" ) {
		$featured_id = get_post_thumbnail_id( $id );
		if ( ! $include ) { // we don't need an exclude variable if $include is set
			if ( $exclude ) { //if $exclude is set, concatenate it
				$exclude = $exclude . "," . $featured_id;	
			 } else { //$exclude is simply equal to $featured_id 
			 	$exclude = $featured_id;
			 }
		}	
	} 

	//count the attachments
	if ( $include ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$attachments = get_children( array('post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'include' => $include) );
		
	} elseif ( $exclude ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'exclude' => $exclude) );
	} else {
		$attachments = get_children( array ( 'post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );
	}
	
	global $ps_count;
	$ps_count = count( $attachments );
		
	// Navigation
	if ( ! is_feed() && $ps_count > 1 ) { //no need for navigation if there's only one slide
	
	$ps_nav = '<div id="slideshow-nav'.$i.'" class="slideshow-nav">';

	$ps_nav .='<a class="pause" style="display:none" href="javascript:void(0);">Pause</a><a class="play" href="javascript:void(0);">Play</a><a class="restart" style="display:none" href="javascript: void(0);">Play</a><a class="slideshow-prev" href="javascript: void(0);">Prev</a><span class="sep">|</span><a class="slideshow-next" href="javascript: void(0);">Next</a><span class="slideshow-info' . $i . ' slideshow-info"></span>';
					
	$ps_nav .= '</div><!-- .slideshow-nav-->
	';
	} 

	//Pager
	
	//Do we show thumbnails?
	if ( ! is_feed() &&  $ps_count > 1 ) {
		$ps_pager = '<div class="pscarousel';

		$ps_pager .='">';
						   
		$ps_pager .= '<div id="pager' . $i . '" class="pager items clearfix">';

		if ( $include ) {
			$include = preg_replace( '/[^0-9,]+/', '', $include );
			$attachments = get_posts( array(
			'order'          => 'ASC',
			'orderby' 		 => 'menu_order ID',
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'post_mime_type' => 'image',
			'post_status'    => null,
			'numberposts'    => -1,
			'size'			 => 'thumbnail',
			'include'		 => $include) );
		} elseif ( $exclude ) {
			$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
			$attachments = get_posts( array(
			'order'          => 'ASC',
			'orderby' 		 => 'menu_order ID',
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'post_mime_type' => 'image',
			'post_status'    => null,
			'numberposts'    => -1,
			'size'			 => 'thumbnail',
			'exclude'		 => $exclude) );
		} else {
			$attachments = get_posts( array(
			'order'          => 'ASC',
			'orderby' 		 => 'menu_order ID',
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'post_mime_type' => 'image',
			'post_status'    => null,
			'numberposts'    => -1,
			'size'			 => 'thumbnail') );
		}
					
		if ($attachments) {
			$j = 1;
			
			foreach ( $attachments as $attachment ) {
			
			$ps_pager .= wp_get_attachment_image($attachment->ID, 'thumbnail', false, false);;		

			$j++;
			}
		}
		
		$ps_pager .= '</div>';
							
		$ps_pager .= '</div>';
	}	
		
	if ( ! is_feed() ) { 

	$slideshow = 
		'<script type="text/javascript">/* <![CDATA[ */ psTimeout['.$i.']='.$timeout.';psAutoplay['.$i.']='.$autoplay.';psTrans['.$i.']=\''.$trans.'\';psLoop['.$i.']='.$loop.';psSpeed['.$i.']='.$speed.';/* ]]> */</script>
		'; 
	
		//wrap the whole thing in a div for styling		
		$slideshow .= '<div id="slideshow-wrapper'.$i.'" class="slideshow-wrapper clearfix';
		
		if ( $fluid == "true" ) { 
			$slideshow .= " fluid"; 
		}

		if ( $ps_options['showloader'] == "true" ) { 
			$slideshow .= " showloader"; 
		}
	
		$slideshow .= '">';
		
		if ( $navpos == "top" ) { 
			$slideshow .= $ps_nav;
		}
	
		if ( $pagerpos == "top" ) { 
			$slideshow .= $ps_pager;
		}
		
	} //end ! is_feed()

	$slideshow .= '<div id="portfolio-slideshow'.$i.'" class="portfolio-slideshow"';
	
	/* An inline style if they need to set a height for the main slideshow container */	
	
	if ( $slideheight ) {
		$slideshow .= ' style="min-height:' . $slideheight . 'px !important;"';
	}
	
	$slideshow .='>
	';

	$slideID = 0;
	
	if ( $include ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$attachments = get_posts( array( 'order'          => 'ASC',
		'orderby' 		 => 'menu_order ID',
		'post_type'      => 'attachment',
		'post_parent'    => $id,
		'post_mime_type' => 'image',
		'post_status'    => null,
		'numberposts'    => -1,
		'size'			 => $size,
		'include'		 => $include) );
		
	} elseif ( $exclude ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_posts( array( 'order'          => 'ASC',
		'orderby' 		 => 'menu_order ID',
		'post_type'      => 'attachment',
		'post_parent'    => $id,
		'post_mime_type' => 'image',
		'post_status'    => null,
		'numberposts'    => -1,
		'size'			 => $size,
		'exclude'		 => $exclude) );
	} else {
		$attachments = get_posts( array( 'order'          => 'ASC',
		'orderby' 		 => 'menu_order ID',
		'post_type'      => 'attachment',
		'post_parent'    => $id,
		'post_mime_type' => 'image',
		'post_status'    => null,
		'numberposts'    => -1,
		'size'			 => $size) );
	}

	if ( $attachments ) { //if attachments are found, run the slideshow
	
		//begin the slideshow loop
		foreach ( $attachments as $attachment ) {
			
			$alttext = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );

			if ( ! $alttext ) {
				$alttext = $attachment->post_title;
			}
				
			$slideshow .= '<div class="';
			if ( $slideID != "0" ) { $slideshow .= "not-first "; }
			$slideshow .= 'slideshow-next slideshow-content'; 
			$slideshow .= '">
			';
				 
			switch ( $click ) {

				case "openurl" :
					$imagelink = get_post_meta( $attachment->ID, '_ps_image_link', true );
					if ( $imagelink ) { $imagelink = $imagelink . '" target="' . $ps_options['click_target']; } else { $imagelink = 'javascript: void(0);" class="slideshow-next';}
					break;

				default :
					$imagelink = 'javascript: void(0);" class="slideshow-next';
					break;	
			}		
			
if ( $loop == "false" && $ps_count - 1 != $slideID || $loop != "false" ) { $slideshow .= '<a href="'.$imagelink.'">';}
			
/*
 * This is the part of the loop that actually returns the images
 */
			
			$ps_placeholder = PORTFOLIO_SLIDESHOW_URL . '/img/tiny.png';
					 
			/* Otherwise it's just one of the WP defaults */
			
			$img =  wp_get_attachment_image_src( $attachment->ID, $size );
					
			$slideshow .= '<img class="psp-active" data-img="' . $img[0] . '"'; 
				
			if ( $slideID < 1 ) { 
				$slideshow .= ' src="' . $img[0] . '"';
			} else {
				$slideshow .= ' src="' . $ps_placeholder . '"';
			}
			//include the src attribute for the first slide only
				
			$slideshow .= ' height="' . $img[2] . '" width="' . $img[1] . '" alt="' . $alttext . '" /><noscript><img src="' . $img[0] . '" height="' . $img[2] . '" width="' . $img[1] . '" alt="' . $alttext . '" /></noscript>';
									
			
/*
 * That's it for the images
 */			
			
			if ( $loop == "false" && $ps_count - 1 != $slideID || $loop !="false" ) { 
						$slideshow .= "</a>";
			}		
		
		
			if ( $showtitles == "true" || $showcaps == "true" || $showdesc == "true" ) {
				$slideshow .= '<div class="slideshow-meta">';
			}

			//if titles option is selected
			if ( $showtitles == "true" ) {
				$title = $attachment->post_title;
				if ( $title ) { 
					$slideshow .= '<p class="slideshow-title">'.$title.'</p>'; 
				} 
			}
			
			//if captions option is selected
			if ( $showcaps == "true" ) {			
				$caption = $attachment->post_excerpt;
				if ( $caption ) { 
					$slideshow .= '<p class="slideshow-caption">'.$caption.'</p>'; 
				}
			}
			
			//if descriptions option is selected
			if ( $showdesc == "true" ) {			
				$description = $attachment->post_content;
				if ( $description ) { 
					$slideshow .= '<div class="slideshow-description">'. wpautop( $description ) .'</div>'; 
				}
			}
			if ( $showtitles == "true" || $showcaps == "true" || $showdesc == "true" ) {
				$slideshow .= '</div>';
			}

			$slideshow .= '</div>
			';
			
			$slideID++;
					
		}  // end slideshow loop
	} // end if ( $attachments)

	$slideshow .= "</div><!--#portfolio-slideshow-->";
	
	if ( $navpos == "bottom" ) { 
		$slideshow .= $ps_nav;
	}
	
	if ( $pagerpos == "bottom" ) { 
		$slideshow .= $ps_pager;
	}
	
	$slideshow .='</div><!--#slideshow-wrapper-->';

	$i++;

	return $slideshow;	//that's the slideshow
	
	
} //ends the portfolio_shortcode function ?>
