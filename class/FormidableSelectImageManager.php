<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class FormidableSelectImageManager {
	/**
	 * @var FormidableSelectImageLoader
	 */
	protected $loader;

	protected $plugin_slug;
	private static $plugin_short = 'FormidableSelectImage';

	private static $version;

	public function __construct() {

		$this->plugin_slug = 'formidable-select-image';
		self::$version     = '1.00';

		$this->load_dependencies();
		$this->define_admin_hooks();

	}

	static function getShort() {
		return self::$plugin_short;
	}

	static function getVersion() {
		return self::$version;
	}

	private function load_dependencies() {

		require_once plugin_dir_path( __FILE__ ) . 'FormidableSelectImageLoader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class/FormidableSelectImageAdmin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class/FormidableSelectImageSettings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class/GManagerFactory.php';

		$this->loader = new FormidableSelectImageLoader();
	}

	private function define_admin_hooks() {
		$gManager = GManagerFactory::buildManager( 'FormidableSelectImageManager', 'formidable_select_image', self::getShort() );
		$admin    = new FormidableSelectImageAdmin( $this->get_version(), $this->plugin_slug, $gManager );

		if ( class_exists( "FrmProAppController" ) ) {
			$this->loader->add_action( 'frm_pro_available_fields', $admin, 'add' . self::getShort() . 'Field' );
		} else {
			$this->loader->add_action( 'frm_available_fields', $admin, 'add' . self::getShort() . 'Field' );
		}

		$this->loader->add_action( 'wp_enqueue_scripts', $admin, 'enqueue_' . self::getShort() . '_js' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_' . self::getShort() . '_js' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_' . self::getShort() . '_style' );
		$this->loader->add_action( 'frm_before_field_created', $admin, 'set' . self::getShort() . 'Options' );
		$this->loader->add_action( 'frm_display_added_fields', $admin, 'show' . self::getShort() . 'AdminField' );
		$this->loader->add_action( 'frm_form_fields', $admin, 'show' . self::getShort() . 'FrontField', 10, 2 );
		$this->loader->add_action( 'frm_display_value', $admin, 'display' . self::getShort() . 'AdminField', 10, 3 );
		$this->loader->add_action( 'frm_add_settings_section', $admin, 'add' . self::getShort() . 'SettingPage', 10, 3 );
		$this->loader->add_filter( 'plugin_action_links', $admin, 'add' . self::getShort() . 'SettingLink', 9, 2 );
		$this->loader->add_filter( 'frmpro_fields_replace_shortcodes', $admin, 'shortCode' . self::getShort() . 'Replace', 10, 4 );
		$this->loader->add_filter( 'frm_display_field_options', $admin, 'add' . self::getShort() . 'DisplayOptions' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_version() {
		return self::$version;
	}

	/**
	 * Translate string to main Domain
	 *
	 * @param $str
	 *
	 * @return string|void
	 */
	public static function t( $str ) {
		return __( $str, 'formidable_select_image-locale' );
	}
}