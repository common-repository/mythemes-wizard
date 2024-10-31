<?php

	if( !class_exists( 'mythemes_wizard_wp_option' ) ){

		class mythemes_wizard_wp_option
		{
			public function update( $option, $value )
			{
				update_option( $option, $value );
			}

			public function get( $option, $default = false )
			{
				return get_option( $option, $default );
			}

			public function delete( $option )
			{
				delete_option( $option );
			}
		}
	}
?>
