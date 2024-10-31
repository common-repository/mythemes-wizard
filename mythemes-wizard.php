<?php
/*
	Plugin Name: myThemes Wizard
	Plugin URI: http://mythem.es/item/mythemes-wizard/
	Description: With myThemes Wizard you can build a custom setup for each WordPress Theme and Plugin. Inspired from WooCommerce Plugin Setup. This is a GPL 2 WordPress plugin.
	Version: 0.0.4
	Author: myThem.es
	Author URI: http://mythem.es/
	License: GNU General Public License v2.0
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	Text Domain:  mythemes-wizard
	Domain Path:  /languages/
*/

/*
    Copyright 2017 myThem.es ( email: mythemes-wizard at mythem.es )

    myThemes Wizard, Copyright 2017 myThem.es
    myThemes wizard is distributed under the terms of the GNU GPL


                     ________________
                    |_____    _______|
     ___ ___ ___   __ __  |  |  __       ____   ___ ___ ___       ____   ____
    |           | |_ |  | |  | |  |___  |  __| |           |     |  __| |  __|
    |   |   |   |  | |  | |  | |  __  | |  __| |   |   |   |  _  |  __| |__  |
    |___|___|___|   |  |  |__| |_ ||_ | |____| |___|___|___| |_| |____| |____|
                    |_|


*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function mythemes_wizard_plugin()
{
	/**
	 *  Plugin Directory
	 */

	function mythemes_wizard_plugin_dir()
	{
		return rtrim( dirname( __FILE__ ), '/' );
	}

	/**
	 *  Plugin Uri
	 */

	function mythemes_wizard_plugin_uri()
	{
		return rtrim( plugin_dir_url( __FILE__ ), '/' );
	}

	/**
	 *	Plugin Text Domain
	 */

	function mythemes_wizard_load_textdomain(){
		load_plugin_textdomain( 'mythemes-wizard', false, mythemes_wizard_plugin_dir() . '/languages' );
	}

	add_action( 'plugins_loaded', 'mythemes_wizard_load_textdomain' );


	/**
	 *	Includes Classes and Functions
	 *
	 *  @creaded    october 11, 2017
	 *  @updated    october 11, 2017
	 *
	 *  @package 	myThemes Wizard
	 *  @since      myThemes Wizard 0.0.1
	 *  @version    0.0.1
	 */

	function mythemes_wizard_autoload( $class_name )
	{
		$file_name	= '';
		$class_path	= '';

		if( preg_match( "/^mythemes_wizard/", $class_name ) ){
			$file_name	= str_replace( '_', '-', strtolower( $class_name ) );
			$class_path = mythemes_wizard_plugin_dir() . '/includes/' . $file_name . '.php';
		}

		if( !empty( $class_path ) && is_file( $class_path ) ){
			include_once $class_path;
		}
	}

	spl_autoload_register( 'mythemes_wizard_autoload' );


	/**
	 *	Init myThemes Wizard
	 */

	if( !is_network_admin() )
		new mythemes_wizard();
}

add_action( 'after_setup_theme', 'mythemes_wizard_plugin', 30 );

?>
