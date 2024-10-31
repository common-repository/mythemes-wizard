<?php

	/**
	 *  Plugin Tools Class.
	 *
	 *	Multiple Settings (adding dynamically)
	 *
	 *  @creaded    october 11, 2017
	 *  @updated    october 11, 2017
	 *
	 *  @package 	Gourmand
	 *  @since      Gourmand 0.0.2
	 *  @version    0.0.1
	 */

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if( isset( $_SERVER ) && $_SERVER[ 'SCRIPT_NAME' ] !== '/wp-admin/plugin-install.php' ){
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
	}

	if( !class_exists( 'mythemes_wizard_plugin' ) ){

		class mythemes_wizard_plugin
		{
			public static function info( $slug )
			{
				$rett = get_transient( 'gourmand_plugin_info_' . $slug );

				if ( false === $rett ) {
					$rett = (array)plugins_api(
						'plugin_information', array(
							'slug'   => $slug,
							'fields' => array(
								'versions'			=> false,
								'downloaded'        => false,
								'rating'            => false,
								'description'       => false,
								'donate_link'       => false,
								'tags'              => false,
								'sections'          => false,
								'added'             => false,
								'last_updated'      => false,
								'compatibility'     => false,
								'tested'            => false,
								'requires'          => false,
								'downloadlink'      => false,
								'banners'			=> false,
								'homepage'          => true,
								'short_description' => true,
								'icons'             => true,
							),
						)
					);
					set_transient( 'gourmand_plugin_info_' . $slug, $rett, 30 * MINUTE_IN_SECONDS );
				}

				return $rett;
			}

			public static function file( $slug )
			{
				$rett 	= '';
				$path 	= ABSPATH . 'wp-content/plugins/';

				$file_1	= $slug . '/index.php';
				$file_2	= $slug . '/loco.php';
				$file_3	= $slug . '/wp-' . $slug . '.php';
				$file_4	= $slug . '/' . $slug . '.php';

				$file 	= $file_1;

				if( is_file( $path . $file_4 ) )
					$file = $file_4;

				if( is_file( $path . $file_3 ) )
					$file = $file_3;

				if( is_file( $path . $file_3 ) )
					$file = $file_3;

				if( is_file( $path . $file_2 ) )
					$file = $file_2;

				if( is_file( $path . $file ) )
					$rett = $file;

				return $rett;
			}

			public static function abs_file( $slug )
			{
				$rett = '';
				$file = self::file( $slug );

				if( !empty( $file ) )
					$rett = ABSPATH . 'wp-content/plugins/' . $file;

				return $rett;
			}

			public static function is_installed( $slug )
			{
				$rett = false;

				$file = self::file( $slug );

				if( $file = self::file( $slug ) && !empty( $file ) )
					$rett = true;

				return $rett;
			}

			public static function is_active( $slug )
			{
				return is_plugin_active( self::file( $slug ) );
			}

			public static function install_url( $slug )
			{
				return wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'from'   => 'import',
							'plugin' => $slug,
						),
						network_admin_url( 'update.php' )
					),
					'install-plugin_' . $slug
				);
			}

			public static function activate_url( $slug )
			{
				$rett 	= '';
				$path 	= ABSPATH . 'wp-content/plugins/';
				$file 	= self::file( $slug );

				if( is_file( $path . $file ) ){
					$rett = add_query_arg(
						array(
							'action'        => 'activate',
							'plugin'        => rawurlencode( $file ),
							'plugin_status' => 'all',
							'paged'         => '1',
							'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $file ),
						), network_admin_url( 'plugins.php' )
					);
				}

				return $rett;
			}

			public static function card( $slug )
			{
				$plugin = self::info( $slug );

				$action	= __( 'Install', 'mythemes-wizard' );
				$status	= 'install';
				$data 	= null;
				$url 	= null;
				$name 	= null;

				if( self::is_installed( $slug ) ){
					$action 	= __( 'Activate', 'mythemes-wizard' );
					$status		= 'activate';
					$url 		= self::activate_url( $slug );
					$file 		= self::abs_file( $slug );

					if( !empty( $file ) )
						$data = get_plugin_data( $file );
				}

				if( self::is_active( $slug ) ){
					$action 	= __( 'Is Active', 'mythemes-wizard' );
					$status		= 'is-active';
				}

				/**
				 *
				 */

				$data_actions  = '';
				$data_actions .= 'data-install="' . __( 'Install', 'mythemes-wizard' ) . '" ';
				$data_actions .= 'data-installed="' . __( 'Installed', 'mythemes-wizard' ) . '" ';
				$data_actions .= 'data-activation="' . __( 'Activation', 'mythemes-wizard' ) . '" ';
				$data_actions .= 'data-active="' . __( 'Active', 'mythemes-wizard' ) . '" ';
				$data_actions .= 'data-success="' . __( 'Success', 'mythemes-wizard' ) . '" ';
				$data_actions .= 'data-install-failed="' . __( 'Install Failed', 'mythemes-wizard' ) . '" ';
				$data_actions .= 'data-activation-failed="' . __( 'Activation Failed', 'mythemes-wizard' ) . '"';

				if( isset( $plugin[ 'errors' ] ) ){

					$card  = '';
					$card .= '<div class="plugin-card ' . esc_attr( $slug ) . ' error">';
					$card .= '<strong>' . sprintf( __( 'Error - plugin slug: %1s', 'mythemes-wizard' ), esc_html( $slug ) ) . '</strong>';
					$card .= '<span class="plugin-description">' . __( 'This can be a connection error or this plugin can\'t be found on WordPress.org plugins directory.', 'mythemes-wizard' ) . '</span>';
					$card .= '</div>';

					if( !empty( $data ) ){

						$name = __( 'Undefined Plugin Name', 'mythemes-wizard' );

						if( isset( $data[ 'Name' ] ) && !empty( $data[ 'Name' ] ) )
							$name = $data[ 'Name' ];

						$description = __( 'Undefined or empty plugin description', 'mythemes-wizard' );

						if( isset( $data[ 'Description' ] ) && !empty( $data[ 'Description' ] ) )
							$description = $data[ 'Description' ];

						$version = __( 'undefined', 'mythemes-wizard' );

						if( isset( $data[ 'Version' ] ) && !empty( $data[ 'Version' ] ) )
							$version = $data[ 'Version' ];

						$author = __( 'Undefined Author', 'mythemes-wizard' );

						if( isset( $data[ 'Author' ] ) && !empty( $data[ 'Author' ] ) )
							$author = $data[ 'Author' ];

						$card  = '';
						$card .= '<span class="plugin-card ' . esc_attr( $slug ) . ' ' . esc_attr( $status ) . '">';
						$card .= '<img src="' . esc_url( mythemes_wizard_plugin_uri() . '/assets/img/plugin-icon.png' ) . '" title="' . esc_attr( $name ) . '" class="plugin-icon"/>';
						$card .= '<span class="plugin-action" ' . $data_actions . '>';
						$card .= '<span class="waiting-label">' . __( 'Waiting', 'mythemes-wizard' ) . '</span>';
						$card .= '<span class="action-label">' . esc_html( $action ) . '</span>';
						$card .= '<span class="effect marquee">...</span>';
						$card .= '</span>';
						$card .= '<strong>' . esc_html( $name ) . '</strong>';
						$card .= '<span class="plugin-details">' . sprintf( __( 'By %1s Version - %2s', 'mythemes-setup-wizard' ), $author, esc_attr( $version ) ) . '</span>';
						$card .= '<span class="plugin-description">' . $description . '</span>';
						$card .= '</span>';
					}

					else{
						$status = 'error';
					}
				}

				else {

					$name = $plugin['name'];

					$card  = '';
					$card .= '<span class="plugin-card ' . esc_attr( $slug ) . ' ' . esc_attr( $status ) . '">';
					$card .= '<img src="' . esc_url( $plugin['icons']['1x'] ) . '" title="' . esc_attr( $plugin['name'] ) . '" class="plugin-icon"/>';
					$card .= '<span class="plugin-action" ' . $data_actions . '>';
					$card .= '<span class="waiting-label">' . __( 'Waiting', 'mythemes-wizard' ) . '</span>';
					$card .= '<span class="action-label">' . esc_html( $action ) . '</span>';
					$card .= '<span class="effect marquee">...</span>';
					$card .= '</span>';
					$card .= '<strong>' . esc_html( $plugin['name'] ) . '</strong>';
					$card .= '<span class="plugin-details">' . sprintf( __( 'By %1s Version - %2s', 'mythemes-setup-wizard' ), $plugin['author'], esc_attr( $plugin['version'] ) ) . '</span>';
					$card .= '<span class="plugin-description">' . $plugin['short_description'] . '</span>';
					$card .= '</span>';
				}

				return array(
					'url'		=> $url,
					'slug'		=> $slug,
					'card'		=> $card,
					'name'		=> $name,
					'status' 	=> $status
				);
			}
		}
	}
