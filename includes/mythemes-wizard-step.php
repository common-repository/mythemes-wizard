<?php

	if( !class_exists( 'mythemes_wizard_step' ) ){

		class mythemes_wizard_step
		{
			private $wizard;
			private $fields;
			private $slug;
			private $args;
			private $nav;

			public function __construct( $slug, $args, $wizard )
			{
				$this -> type	= $args[ 'type' ];
				$this -> slug 	= $slug;
				$this -> wizard = $wizard;

				$this -> wizard -> params( $args );
				$this -> args = $this -> wizard -> callback( 'steps', $slug, $args );
			}

			public function add_field( $slug, $args )
			{
				$this -> fields[ $slug ] = $this -> wizard -> add_field( new mythemes_wizard_field( $slug, $args, $this -> wizard ) );
			}

			public function classes( $classes = '' )
			{
				if( !$this -> args[ 'callback' ] )
					$classes .= ' disable';

				if( isset( $this -> args[ 'classes' ] ) )
					$classes .= ' ' . $this -> args[ 'classes' ];

				echo 'class="wizard-step ' . esc_attr( trim( $this -> slug . ' ' . $this -> type . ' ' . $classes ) ) . '"';
			}

			public function button( $_args = array() )
			{
				$args = wp_parse_args( $_args, array(
					'type'		=> null,
					'label'		=> sprintf( __( '%1s Previous', 'mythemes-wizard' ), '&lsaquo;' ),
					'action'	=> 'prev-step',
					'url'		=> esc_url( admin_url( '/index.php?page=' . $this -> wizard -> page_slug() ) ),
				));

				if( !isset( $_args[ 'action' ] ) && isset( $_args[ 'url' ] ) )
					$args[ 'action' ] = null;

				if( $args[ 'action' ] == 'next-step' ){

					if( !isset( $_args[ 'type' ] ) )
						$args[ 'type' ] = 'primary';

					if( !isset( $_args[ 'label' ] ) )
						$args[ 'label' ] = sprintf( __( 'Next %1s', 'mythemes-wizard' ), '&rsaquo;' );
				}

				$effect = '';

				if( $args[ 'action' ] == 'setup-step' )
					$effect = '<span class="effect loading">...</span>';


				echo
					'<a href="' . esc_url( $args[ 'url' ] ) . '" class="button ' . esc_attr( $args[ 'type' ] ) . '" data-action="' . esc_attr( $args[ 'action' ] ) . '">' .
					'<span class="button-text">' . esc_html( $args[ 'label' ] ) . '</span>' . $effect .
					'</a>';
			}

			public function navigation()
			{
				switch( $this -> type ){
					case 'zero' : {
						$this -> button( array(
							'label'		=> __( 'Not right Now', 'mythemes-wizard' ),
							'url' 		=> esc_url( admin_url( '/' ) )
						));
						$this -> button( array(
							'label'		=> __( 'Let\'s go!', 'mythemes-wizard' ),
							'action' 	=> 'next-step'
						));
						break;
					};

					case 'setup' : {
						$this -> button();
						$this -> button( array(
							'type'		=> 'primary',
							'label'		=> __( 'Start Setup', 'mythemes-wizard' ),
							'action' 	=> 'setup-step'
						));
						break;
					};

					case 'other' : {
						if( isset( $this -> args[ 'navigation' ] ) && is_array( $this -> args[ 'navigation' ] ) ){
							foreach( $this -> args[ 'navigation' ] as $index => $args )
								$this -> button( $args );
						}
						break;
					};

					default : {
						$this -> button();
						$this -> button( array(
							'action' 	=> 'next-step'
						));
						break;
					};

				}
			}

			public function render( $index, $count )
			{
					$classes = $index > 0 ? 'hidden' : null;
				?>

					<?php

						/**
						 *	Setup Preview Step
						 */
					?>

					<?php if( $index == 0 ) : ?>
						<div id="setup-wizard-step" class="setup wizard-step disable">disable</div>
					<?php endif; ?>

					<div <?php $this -> classes( $classes ); ?>>


						<div class="step-header">

							<span class="counter">
								<span class="step"><?php echo absint( $index ); ?></span> / <span class="steps"><?php echo absint( $count - 1 ); ?></span>
							</span>

							<?php if( isset( $this -> args[ 'title' ] ) ) : ?>
								<h2 class="wizard-title"><?php echo esc_html( $this -> args[ 'title' ] ) ?></h2>
							<?php endif; ?>

							<?php if( isset( $this -> args[ 'description' ] ) ) : ?>
								<span class="wizard-description"><?php echo esc_html( $this -> args[ 'description' ] ) ?></span>
							<?php endif; ?>

						</div>


						<div class="step-content">

							<?php
								foreach( $this -> fields as $slug => $field ){
									$field -> render();
								}
							?>

						</div>


						<div class="step-footer">

							<span class="spinner">
								<img src="<?php echo admin_url( '/images/spinner.gif' ); ?>">
							</span>

							<?php $this -> navigation(); ?>

						</div>

					</div>

				<?php
			}
		}
	}
?>
