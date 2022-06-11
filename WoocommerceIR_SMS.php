<?php
/*
Plugin Name: پیامک حرفه ای ووکامرس
Version: 4.4.3
Plugin URI: https://woosupport.ir
Description: افزونه کامل و حرفه ای برای اطلاع رسانی پیامکی سفارشات و رویداد های محصولات ووکامرس. تمامی حقوق این افزونه متعلق به <a href="http://woosupport.ir" target="_blank">تیم ووکامرس پارسی</a> می باشد و هر گونه کپی برداری، فروش آن غیر مجاز می باشد.
Author URI: https://woosupport.ir
Author: ووکامرس فارسی
Contributors: Persianscript
WC tested up to: 6.5.1
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Location: http://woocommerce.ir' );
	exit;
}

if ( ! defined( 'PWOOSMS_VERSION' ) ) {
	define( 'PWOOSMS_VERSION', '4.4.3' );
	define( 'PS_WOO_SMS_VERSION', PWOOSMS_VERSION );//deprecated
}

if ( ! defined( 'PWOOSMS_URL' ) ) {
	define( 'PWOOSMS_URL', plugins_url( '', __FILE__ ) );
	define( 'PS_WOO_SMS_PLUGIN_PATH', PWOOSMS_URL );//deprecated
}

if ( ! defined( 'PWOOSMS_INCLUDE_DIR' ) ) {
	define( 'PWOOSMS_INCLUDE_DIR', dirname( __FILE__ ) . '/includes' );
	define( 'PS_WOO_SMS_PLUGIN_LIB_PATH', PWOOSMS_INCLUDE_DIR );//deprecated
}

register_activation_hook( __FILE__, 'WoocommerceIR_SMS_Register' );
register_deactivation_hook( __FILE__, 'WoocommerceIR_SMS_Register' );
function WoocommerceIR_SMS_Register() {
	delete_option( 'pwoosms_table_archive' );
	delete_option( 'pwoosms_table_contacts' );
	delete_option( 'pwoosms_hide_about_page' );
	delete_option( 'pwoosms_redirect_about_page' );
}

require_once 'includes/class-gateways.php';
require_once 'includes/class-settings-api.php';
require_once 'includes/class-settings.php';
require_once 'includes/class-helper.php';
require_once 'includes/class-bulk.php';
require_once 'includes/class-about.php';
require_once 'includes/class-ads.php';

require_once 'includes/class-metabox.php';
require_once 'includes/class-subscription.php';
require_once 'includes/class-product-tab.php';
require_once 'includes/class-product-events.php';
require_once 'includes/class-orders.php';
require_once 'includes/class-archive.php';
require_once 'includes/class-contacts.php';

require_once 'includes/class-deprecateds.php';
