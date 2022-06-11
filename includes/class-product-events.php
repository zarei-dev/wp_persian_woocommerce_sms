<?php

defined( 'ABSPATH' ) || exit;

class WoocommerceIR_SMS_Product_Events {

	private $enable_notification = false;
	private $enable_super_admin_sms = false;
	private $enable_product_admin_sms = false;

	public function __construct() {

		$this->enable_notification      = PWooSMS()->Options( 'enable_notif_sms_main' );
		$this->enable_super_admin_sms   = PWooSMS()->Options( 'enable_super_admin_sms' );
		$this->enable_product_admin_sms = PWooSMS()->Options( 'enable_product_admin_sms' );

		if ( $this->enable_notification || $this->enable_super_admin_sms || $this->enable_product_admin_sms ) {
			add_action( 'init', [ $this, 'init' ] );
		}
	}

	public function init() {

		$action = ! empty( $_POST['action'] ) ? str_ireplace( 'woocommerce_', '', sanitize_text_field( $_POST['action'] ) ) : '';
		if ( in_array( $action, [ 'add_variation', 'link_all_variations' ] ) ) {
			return;
		}

		/*onSale*/
		add_action( 'woocommerce_process_product_meta', [ $this, 'smsIsOnSale' ], 9999, 1 );
		add_action( 'woocommerce_update_product_variation', [ $this, 'smsIsOnSale' ], 9999, 1 );
		add_action( 'woocommerce_sms_send_onsale_event', [ $this, 'smsIsOnSale' ] );
		/*inStock*/
		add_action( 'woocommerce_product_set_stock_status', [ $this, 'smsInStock' ] );
		add_action( 'woocommerce_variation_set_stock_status', [ $this, 'smsInStock' ] );
		/*outStock*/
		add_action( 'woocommerce_product_set_stock_status', [ $this, 'smsOutStock' ] );
		add_action( 'woocommerce_variation_set_stock_status', [ $this, 'smsOutStock' ] );
		/*lowStock*/
		add_action( 'woocommerce_low_stock', [ $this, 'smsIsLowStock' ] );
		add_action( 'woocommerce_product_set_stock', [ $this, 'smsIsLowStock' ] );
		add_action( 'woocommerce_variation_set_stock', [ $this, 'smsIsLowStock' ] );
	}

	// وقتی محصول فروش ویژه شد : کاربر
	public function smsIsOnSale( $product_id = 0 ) {

		$product_id = PWooSMS()->MayBeVariable( $product_id );
		if ( is_array( $product_id ) ) {
			return array_map( [ $this, __FUNCTION__ ], $product_id );
		}

		$product           = wc_get_product( $product_id );
		$parent_product_id = PWooSMS()->ProductProp( $product, 'parent_id' );
		$parent_product_id = ! empty( $parent_product_id ) ? $parent_product_id : $product_id;
		/*-----------------------------------------------------------------*/

		$post_meta   = '_onsale_send';
		$schedule    = 'woocommerce_sms_send_onsale_event';
		$sale_price  = $product->get_sale_price();
		$is_schedule = current_action() == $schedule;

		if ( $sale_price === get_post_meta( $product_id, $post_meta, true ) ) {
			return false;
		} elseif ( ! $is_schedule ) {
			delete_post_meta( $product_id, $post_meta );
		}

		if ( PWooSMS()->hasNotifCond( 'enable_onsale', $parent_product_id ) ) {

			if ( ! $product->is_on_sale() ) {

				if ( ! $is_schedule ) {
					$date_from = PWooSMS()->ProductSalePriceTime( $product_id, 'from' );
					if ( ! empty( $date_from ) && $date_from > strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
						wp_schedule_single_event( $date_from + 3600, $schedule, [ $product_id ] );
					}
				}

				return delete_post_meta( $product_id, $post_meta );
			}

			wp_clear_scheduled_hook( $schedule );

			$data = [
				'post_id' => $parent_product_id,
				'type'    => 9,
				'mobile'  => WoocommerceIR_SMS_Contacts::getContactsMobiles( $parent_product_id, '_onsale' ),
				'message' => PWooSMS()->ReplaceTags( 'notif_onsale_sms', $product_id, $parent_product_id ),
			];

			if ( PWooSMS()->SendSMS( $data ) === true ) {
				//return update_post_meta( $product_id, $post_meta, $sale_price );
				//} else {
				//return delete_post_meta( $product_id, $post_meta );
			}

			return update_post_meta( $product_id, $post_meta, $sale_price );
		}
	}

	// وقتی محصول موجود شد : کاربر
	public function smsInStock( $product_id ) {

		$product_id = PWooSMS()->MayBeVariable( $product_id );
		if ( is_array( $product_id ) ) {
			return array_map( [ $this, __FUNCTION__ ], $product_id );
		}

		$product           = wc_get_product( $product_id );
		$parent_product_id = PWooSMS()->ProductProp( $product, 'parent_id' );
		$parent_product_id = ! empty( $parent_product_id ) ? $parent_product_id : $product_id;
		/*-----------------------------------------------------------------*/

		$post_meta = '_in_stock_send';

		if ( ! $product->is_in_stock() ) {
			return delete_post_meta( $product_id, $post_meta );
		}

		if ( PWooSMS()->maybeBool( get_post_meta( $product_id, $post_meta, true ) ) ) {
			return false;
		}

		if ( PWooSMS()->hasNotifCond( 'enable_notif_no_stock', $parent_product_id ) ) {

			$data = [
				'post_id' => $parent_product_id,
				'type'    => 11,
				'mobile'  => WoocommerceIR_SMS_Contacts::getContactsMobiles( $parent_product_id, '_in' ),
				'message' => PWooSMS()->ReplaceTags( 'notif_no_stock_sms', $product_id, $parent_product_id ),
			];

			if ( PWooSMS()->SendSMS( $data ) === true ) {
				//return update_post_meta( $product_id, $post_meta, 'yes' );
				//} else {
				//return delete_post_meta( $product_id, $post_meta );
			}

			return update_post_meta( $product_id, $post_meta, 'yes' );
		}
	}

	// وقتی محصول ناموجود شد : مدیران کل و مدیران محصول
	public function smsOutStock( $product_id ) {

		$product_id = PWooSMS()->MayBeVariable( $product_id );
		if ( is_array( $product_id ) ) {
			return array_map( [ $this, __FUNCTION__ ], $product_id );
		}

		$product           = wc_get_product( $product_id );
		$parent_product_id = PWooSMS()->ProductProp( $product, 'parent_id' );
		$parent_product_id = ! empty( $parent_product_id ) ? $parent_product_id : $product_id;
		/*-----------------------------------------------------------------*/

		$post_meta = '_out_stock_send_sms';

		if ( $product->is_in_stock() ) {
			return delete_post_meta( $product_id, $post_meta );
		}

		if ( PWooSMS()->maybeBool( get_post_meta( $product_id, $post_meta, true ) ) ) {
			return false;
		}

		if ( $this->smsAdminsStocks( $product_id, $parent_product_id, 'out', 7 ) ) {
			//return update_post_meta( $product_id, $post_meta, 'yes' );
			//} else {
			//return delete_post_meta( $product_id, $post_meta );
		}

		return update_post_meta( $product_id, $post_meta, 'yes' );
	}

	// محصول رو به اتمام است : مدیر و کاربر

	private function smsAdminsStocks( $product_id, $parent_product_id, $status, $type ) {

		$mobiles = [];
		if ( $this->enable_super_admin_sms ) {
			if ( in_array( $status, (array) PWooSMS()->Options( 'super_admin_order_status' ) ) ) {
				$mobiles = array_merge( $mobiles, explode( ',', PWooSMS()->Options( 'super_admin_phone' ) ) );
			}
		}
		if ( $this->enable_product_admin_sms ) {
			$mobiles = array_merge( $mobiles, array_keys( PWooSMS()->ProductAdminMobiles( $parent_product_id, $status ) ) );
		}

		$mobiles = array_map( 'trim', $mobiles );
		$mobiles = array_unique( array_filter( $mobiles ) );

		if ( ! empty( $mobiles ) ) {
			$data = [
				'post_id' => $parent_product_id,
				'type'    => $type,
				'mobile'  => $mobiles,
				'message' => PWooSMS()->ReplaceTags( "admin_{$status}_stock", $product_id, $parent_product_id ),
			];

			return PWooSMS()->SendSMS( $data ) === true;
		}

		return false;
	}

	public function smsIsLowStock( $product_id ) {

		if ( 'yes' !== get_option( 'woocommerce_manage_stock' ) ) {
			return false;
		}

		$product_id = PWooSMS()->MayBeVariable( $product_id );
		if ( is_array( $product_id ) ) {
			return array_map( [ $this, __FUNCTION__ ], $product_id );
		}

		$product           = wc_get_product( $product_id );
		$parent_product_id = PWooSMS()->ProductProp( $product, 'parent_id' );
		$parent_product_id = ! empty( $parent_product_id ) ? $parent_product_id : $product_id;
		/*-----------------------------------------------------------------*/

		if ( ! PWooSMS()->IsStockManaging( $product ) ) {
			return false;
		}

		$post_meta = '_low_stock_send';

		$quantity = PWooSMS()->ProductStockQty( $product );
		if ( $quantity > get_option( 'woocommerce_notify_low_stock_amount' ) || $quantity <= get_option( 'woocommerce_notify_no_stock_amount' ) ) {
			return delete_post_meta( $product_id, $post_meta );
		}

		if ( PWooSMS()->maybeBool( get_post_meta( $product_id, $post_meta, true ) ) ) {
			return false;
		}

		//کاربر
		if ( PWooSMS()->hasNotifCond( 'enable_notif_low_stock', $parent_product_id ) ) {
			$data         = [
				'post_id' => $parent_product_id,
				'type'    => 13,
				'mobile'  => WoocommerceIR_SMS_Contacts::getContactsMobiles( $parent_product_id, '_low' ),
				'message' => PWooSMS()->ReplaceTags( 'notif_low_stock_sms', $product_id, $parent_product_id ),
			];
			$result_users = PWooSMS()->SendSMS( $data ) === true;
		}

		//مدیر
		if ( $this->smsAdminsStocks( $product_id, $parent_product_id, 'low', 8 ) || ! empty( $result_users ) ) {
			//return update_post_meta( $product_id, $post_meta, 'yes' );
			//} else {
			//return delete_post_meta( $product_id, $post_meta );
		}

		return update_post_meta( $product_id, $post_meta, 'yes' );
	}
}

new WoocommerceIR_SMS_Product_Events();