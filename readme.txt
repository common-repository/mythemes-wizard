=== myThemes Wizard ===
Contributors: mythemes
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4LFP5SLC6PV98
Tags: wizard, setup, setup themes, setup plugins, developers
Requires PHP: 5.3
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 0.0.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With myThemes Wizard you can build a custom setup for each WordPress Theme and Plugin. Inspired from WooCommerce Plugin Setup. This is a GPL 2 WordPress plugin.

== Description ==

myThemes Wizard is a free plugin that allows easily build your own custom Setup for any WordPress Themes and Plugins. This plugin is dedicated to WordPress developers. Themes and Plugins require additional support to can be integrate with myThemes Wizard Plugin.


= Integration =

To integrate features of this plugin with you theme, in functions.php, is need define 2 actions: register action and setup action.

Register Action eg:

	function my_wizard_register( $wizard )
	{
		...
	}

	add_action( 'mythemes_wizard_register', 'my_wizard_register' );

Setup Action eg:

	function my_wizard_setup( $wizard )
	{
		...
	}

	add_action( 'mythemes_wizard_setup', 'my_wizard_setup' );

In register action function you will include wizard Steps and Fields. You can define recommended plugins ( one of fields type ) and if user will check some of the plugin then on begin of the setup this plugin will be Installed and Activated automatically.

In setup action function you will use settings values collected from steps and field ( register function ) to setup different theme options create pages and posts.

Register Action Steps and Fields eg: [DropBox register-action.php](https://www.dropbox.com/s/atotibcwrsbmqu8/register-action.php?dl=0)

So you can see in the example above a register function with 4 Steps:

- Step 0: Welcome Step
- Step 1: Website Setup
- Step 2: Create Pages
- Step 3: Install and Activate Recommended Plugins

Setup Action eg: [DropBox setup-action.php](https://www.dropbox.com/s/g32kyyanzqnru88/setup-action.php?dl=0)

After in your WordPress Theme or Plugin was be added support for register and setup actions you can install the plugin. The plugin not has settings or additional config actions. To run the setup is need access the URL `admin_url( '/index.php?page=mythemes-wizard' )`. How you will do that is your choice.

For example I include these feature in [Gourmand WordPress Theme](http://mythem.es/item/gourmand/) `gourmand/settings/setup/wizard.php`. On Admin Dashboard I create an About Page with Recommended Actions Section where is a button. This button use some additional JavaScript to install and activate the plugin myThemes Wizard and after this automatically the user is redirected to setup page. Remember this is developer tools.

= Steps =

Step type can be:

- zero: not display step counter and navigation buttons will be "Not right Now" and "Let's go!"
- default: display step counter and navigation buttons will be "< Previous" and "Next >"
- setup: display step counter and navigation buttons will be "< Previous" and "Setup" if click on button Setup will start automatically setup with Install & Activate selected Plugins and `do_action( 'mythemes_wizard_setup' )`
- other: not display step counter and by default not display navigation buttons also you can include additional element "navigation" in step args eg:

Example of Custom Navigations:

	$wizard -> add_step( 'donate-page', array(
		'type' 			=> 'other',
		'title'			=> __( 'Setup is already finished !' ),
		'navigation'	=> array(
			array(
				'label'		=> __( 'Dashboard' ),
				'url'		=> esc_url( admin_url( '/' ) )
			),

			// OR / AND
			array(
				'action' 	=> 'prev-step',
			),
			array(
				'action' 	=> 'next-step',
			),

			// OR / AND
			array(
				'type' => 'primary',
				'label'		=> __( 'Setup' ),
				'action'	=> 'setup-step'
			),

			// OR / AND
			array(
				'type'		=> 'primary',
				'label'		=> __( 'Customize Theme' ),
				'url'		=> esc_url( admin_url( '/customize.php' ) )
			)
		)
	));


= Fields =

Fields type can be:

- plugin - if is checked then Install and Activate the plugin Automatically on Start Setup.
- checkbox
- select
- url
- text
- textarea
- title
- hint
- html


== Installation ==

= Minimum Requirements =

* PHP version 5.3
* WordPress 4.6

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of myThemes Wizard, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “myThemes Wizard” and click Search Plugins. Once you've found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

== Changelog ==

= 0.0.4 =
* Fixed bug on install plugins

= 0.0.3 =
* Replace php shortcode "<?" with "<?php"

= 0.0.2 =
* Fix the Network Install Plugins Bug

== Screenshots ==

1. Welcome Step, Step 0 - this is first step with a welcome message, step type zero
2. Step 1 - this is default step with general website settings
3. Step 2 - this is default step with page settings
4. Step 3 - this is a setup step which allow choose from recommended plugins and after this start the Setup.
