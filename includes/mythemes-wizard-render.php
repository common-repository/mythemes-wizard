<?php

	if( !class_exists( 'mythemes_wizard_render' ) )
	{
		class mythemes_wizard_render
		{
			public static function plugin( $field )
			{
					$slug = $field -> slug;

				?>
					<div <?php $field -> classes(); ?> <?php $field -> is_enable(); ?>>

						<label for="field-<?php echo esc_attr( $slug ); ?>">

							<?php
								$wizard = $field -> wizard;
								$plugin = mythemes_wizard_plugin::card( $slug );

								$field -> wizard -> plugins[] = array(
									'url'		=> $plugin[ 'url' ],
									'slug'		=> $plugin[ 'slug' ],
									'name'		=> $plugin[ 'name' ],
									'status' 	=> $plugin[ 'status' ]
								);

								if( isset( $plugin[ 'status' ] ) &&  $plugin[ 'status' ] == 'error' ){

									if( isset( $wizard -> settings[ $slug ] ) )
										unset( $wizard -> settings[ $slug ] );

									if( isset( $wizard -> defaults[ $slug ] ) )
										unset( $wizard -> defaults[ $slug ] );

									if( isset( $wizard -> callback[ 'defaults' ][ $slug ] ) )
										unset( $wizard -> callback[ 'defaults' ][ $slug ] );
								}
							?>

							<?php if ( isset( $plugin[ 'status' ] ) && $plugin[ 'status' ] == 'is-active' ) { ?>
								<input type="checkbox" value="1" <?php $field -> attrs(); ?> <?php checked( true, true ); ?> <?php disabled( true, true ) ?>/>
							<?php } else if( isset( $plugin[ 'status' ] ) && $plugin[ 'status' ] !== 'error' ) { ?>
								<input type="checkbox" value="1" <?php $field -> attrs(); ?> <?php checked( true, $field -> value() ); ?> />
							<?php } ?>

							<?php echo isset( $plugin[ 'card' ] ) ?  $plugin[ 'card' ] : null; ?>

						</label>
					</div>
				<?php
			}

			public static function checkbox( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>

						<label for="field-<?php echo esc_attr( $field -> slug ); ?>">

							<input type="checkbox" value="1" <?php $field -> attrs(); ?> <?php checked( true, $field -> value() ); ?> />

							<?php if( isset( $field -> args['label'] ) && !empty( $field -> args['label'] ) ) : ?>
								<span class="wizard-label"><?php echo esc_html( $field -> args['label'] ); ?></span>
							<?php endif; ?>

							<?php if( isset( $field -> args['description'] ) && !empty( $field -> args['description'] ) ) : ?>
								<span class="wizard-description"><?php echo esc_html( $field -> args['description'] ); ?></span>
							<?php endif; ?>

						</label>
					</div>
				<?php
			}

			public static function select( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>
						<label for="field-<?php echo esc_attr( $field -> slug ); ?>">

							<?php if( isset( $field -> args['label'] ) && !empty( $field -> args['label'] ) ) : ?>
								<span class="wizard-label"><?php echo esc_html( $field -> args['label'] ); ?></span>
							<?php endif; ?>

							<?php if( isset( $field -> args['description'] ) && !empty( $field -> args['description'] ) ) : ?>
								<span class="wizard-description"><?php echo esc_html( $field -> args['description'] ); ?></span>
							<?php endif; ?>

							<select <?php $field -> attrs(); ?>>

								<?php
									foreach( $field -> args[ 'options' ] as $option => $value ){
										?><option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $field -> value() ); ?>><?php echo esc_attr( $value ); ?></option><?php
									}
								?>

							</select>

						</label>
					</div>
				<?php
			}

			public static function url( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>
						<label for="field-<?php echo esc_attr( $field -> slug ); ?>">

							<?php if( isset( $field -> args['label'] ) && !empty( $field -> args['label'] ) ) : ?>
								<span class="wizard-label"><?php echo esc_html( $field -> args['label'] ); ?></span>
							<?php endif; ?>

							<?php if( isset( $field -> args['description'] ) && !empty( $field -> args['description'] ) ) : ?>
								<span class="wizard-description"><?php echo esc_html( $field -> args['description'] ); ?></span>
							<?php endif; ?>

							<input type="url" value="<?php echo esc_url( $field -> value() ) ?>" <?php $field -> attrs(); ?>/>

						</label>
					</div>
				<?php
			}

			public static function text( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>
						<label for="field-<?php echo esc_attr( $field -> slug ); ?>">

							<?php if( isset( $field -> args['label'] ) && !empty( $field -> args['label'] ) ) : ?>
								<span class="wizard-label"><?php echo esc_html( $field -> args['label'] ); ?></span>
							<?php endif; ?>

							<?php if( isset( $field -> args['description'] ) && !empty( $field -> args['description'] ) ) : ?>
								<span class="wizard-description"><?php echo esc_html( $field -> args['description'] ); ?></span>
							<?php endif; ?>

							<input type="text" value="<?php echo esc_attr( $field -> value() ) ?>" <?php $field -> attrs(); ?>/>

						</label>
					</div>
				<?php
			}

			public static function textarea( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>
						<label for="field-<?php echo esc_attr( $field -> slug ); ?>">

							<?php if( isset( $field -> args['label'] ) && !empty( $field -> args['label'] ) ) : ?>
								<span class="wizard-label"><?php echo esc_html( $field -> args['label'] ); ?></span>
							<?php endif; ?>

							<?php if( isset( $field -> args['description'] ) && !empty( $field -> args['description'] ) ) : ?>
								<span class="wizard-description"><?php echo esc_html( $field -> args['description'] ); ?></span>
							<?php endif; ?>

							<textarea <?php $field -> attrs(); ?>><?php echo esc_textarea( $field -> value() ); ?></textarea>

						</label>
					</div>
				<?php
			}

			public static function title( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>

						<?php if( isset( $field -> args[ 'title' ] ) && !empty( $field -> args[ 'title' ] ) ) : ?>
							<h2 class="wizard-title"><?php echo esc_html( $field -> args[ 'title' ] ); ?></h2>
						<?php endif; ?>

						<?php if( isset( $field -> args[ 'description' ] )  && !empty( $field -> args[ 'description' ] ) ) : ?>
							<span class="wizard-description"><?php echo esc_html( $field -> args[ 'description' ] ); ?></span>
						<?php endif; ?>

					</div>
				<?php
			}

			public static function hint( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>
						<p>
							<?php
								echo wp_kses( $field -> args[ 'content' ], array(
									'a' => array(
										'href'  	=> array(),
										'label' 	=> array(),
										'title' 	=> array(),
										'class' 	=> array(),
										'id'    	=> array(),
										'target'	=> array()
									),
									'br'        => array(),
									'em'        => array(),
									'strong'    => array(),
									'span'      => array(),
									'b'    		=> array(),
									'i'    		=> array(),
								));
							?>
						</p>
					</div>
				<?php
			}

			public static function html( $field )
			{
				?>
					<div <?php $field -> classes( ); ?> <?php $field -> is_enable(); ?>>
						<?php
							echo wp_kses( $field -> args[ 'content' ], array(
								'a' => array(
									'href'  	=> array(),
									'label' 	=> array(),
									'title' 	=> array(),
									'class' 	=> array(),
									'id'    	=> array(),
									'target'	=> array()
								),
								'img'		=> array(),
								'p'			=> array(),
								'br'        => array(),
								'em'        => array(),
								'strong'    => array(),
								'span'      => array(),
								'b'    		=> array(),
								'i'    		=> array(),
								'h1'    	=> array(),
								'h2'    	=> array(),
								'h3'    	=> array(),
								'h4'    	=> array(),
								'h5'    	=> array(),
								'h6'    	=> array(),
								'ol'    	=> array(),
								'ul'    	=> array(),
								'li'    	=> array(),
								'div'    	=> array(),
							));
						?>
					</div>
				<?php
			}
		}
	}
?>
