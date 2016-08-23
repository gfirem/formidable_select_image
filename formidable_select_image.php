<?php
/*
 * Plugin Name:       Formidable select image
 * Plugin URI:        https://github.com/gfirem/formidable_select_image
 * Description:       Formidable custom field to use wp media library
 * Version:           1.00
 * Author:            Guillermo Figueroa Mesa
 * Author URI:        http://wwww.gfirem.com
 * Text Domain:       formidable_select_image-locale
 * License:           Apache License 2.0
 * License URI:       http://www.apache.org/licenses/
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'class/FormidableSelectImageManager.php';

require_once 'plugin-update-checker/plugin-update-checker.php';

$myUpdateChecker = PucFactory::buildUpdateChecker( 'http://gfirem.com/update-services/?action=get_metadata&slug=formidable_select_image', __FILE__ );
$myUpdateChecker->addQueryArgFilter( 'appendFormidableSelectImageQueryArgsCredentials' );

define( 'FSIMAGE_CSS_PATH', plugin_dir_url( __FILE__ ) . 'css/' );

/**
 * Append the order key to the update server URL
 *
 * @param $queryArgs
 *
 * @return
 */
function appendFormidableSelectImageQueryArgsCredentials( $queryArgs ) {
	$queryArgs['order_key'] = get_option( FormidableSelectImageManager::getShort() . 'licence_key', '' );

	return $queryArgs;
}

function FormidableSelectImageBootLoader() {
	add_action( 'plugins_loaded', 'setFormidableSelectImageTranslation' );
	$manager = new FormidableSelectImageManager();
	$manager->run();
}

function checkRequiredFormidableSelectImage() {
	if ( ! class_exists( "FrmHooksController" ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			FormidableCopyActionManager::t( 'This plugins required Formidable to run!' ),
			FormidableCopyActionManager::t( 'Formidable Copy Action' ),
			array( 'back_link' => true )
		);
	}
}

register_activation_hook( __FILE__, "checkRequiredFormidableSelectImage" );
/**
 * Add translation files
 */
function setFormidableSelectImageTranslation() {
	load_plugin_textdomain( 'formidable_select_image-locale', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

FormidableSelectImageBootLoader();