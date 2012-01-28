<?php
/*
Plugin Name: Portfolio Slideshow
Plugin URI: http://madebyraygun.com/wordpress/plugins/portfolio-slideshow
Description: A clean, simple, and mobile-friendly slideshow for WordPress.
Author: Dalton Rooney
Version: 1.3.5
Author URI: http://madebyraygun.com

Copyright 2011 Raygun Design LLC (email : contact@madebyraygun.com)
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/ 

define( 'PORTFOLIO_SLIDESHOW_PATH', plugin_dir_path( __FILE__ ) );

define( 'PORTFOLIO_SLIDESHOW_LOCATION', plugin_basename(__FILE__) );

define ( 'PORTFOLIO_SLIDESHOW_URL', plugins_url( '' ,  __FILE__ ) );

define( "PORTFOLIO_SLIDESHOW_VERSION", "1.3.5" );

//our main functions file
require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/functions.php'); 

// Get the admin page if necessary
if ( is_admin() ) { 
	require( PORTFOLIO_SLIDESHOW_PATH . 'admin/portfolio-slideshow-admin.php' );
}

?>
