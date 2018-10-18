<?php
/**
 * Plugin Name: Hosting Panel for Beget
 * Plugin URI: https://nektobit.ru/
 * Description: Information panel for Beget hosting clients. Find out information about your account without leaving the admin panel of your site.
 * Version: 1.0.0
 * Author: nektobit
 * Author URI: https://nektobit.ru
 * License: GPLv2 or later
 *
 * Text Domain: beget-hm
 * Domain Path: /i18n/languages/
 *
 * @package BegetHM
 * @category Core
 * @author nektobit
 */

 /* Тест */

defined( 'ABSPATH' ) || exit;
define( 'BEGET_PLUGIN_FILE', __FILE__ );
define( 'BEGET_DOMAIN', 'beget-hm' );

load_plugin_textdomain( BEGET_DOMAIN, false, plugin_basename( dirname( BEGET_PLUGIN_FILE ) ) . '/i18n/languages' );

// Include the main Beget hosting manager class.
if ( ! class_exists( 'BegetHM' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-begethm.php';
}

/**
 * Main instance of BegetHM.
 *
 * Returns the main instance of BegetHM to prevent the need to use globals.
 *
 * @since  1.0
 * @return BegetHM
 */
function beget() {
	return BegetHM::instance();
}

beget();