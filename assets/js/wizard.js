var mythemes_wizard = function( $, args ){

	var

	_settings	= function(){

		if( jQuery( 'div.wizard-setup-settings' ).length ){
			jQuery( 'div.wizard-setup-settings' ).removeClass( 'waiting' );

			var ajax_args   = {
				'action'    			: 'mythemes_wizard_setup_settings',
				'mythemes-ajax-wizard'	: 1,
				'settings'				: args.settings,
				'defaults'				: args.defaults
			};

			jQuery.post( args.ajax_url, ajax_args, function( result ){
				jQuery( 'div.wizard-setup-settings div.content' ).html( result );

				jQuery( '.step-footer span.spinner' ).removeClass( 'is-visible' );
				jQuery( 'a.button.ajax-disable' ).removeClass( 'ajax-disable' );
				jQuery( '.wizard-field.ajax-disable' ).removeClass( 'ajax-disable' );
			});
		}

		else{
			jQuery( '.step-footer span.spinner' ).removeClass( 'is-visible' );
			jQuery( 'a.button.ajax-disable' ).removeClass( 'ajax-disable' );
			jQuery( '.wizard-field.ajax-disable' ).removeClass( 'ajax-disable' );
		}
	},

	_plugins 	= {
		install 	: function( plugin ){

			var wrapper = jQuery( 'div.wizard-field-wrapper.plugin.' + plugin.slug );

			wp.updates.installPlugin({
				slug        			: plugin.slug,
				'mythemes-ajax-wizard'	: 1,
				beforeSend  			: function(){

					jQuery( wrapper ).addClass( 'progress' );
					jQuery( wrapper ).find( 'span.plugin-action span.waiting-label' ).hide();
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).show();
					var action = jQuery( wrapper ).find( 'span.plugin-action' ).attr( 'data-install' );
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).text( action );

				},
				success : function( response ){

					jQuery( wrapper ).removeClass( 'progress' );
					var action = jQuery( wrapper ).find( 'span.plugin-action' ).attr( 'data-installed' );
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).text( action );

					// ACTIVATE
					plugin.url = response.activateUrl;

					_plugins.activate( plugin );
				},
				error : function( response ){
					console.log( 'INSTALL PLUGIN ERROR' );
					console.log( response );

					jQuery( wrapper ).removeClass( 'progress' );
					var action = jQuery( wrapper ).find( 'span.plugin-action' ).attr( 'data-install-failed' );
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).text( action );

					_plugins.callback();
				}
			});
		},
		activate 	: function( plugin ){
			var wrapper = jQuery( 'div.wizard-field-wrapper.plugin.' + plugin.slug );

			jQuery.ajax({
				async       			: true,
				type        			: 'GET',
				'mythemes-ajax-wizard'	: 1,
				url         			: plugin.url,
				beforeSend  			: function(){

					jQuery( wrapper ).addClass( 'progress' );
					jQuery( wrapper ).find( 'span.plugin-action span.waiting-label' ).hide();
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).show();
					var action = jQuery( wrapper ).find( 'span.plugin-action' ).attr( 'data-activation' );
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).text( action );
				},
				success : function( response ){

					jQuery( wrapper ).removeClass( 'progress' ).removeClass( 'waiting' );
					var action = jQuery( wrapper ).find( 'span.plugin-action' ).attr( 'data-success' );
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).text( action );

					// Next Plugin or Setup Settings
					_plugins.callback();
				},
				error : function( response ){

					console.log( 'ACTIVATE PLUGIN ERROR' );
					console.log( response );

					jQuery( wrapper ).removeClass( 'progress' ).removeClass( 'waiting' );
					var action = jQuery( wrapper ).find( 'span.plugin-action' ).attr( 'data-activation-failed' );
					jQuery( wrapper ).find( 'span.plugin-action span.action-label' ).text( action );

					_plugins.callback();
				}
			});
		},
		callback 	: function(){
			var

			plugins  = args.plugins,
			settings = args.settings;

			if( Object.keys( plugins ).length ){
				var

				action		= false,
				key			= Object.keys( plugins )[ 0 ],
				plugin		= plugins[ key ],
				slug 		= plugin.slug;


				if( !settings.hasOwnProperty( slug ) && args.defaults.hasOwnProperty( slug ) )
					settings[ slug ] = args.defaults[ slug ];

				if( settings.hasOwnProperty( slug ) ){
					if( settings[ slug ] && plugin.status == 'install' ){
						delete args.plugins[ key ];

						action = true;

						_plugins.install( plugin );
					}

					if( !action && settings[ slug ] && plugin.status == 'activate' ){
						delete args.plugins[ key ];

						action = true;

						_plugins.activate( plugin );
					}
				}

				if( !action ){
					delete args.plugins[ key ];

					_plugins.callback();
				}
			}

			else{
				_settings();
			}
		},
		setup 		: function(){
			_plugins.callback();
		}
	},

	_field 		= {
		get : function( selector ){
			var value = null;

			if( jQuery( selector ).is( 'select' ) ){
				jQuery( selector ).find( 'option' ).each(function(){
					if( jQuery( this ).is(':selected') ){
						value = jQuery( this ).val().trim();
					}
				});
			}

			else if( jQuery( selector ).is( 'input[type="checkbox"]' ) ){
				if( jQuery( selector ).is(':checked') ){
					value = 1;
				}

				else{
					value = 0;
				}
			}

			else if( jQuery( selector ).is( 'input' ) || jQuery( selector ).is( 'textarea' ) ){
				value = jQuery( selector ).val().trim();
			}

			return value;
		},
		set : function( slug, value ){
			var field = jQuery( '.wizard-field.' + slug );

			if( jQuery( '.wizard-field.' + slug ).length ){
				if( jQuery( field ).is( 'select' ) ){
					jQuery( field ).find( 'option' ).removeAttr( 'selected' ).filter('[value=' + value + ']').prop( 'selected', true );
				}

				else if( jQuery( field ).is( 'input[type="checkbox"]' ) ){

					if( value == 1 && !jQuery( field ).is(':checked') ){
						jQuery( field ).prop( 'checked', true );
					}

					else if( value == 0 && jQuery( field ).is(':checked') ){
						jQuery( field ).prop( 'checked', false );
					}
				}

				else if( jQuery( field ).is( 'input' ) || jQuery( field ).is( 'textarea' ) ){
					jQuery( field ).val( value );
				}
			}
		}
	},

	_callback 	= {
		steps 	: function( _args ){
			if( _args.hasOwnProperty( 'steps' ) ){
				for ( var slug in _args.steps ) {
					if( _args.steps[ slug ] ){
						if( jQuery( 'div.wizard-step.' + slug ).hasClass( 'disable' ) ){
							jQuery( 'div.wizard-step.' + slug ).removeClass( 'disable' );
						}
					}

					else{
						if( !jQuery( 'div.wizard-step.' + slug ).hasClass( 'disable' ) ){
							jQuery( 'div.wizard-step.' + slug ).addClass( 'disable' );
						}
					}
				}
			}
		},
		fields 	: function( _args ){
			if( _args.hasOwnProperty( 'fields' ) ){
				for ( var slug in _args.fields ) {

					if( _args.fields[ slug ] ){
						jQuery( 'div.wizard-field-wrapper.' + slug ).slideDown( 'fast' );
					}

					else{
						jQuery( 'div.wizard-field-wrapper.' + slug ).slideUp( 'fast' );
					}
				}
			}
		}
	};

	jQuery('a.button').click(function( e ){
		if( jQuery( this ).hasClass( 'ajax-disable' ) ){
			e.preventDefault();
		}
	});


	/**
	 *	Add Next Step button event Action
	 */

	jQuery( 'a.button[data-action="next-step"]' ).click(function( e ){

		e.preventDefault();

		if( !jQuery( this ).hasClass( 'ajax-disable' ) ){

			/**
			 *	Hide Current Step
			 */

			if( !jQuery( this ).parents( 'div.wizard-step' ).hasClass( 'hidden' ) ){
				jQuery( this ).parents( 'div.wizard-step' ).addClass( 'hidden' );
			}

			/**
			 *	Exclude disabled steps ( callback return false )
			 */

			var next = jQuery( this ).parents( 'div.wizard-step' ).nextAll( 'div.wizard-step:not(.disable):first' );


			/**
			 *	Check if exists and show Next Step
			 */

			if( jQuery( next ).length ){
				jQuery( next ).removeClass( 'hidden' );
			}
		}
	});


	/**
	 *	Add Previous Step button event Action
	 */

	jQuery( 'a.button[data-action="prev-step"]' ).click(function( e ){

		e.preventDefault();

		if( !jQuery( this ).hasClass( 'ajax-disable' ) ){

			/**
			 *	Hide Current Step
			 */

			if( !jQuery( this ).parents( 'div.wizard-step' ).hasClass( 'hidden' ) ){
				jQuery( this ).parents( 'div.wizard-step' ).addClass( 'hidden' );
			}

			/**
			 *	Exclude disabled steps ( callback return false )
			 */

			var prev = jQuery( this ).parents( 'div.wizard-step' ).prevAll( 'div.wizard-step:not(.disable):first' );

			/**
			 *	Check if exists and show Previous Step
			 */

			if( jQuery( prev ).length ){
				jQuery( prev ).removeClass( 'hidden' );
			}
		}
	});


	/**
	 *	Add Change field event Action
	 */

	jQuery('.wizard-field').each(function(){

		jQuery( this ).change(function(){

			if( !jQuery( this ).hasClass( 'ajax-disable' ) ){

				var value 	= _field.get( this );
				var slug	= jQuery( this ).attr( 'name' ).toString();

				console.log( args );

				if( args.ajax[ slug ] ){

					var ajax_args   = {
		                'action'    			: 'mythemes_wizard_callback',
						'mythemes-ajax-wizard'	: 1,
						'slug'					: slug,
		                'value'					: value,
						'settings'				: JSON.parse( JSON.stringify( args.settings ) )
		            };

					jQuery( 'a.button:not(.ajax-disable)' ).addClass( 'ajax-disable' );
					jQuery( '.wizard-field:not(.ajax-disable)' ).addClass( 'ajax-disable' );
					jQuery( '.wizard-ajax-mask' ).show();
					jQuery( '.step-footer span.spinner' ).addClass( 'is-visible' );

					//return;

					jQuery.post( args.ajax_url, ajax_args, function( result ){

						jQuery( 'a.button.ajax-disable' ).removeClass( 'ajax-disable' );
						jQuery( '.wizard-field.ajax-disable' ).removeClass( 'ajax-disable' );
						jQuery( '.wizard-ajax-mask' ).hide();
						jQuery( '.step-footer span.spinner' ).removeClass( 'is-visible' );

						var _args = JSON.parse( result );

						console.log( _args );

						/**
						 *	Update Settings and Defaults
						 */

						args.settings = _args.settings;
						args.defaults = _args.defaults;


						/**
						 *	Enable / disable Steps by callback's results
						 */

						_callback.steps( _args );

						/**
						 *	Enable / disable Fields by callback's results
						 */

						_callback.fields( _args );

						/**
						 *	Reset default Fields Values by defaults callback's results
						 */

						if( _args.hasOwnProperty( 'dynamic' ) ){
							for ( var slug in _args.dynamic ) {
								_field.set( slug, _args.dynamic[ slug ] );
							}
						}
					});
				}

				else{
					args.settings[ slug ] = value;
					args.settings = jQuery.extend( {}, args.settings, {} );
				}
			}
		});
	});


	/**
	 *	Add Setup button event Action
	 */

	jQuery('a.button[data-action="setup-step"]').click(function( e ){

		e.preventDefault();

		if( !jQuery( this ).hasClass( 'ajax-disable' ) ){

			var

			width 	= parseInt(jQuery( this ).outerWidth());
			current = jQuery( this ).parents( 'div.wizard-step' ),
			prevAll = jQuery( current ).prevAll( 'div.wizard-steps:not(#setup-wizard-step)' ),
			setup 	= jQuery( this ).parents( 'div.wizard-steps' ).find( 'div#setup-wizard-step' );

			jQuery( 'a.button:not(.ajax-disable)' ).addClass( 'ajax-disable' );
			jQuery( '.wizard-field:not(.ajax-disable)' ).addClass( 'ajax-disable' );
			jQuery( '.wizard-ajax-mask' ).show();
			jQuery( '.step-footer span.spinner' ).addClass( 'is-visible' );

			if( jQuery( setup ).length ){

				var ajax_args = {
					'action'    			: 'mythemes_wizard_setup_preview',
					'mythemes-ajax-wizard'	: 1,
					'settings'				: args.settings,
					'defaults'				: args.defaults
				};

				jQuery.post( args.ajax_url, ajax_args, function( result ){

					jQuery( '.wizard-ajax-mask' ).hide();
					jQuery( '.step-footer span.spinner' ).removeClass( 'is-visible' );

					// Disable all previous steps
					if( !jQuery( prevAll ).hasClass( 'disable' ) )
						jQuery( prevAll ).addClass( 'disable' );

					jQuery( current ).addClass( 'disable' ).addClass( 'hidden' );

					// Dispaly setup preview content
					jQuery( setup ).html( result );
					jQuery( setup ).removeClass( 'disable' ).removeClass( 'hidden' );

					// Begin Setup
					_plugins.setup();
				});
			}
		}
	});
};

jQuery(function(){
	new mythemes_wizard( jQuery, mythemes_wizard_args );
});
