<?php

	/**
	 *	myThemes Wizard
	 *
	 *  @creaded    october 11, 2017
	 *  @updated    october 11, 2017
	 *
	 *  @package 	myThemes Wizard
	 *  @since      myThemes Wizard 0.0.1
	 *  @version    0.0.1
	 */

	class mythemes_wizard
	{
		public $settings;
		public $default;
		public $plugins;
		public $setting;
		public $ajax;

		public $wp_theme_mod;
		public $wp_option;
		public $wp_meta;
		public $params;

		private $fields 	= array();
		private $steps		= array();

		public $callback 	= array(
			'settings'	=> array(),
			'defaults'	=> array(),
			'steps'		=> array(),
			'fields'	=> array()
		);

		public function __construct()
		{
			$this -> defaults		= array();
			$this -> settings 		= array();
			$this -> plugins		= array();
			$this -> ajax			= array();

			$this -> wp_theme_mod 	= new mythemes_wizard_wp_theme_mod();
			$this -> wp_option 		= new mythemes_wizard_wp_option();
			$this -> wp_meta 		= new mythemes_wizard_wp_meta();

			do_action( 'mythemes_wizard_register', $this );

			add_action( 'wp_ajax_mythemes_wizard_callback', array( $this, 'ajax_callback' ), 100 );
			add_action( 'wp_ajax_mythemes_wizard_setup_preview', array( $this, 'ajax_setup_preview' ), 100 );
			add_action( 'wp_ajax_mythemes_wizard_setup_settings', array( $this, 'ajax_setup_settings' ), 100 );

			if ( apply_filters( 'mythemes_wizard_enable', true ) && current_user_can( 'manage_options' ) ) {
				add_action( 'admin_menu', array( $this, 'menu' ), 0 );
				add_action( 'admin_init', array( $this, 'page' ), 0 );
			}
		}


		public function add_step( $slug, $args )
		{
			/**
			 *	Type can be:
			 *
			 *	zero		not display step counter and navigation buttons will be [ Not right Now ] [ Let's go! ]
			 *	default		display step counter and navigation buttons will be [ < Previous ] [ Next > ]
			 *	setup		display step counter and navigation buttons will be [ < Previous ] [ Setup ]
			 *				if click on button Setup will start setup with automaticaly
			 *				Intall & Activate selected Plugins and do_action( 'mythemes_wizard_setup' )
			 *
			 *	other		not display step counter and by default not display navigation buttons
			 *				also you can include additional element "navigation" in step args eg:
			 *
			 *				$wizard -> add_step( 'donate-page', array(
			 *					'type' 			=> 'other',
			 *					'title'			=> __( 'Setup is already finished !' ),
			 *					'navigation'	=> array(
			 *						array(
			 *							'label'	=> __( 'Dashboard' ),
			 *							'url'	=> esc_url( admin_url( '/' ) )
			 *						),
			 *						array(
			 *							'action'	=> 'prev-step',
			 *						),
			 *						array(
			 *							'action'	=> 'next-step',
			 *						),
			 *						array(
			 *							'type'		=> 'primary',
			 *							'label'		=> __( 'Setup' ),
			 *							'action'	=> 'setup-step'
			 *						),
			 *						array(
			 *							'type'	=> 'primary',
			 *							'label'	=> __( 'Customize Theme' ),
			 *							'url'	=> esc_url( admin_url( '/customize.php' ) )
			 *						),
			 *					)
			 *				));
			 */

			$args = wp_parse_args( $args, array(
				'type'	=> 'default'
			));

			return $this -> steps[ $slug ] = new mythemes_wizard_step( $slug, $args, $this );
		}

		public function add_field( $field )
		{
			return $this -> fields[ $field -> slug ] = $field;
		}


		public function get_setting( $slug )
		{
			$rett = null;

			if( isset( $this -> defaults[ $slug ] ) )
				$rett = $this -> defaults[ $slug ];

			if( isset( $this -> settings[ $slug ] ) )
				$rett = $this -> settings[ $slug ];

			return $rett;
		}

		public function set_setting( $slug, $value )
		{
			$this -> settings[ $slug ] = $value;
		}

		public function remove_setting( $slug )
		{
			if( isset( $this -> settings[ $lug ] ) )
				unset( $this -> settings[ $lug ] );
		}

		public function insert_post( $args )
		{
			$args = wp_parse_args( $args, array(
				'post_type'     => 'page',
				'post_name'     => null,
				'post_title'    => null,
				'post_status'   => 'publish',
				'post_author'   => 1,
			));

			if( empty( $args[ 'post_title' ] ) )
				return 0;

			if( empty( $args[ 'post_name' ] ) )
				$args[ 'post_name' ] = sanitize_title_with_dashes( $args[ 'post_title' ] );

			// Check if post exists
			$post = get_posts(array(
				'name'        => $args[ 'post_name' ],
				'post_type'   => $args[ 'post_type' ],
				'post_status' => $args[ 'post_status' ],
				'numberposts' => 1
			));

			if ( !empty( $post ) && isset( $post[ 0 ] ) && isset( $post[ 0 ] -> ID ) ) {
				$id = $post[ 0 ] -> ID;
			} else {

				// Insert the post into the database
				$id = wp_insert_post( $args );
			}

			return $id;
		}

		public function params( $args )
		{
			$this -> params = isset( $args[ 'params' ] ) ? $args[ 'params' ] : null;
		}

		public function callback( $type, $slug, $args )
		{
			if( isset( $args[ 'callback' ] ) && is_callable( $args[ 'callback' ] ) ){

				$this -> callback[ $type ][ $slug ] = array(
					'callback'	=> $args[ 'callback' ],
					'args' 		=> $this
				);

				$args[ 'callback' ] = call_user_func( $args[ 'callback' ], $this );
			}

			else{
				$args[ 'callback' ] = true;
			}

			return $args;
		}

		public function ajax_callback()
		{
			$steps 		= array();
			$fields 	= array();
			$dynamic	= array();

			$callback 	= $this -> callback;


			$value 		= isset( $_POST[ 'value' ] ) ? $_POST[ 'value' ] : null;
			$slug 		= isset( $_POST[ 'slug' ] ) ? $_POST[ 'slug' ] : null;
			$settings   = isset( $_POST[ 'settings' ] ) ? (array)$_POST[ 'settings' ] : array();

			if( is_numeric( $value ) )
				$value = $value + 0;

			$this -> settings[ $slug ] = $value;
			$this -> settings = wp_parse_args( $this -> settings, $settings );

			foreach( $callback[ 'defaults' ] as $slug => $cuf ){
				$this -> defaults[ $slug ] = call_user_func( $cuf[ 'callback' ], $cuf[ 'args' ] );

				if( !isset( $this -> settings[ $slug ] ) )
					$dynamic[ $slug ] = $this -> defaults[ $slug ];
			}

			foreach( $callback[ 'steps' ] as $slug => $cuf )
				$steps[ $slug ] = call_user_func( $cuf[ 'callback' ], $cuf[ 'args' ] );

			foreach( $callback[ 'fields' ] as $slug => $cuf )
				$fields[ $slug ] = call_user_func( $cuf[ 'callback' ], $cuf[ 'args' ] );

			echo json_encode( array(
					'settings'	=> $this -> settings,
					'defaults'	=> $this -> defaults,
					'plugins'	=> $this -> plugins,
					'dynamic'	=> $dynamic,
					'steps'		=> $steps,
					'fields'	=> $fields
			));

			exit();
		}

		public function ajax_setup_preview()
		{
			$settings = isset( $_POST[ 'settings' ] ) ? (array)$_POST[ 'settings' ] : array();
			$defaults = isset( $_POST[ 'defaults' ] ) ? (array)$_POST[ 'defaults' ] : array();

			$this -> settings = wp_parse_args( $settings, $this -> settings );
			$this -> defaults = wp_parse_args( $defaults, $this -> defaults );

			$plugins = '';

			if( !empty( $this -> plugins ) ){
				foreach( $this -> plugins as $index => $plugin ){

					if( $plugin[ 'status' ] == 'is-active' )
						continue;

					$slug = $plugin[ 'slug' ];
					if( $this -> get_setting( $slug ) ){
						$args = mythemes_wizard_plugin::card( $slug );

						$plugins .= '<div class="wizard-field-wrapper plugin waiting ' . esc_attr( $slug ) . '">';
						$plugins .= $args[ 'card' ];
						$plugins .= '</div>';
					}
				}
			}

			$content = '';

			if( !empty( $plugins ) ){
				$content .= '<div class="wizard-setup-plugins">';
				$content .= '<h2 class="wizard-title">' . __( 'Install &amp; Activate Plugins', 'gourmand' ) . '</h2>';
				$content .= $plugins;
				$content .= '</div>';
			}

			if( has_action( 'mythemes_wizard_setup' ) ){
				$content .= '<div class="wizard-setup-settings waiting">';
				$content .= '<h2 class="wizard-title">' . __( 'Setup Settings and Dependencies', 'mythemes-wizard' ) . '</h2>';
				$content .= '<div class="content">';
				$content .= '<p>' . __( 'Waiting ... ' , 'mythemes-wizard' ) . '</p>';
				$content .= '</div>';
				$content .= '</div>';
			}

			if( !empty( $content ) ){
				?>
					<div class="step-header">
						<h2 class="step-title"><?php echo __( 'Setup' , 'mythemes-wizard' ); ?></h2>
					</div>

					<div class="step-content">

						<?php echo $content; ?>

					</div>

					<div class="step-footer">
						<span class="spinner is-visible">
							<img src="<?php echo admin_url( '/images/spinner.gif' ); ?>">
						</span>

						<a href="<?php echo esc_url( admin_url() ); ?>" class="button primary ajax-disable"><?php echo __( 'Admin Dashboard', 'mythemes-wizard' ); ?></a>
					</div>
				<?php
			}

			exit();
		}

		public function ajax_setup_settings()
		{
			$settings = isset( $_POST[ 'settings' ] ) ? (array)$_POST[ 'settings' ] : array();
			$defaults = isset( $_POST[ 'defaults' ] ) ? (array)$_POST[ 'defaults' ] : array();

			$this -> settings = wp_parse_args( $settings, $this -> settings );
			$this -> defaults = wp_parse_args( $defaults, $this -> defaults );

			do_action( 'mythemes_wizard_setup', $this );

			exit();
		}

		public function page_slug()
		{
			return isset( $_GET[ 'page' ] ) ? esc_attr( $_GET[ 'page' ] ) : 'mythemes-wizard';
		}

		public function page()
		{
			if ( empty( $_GET[ 'page' ] ) || 'mythemes-wizard' !== $_GET[ 'page' ] )
				return;

			if( isset( $_POST[ 'mythemes-ajax-wizard' ] ) &&  absint( $_POST[ 'mythemes-ajax-wizard' ] ) == 1 )
				return;

			$font = 'Open+Sans:400,600&subset=latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese';

			wp_register_style( 'mythemes-wizard-open-sans', '//fonts.googleapis.com/css?family=' . esc_attr( $font ), null, '0.0.1' );

			wp_register_style( 'mythemes-wizard', mythemes_wizard_plugin_uri() . '/assets/css/wizard.css', null , '0.0.1' );
			wp_enqueue_style( 'mythemes-wizard' );

			wp_register_script( 'mythemes-wizard', mythemes_wizard_plugin_uri() . '/assets/js/wizard.js', array( 'jquery', 'updates' ), '0.0.1', true );
			wp_localize_script( 'mythemes-wizard', 'mythemes_wizard_args', array(
				'ajax_url'	=> esc_url( admin_url( '/admin-ajax.php' ) ),
				'settings'	=> $this -> settings,
				'defaults'	=> $this -> defaults,
				'plugins'	=> $this -> plugins,
				'ajax'		=> $this -> ajax,
				'dynamic'	=> array(),
				'steps'		=> array(),
				'fields'	=> array()
			));
			wp_enqueue_script( 'mythemes-wizard' );

			ob_start();

			$this -> header();
			$this -> content();
			$this -> footer();

			exit;
		}

		public function menu()
		{
			add_dashboard_page( '', '', 'manage_options', 'mythemes-wizard', '' );
		}


		public function header()
		{
			$theme = wp_get_theme();
			?>
				<!DOCTYPE html>
				<html <?php language_attributes(); ?>>
					<head>
						<meta name="viewport" content="width=device-width" />
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title><?php printf( __( '%1s &rsaquo; Setup Wizard', 'mythemes-wizard' ), $theme -> get( 'Name' ) ); ?></title>
						<?php wp_print_scripts( 'mythemes-wizard' ); ?>
						<?php do_action( 'admin_print_styles' ); ?>
						<?php do_action( 'admin_head' ); ?>
						<script type="text/javascript">
							if( typeof pagenow === 'undefined' ){
								var pagenow = '<?php echo $this -> page_slug(); ?>';
							}
						</script>
					</head>
					<body class="<?php echo $this -> page_slug(); ?> wp-core-ui">
			<?php
		}

		public function content()
		{
			?>
				<div class="wizard-wrapper">

					<div class="wizard-ajax-mask"></div>

					<div class="wizard-steps">
						<?php
							$index = 0;
							$count = count( $this -> steps );

							foreach( $this -> steps as $slug => $step ){
								$step -> render( $index++,  $count );
							}
						?>
					</div>

					<div class="wizard-footer">
						<?php printf( __( '&copy; copyright 2017 %1s free WordPress Plugin.', 'mythemes-wizard' ), '<a href="http://mythem.es/item/wizard/" title="' . __( 'myThem.es Wizard', 'mythemes-wizard' ) . '" target="_blank">' . __( 'myThem.es Wizard' ) . '</a>' ); ?>
					</div>

				</div>
			<?php
		}

		public function footer()
		{
			?>
					</body>
				</html>
			<?php
		}
	}
?>
