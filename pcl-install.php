<?php

if (!defined('ABSPATH')) {
	exit;
}

class Pickle_Custom_Login_Install {

	private static $db_updates = array(
		'2.0.0' => array(
			'wc_update_200_file_paths',
			'wc_update_200_permalinks',
			'wc_update_200_subcat_display',
			'wc_update_200_taxrates',
			'wc_update_200_line_items',
			'wc_update_200_images',
			'wc_update_200_db_version',
		),
	);

	private static $background_updater;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );



		add_action( 'woocommerce_plugin_background_installer', array( __CLASS__, 'background_installer' ), 10, 2 );
		add_action( 'woocommerce_theme_background_installer', array( __CLASS__, 'theme_background_installer' ), 10, 1 );
	}

	public static function init_background_updater() {
		include_once( dirname( __FILE__ ) . '/class-wc-background-updater.php' );
		self::$background_updater = new WC_Background_Updater();
	}

	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'woocommerce_version' ) !== WC()->version ) {
			self::install();
			do_action( 'woocommerce_updated' );
		}
	}

	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'wc_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'wc_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		wc_maybe_define_constant( 'WC_INSTALLING', true );

		self::remove_admin_notices();
		self::create_options();
		self::create_tables();
		self::create_roles();
		self::setup_environment();
		self::create_terms();
		self::create_cron_jobs();
		self::create_files();
		self::maybe_enable_setup_wizard();
		self::update_wc_version();
		self::maybe_update_db_version();

		delete_transient( 'wc_installing' );

		do_action( 'woocommerce_flush_rewrite_rules' );
		do_action( 'woocommerce_installed' );
	}

	private static function is_new_install() {
		return is_null( get_option( 'woocommerce_version', null ) ) && is_null( get_option( 'woocommerce_db_version', null ) );
	}

	private static function needs_db_update() {
		$current_db_version = get_option( 'woocommerce_db_version', null );
		$updates            = self::get_db_update_callbacks();

		return ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
	}

	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			if ( apply_filters( 'woocommerce_enable_auto_update_db', false ) ) {
				self::init_background_updater();
				self::update();
			} else {
				WC_Admin_Notices::add_notice( 'update' );
			}
		} else {
			self::update_db_version();
		}
	}

	private static function update_wc_version() {
		delete_option( 'woocommerce_version' );
		add_option( 'woocommerce_version', WC()->version );
	}

	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}

	private static function update() {
		$current_db_version = get_option( 'woocommerce_db_version' );
		$logger             = wc_get_logger();
		$update_queued      = false;

		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					$logger->info(
						sprintf( 'Queuing %s - %s', $version, $update_callback ),
						array( 'source' => 'wc_db_updates' )
					);
					self::$background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			self::$background_updater->save()->dispatch();
		}
	}

	public static function update_db_version( $version = null ) {
		delete_option( 'woocommerce_db_version' );
		add_option( 'woocommerce_db_version', is_null( $version ) ? WC()->version : $version );
	}

	public static function create_pages() {
		include_once( dirname( __FILE__ ) . '/admin/wc-admin-functions.php' );

		$pages = apply_filters( 'woocommerce_create_pages', array(
			'shop' => array(
				'name'    => _x( 'shop', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'Shop', 'Page title', 'woocommerce' ),
				'content' => '',
			),
			'cart' => array(
				'name'    => _x( 'cart', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'Cart', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']',
			),
			'checkout' => array(
				'name'    => _x( 'checkout', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'Checkout', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']',
			),
			'myaccount' => array(
				'name'    => _x( 'my-account', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'My account', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']',
			),
		) );

		foreach ( $pages as $key => $page ) {
			wc_create_page( esc_sql( $page['name'] ), 'woocommerce_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : '' );
		}

		delete_transient( 'woocommerce_cache_excluded_uris' );
	}

	private static function create_options() {
		// Include settings so that we can run through defaults
		include_once( dirname( __FILE__ ) . '/admin/class-wc-admin-settings.php' );

		$settings = WC_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			if ( ! method_exists( $section, 'get_settings' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}




	public static function background_installer( $plugin_to_install_id, $plugin_to_install ) {
		// Explicitly clear the event.
		wp_clear_scheduled_hook( 'woocommerce_plugin_background_installer', func_get_args() );

		if ( ! empty( $plugin_to_install['repo-slug'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
			require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			WP_Filesystem();

			$skin              = new Automatic_Upgrader_Skin;
			$upgrader          = new WP_Upgrader( $skin );
			$installed_plugins = array_map( array( __CLASS__, 'format_plugin_slug' ), array_keys( get_plugins() ) );
			$plugin_slug       = $plugin_to_install['repo-slug'];
			$plugin            = $plugin_slug . '/' . $plugin_slug . '.php';
			$installed         = false;
			$activate          = false;

			// See if the plugin is installed already
			if ( in_array( $plugin_to_install['repo-slug'], $installed_plugins ) ) {
				$installed = true;
				$activate  = ! is_plugin_active( $plugin );
			}

			// Install this thing!
			if ( ! $installed ) {
				// Suppress feedback
				ob_start();

				try {
					$plugin_information = plugins_api( 'plugin_information', array(
						'slug'   => $plugin_to_install['repo-slug'],
						'fields' => array(
							'short_description' => false,
							'sections'          => false,
							'requires'          => false,
							'rating'            => false,
							'ratings'           => false,
							'downloaded'        => false,
							'last_updated'      => false,
							'added'             => false,
							'tags'              => false,
							'homepage'          => false,
							'donate_link'       => false,
							'author_profile'    => false,
							'author'            => false,
						),
					) );

					if ( is_wp_error( $plugin_information ) ) {
						throw new Exception( $plugin_information->get_error_message() );
					}

					$package  = $plugin_information->download_link;
					$download = $upgrader->download_package( $package );

					if ( is_wp_error( $download ) ) {
						throw new Exception( $download->get_error_message() );
					}

					$working_dir = $upgrader->unpack_package( $download, true );

					if ( is_wp_error( $working_dir ) ) {
						throw new Exception( $working_dir->get_error_message() );
					}

					$result = $upgrader->install_package( array(
						'source'                      => $working_dir,
						'destination'                 => WP_PLUGIN_DIR,
						'clear_destination'           => false,
						'abort_if_destination_exists' => false,
						'clear_working'               => true,
						'hook_extra'                  => array(
							'type'   => 'plugin',
							'action' => 'install',
						),
					) );

					if ( is_wp_error( $result ) ) {
						throw new Exception( $result->get_error_message() );
					}

					$activate = true;

				} catch ( Exception $e ) {
					WC_Admin_Notices::add_custom_notice(
						$plugin_to_install_id . '_install_error',
						sprintf(
							__( '%1$s could not be installed (%2$s). <a href="%3$s">Please install it manually by clicking here.</a>', 'woocommerce' ),
							$plugin_to_install['name'],
							$e->getMessage(),
							esc_url( admin_url( 'index.php?wc-install-plugin-redirect=' . $plugin_to_install['repo-slug'] ) )
						)
					);
				}

				// Discard feedback
				ob_end_clean();
			}

			wp_clean_plugins_cache();

			// Activate this thing
			if ( $activate ) {
				try {
					$result = activate_plugin( $plugin );

					if ( is_wp_error( $result ) ) {
						throw new Exception( $result->get_error_message() );
					}
				} catch ( Exception $e ) {
					WC_Admin_Notices::add_custom_notice(
						$plugin_to_install_id . '_install_error',
						sprintf(
							__( '%1$s was installed but could not be activated. <a href="%2$s">Please activate it manually by clicking here.</a>', 'woocommerce' ),
							$plugin_to_install['name'],
							admin_url( 'plugins.php' )
						)
					);
				}
			}
		}
	}


}

Pickle_Custom_Login_Install::init();
?>