<?php

	if( !class_exists( 'mythemes_wizard_wp_meta' ) ){

		class mythemes_wizard_wp_meta
		{
		    public function val( $post_id, $metakey )
		    {
		        return get_post_meta( $post_id , $metakey , true );
		    }

		    public function get( $post_id, $metakey, $default = null )
		    {
				$meta = get_post_meta( $post_id , $metakey );
				$rett = $default;

				if( isset( $meta[ 0 ] ) )
					$rett = $meta[ 0 ];

		        return $rett;
		    }

		    public function set( $post_id, $metakey, $value )
		    {
	            add_post_meta( $post_id , $metakey , $value, true );
	            update_post_meta( $post_id , $metakey , $value );
		    }


		    public function delete( $post_id, $metakey )
		    {
		        delete_post_meta( $post_id, $metakey );
		    }
		}
	}
?>
