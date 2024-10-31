<?php

	if( !class_exists( 'mythemes_wizard_field' ) ){

		class mythemes_wizard_field
		{
			public $type;
			public $slug;
			public $args;
			public $params;
			public $wizard;

			public function __construct( $slug, $args, $wizard )
			{
				$this -> type 	= $args[ 'type' ];
				$this -> slug 	= $slug;
				$this -> wizard = $wizard;
				$this -> wizard -> params( $args );
				$args = $this -> defaults( $args );
				$this -> args = $this -> wizard -> callback( 'fields', $slug, $args );

				if( $this -> type == 'plugin' )
					$this -> wizard -> plugins[ $slug  ] = mythemes_wizard_plugin::card( $slug );

				$this -> wizard -> ajax[ $slug ] = false;

				if( isset( $args[ 'ajax' ] ) && $args[ 'ajax' ] )
					$this -> wizard -> ajax[ $slug ] = true;
			}

			public function defaults( $args )
			{
				$this -> wizard -> defaults[ $this -> slug ] = null;

				if( isset( $args[ 'default' ] ) ) {

					if( is_callable( $args[ 'default' ] ) ){

						$this -> wizard -> callback[ 'defaults' ][ $this -> slug ] = array(
							'callback' 	=> $args[ 'default' ],
							'args' 		=> $this -> wizard
						);

						$args[ 'default' ] = call_user_func( $args[ 'default' ], $this -> wizard );
					}

					$this -> wizard -> defaults[ $this -> slug ] = $args[ 'default' ];
				}

				return $args;
			}

			public function is_enable()
			{
				if( !$this -> args[ 'callback' ] )
					echo 'style="display: none;"';
			}

			public function classes( $classes = null )
			{
				if( isset( $this -> args[ 'classes' ] ) )
					$classes .= esc_attr( ' ' . $this -> args[ 'classes' ] );

				echo 'class="wizard-field-wrapper ' . esc_attr( trim( $this -> type . ' '. $this -> slug . ' ' . trim( $classes ) ) ) . '"';
			}

			public function attrs()
			{
				echo

				'id="field-' . esc_attr( $this -> slug ) . '" '.
				'class="wizard-field  ' . esc_attr( $this -> slug ) . '" ' .
				'name="' . esc_attr( $this -> slug ) . '"';
			}

			public function value()
			{
				$wizard = $this -> wizard;
				$slug 	= $this -> slug;
				$rett 	= $wizard -> defaults[ $slug ];

				if( isset( $wizard -> settings[ $slug ] ) )
					$rett = $wizard -> settings[ $slug ];

				return $rett;
			}

			public function render()
			{
				if( isset( $this -> type ) && method_exists( 'mythemes_wizard_render', $this -> type ) )
					call_user_func_array( array( 'mythemes_wizard_render', $this -> type ), array( $this ) );
			}
		}
	}
?>
