<?php

	if( !class_exists( 'mythemes_wizard_wp_theme_mod' ) ){

		class mythemes_wizard_wp_theme_mod
		{
			public function set( $name, $value )
			{
				set_theme_mod( $name, $value );
			}

			public function get( $name, $default = false )
			{
				return get_theme_mod( $name, $default );
			}

			public function remove( $name )
			{
				remove_theme_mod( $name );
			}
		}
	}
?>
