<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WoocommerceIR_Bulk_SMS' ) && class_exists( 'WoocommerceIR_SMS_Bulk' ) ) {
	class WoocommerceIR_Bulk_SMS extends WoocommerceIR_SMS_Bulk {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Gateways_SMS' ) && class_exists( 'WoocommerceIR_SMS_Gateways' ) ) {
	class WoocommerceIR_Gateways_SMS extends WoocommerceIR_SMS_Gateways {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Helper' ) && class_exists( 'WoocommerceIR_SMS_Helper' ) ) {
	class WoocommerceIR_Helper extends WoocommerceIR_SMS_Helper {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Metabox_SMS' ) && class_exists( 'WoocommerceIR_SMS_Metabox' ) ) {
	class WoocommerceIR_Metabox_SMS extends WoocommerceIR_SMS_Metabox {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Notification_SMS' ) && class_exists( 'WoocommerceIR_SMS_Product_Events' ) ) {
	class WoocommerceIR_Notification_SMS extends WoocommerceIR_SMS_Product_Events {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Order_SMS' ) && class_exists( 'WoocommerceIR_SMS_Orders' ) ) {
	class WoocommerceIR_Order_SMS extends WoocommerceIR_SMS_Orders {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Tab_SMS' ) && class_exists( 'WoocommerceIR_SMS_Product_Tab' ) ) {
	class WoocommerceIR_Tab_SMS extends WoocommerceIR_SMS_Product_Tab {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Settings_Api' ) && class_exists( 'WoocommerceIR_SMS_Settings_Api' ) ) {
	class WoocommerceIR_Settings_Api extends WoocommerceIR_SMS_Settings_Api {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Settings_SMS' ) && class_exists( 'WoocommerceIR_SMS_Settings' ) ) {
	class WoocommerceIR_Settings_SMS extends WoocommerceIR_SMS_Settings {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

if ( ! class_exists( 'WoocommerceIR_Widget_SMS' ) && class_exists( 'WoocommerceIR_SMS_Subscription' ) ) {
	class WoocommerceIR_Widget_SMS extends WoocommerceIR_SMS_Subscription {

		public function __call( $name, $arguments ) {
			if ( method_exists( $this, $name ) ) {
				return call_user_func_array( [ $this, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}

		public static function __callStatic( $name, $arguments ) {
			if ( method_exists( __CLASS__, $name ) ) {
				return call_user_func_array( [ __CLASS__, $name ], $arguments );
			}
			_deprecated_function( __METHOD__, '4.0.0' );

			return false;
		}
	}
}

class WoocommerceIR_SMS_Deprecated_Hooks {

	public function __construct() {
		$this->filters();
		$this->actions();
	}

	private function filters() {

		add_filter( 'pwoosms_sms_gateways', function ( $gateway ) {
			return apply_filters( 'persianwoosms_sms_gateway', $gateway );
		}, 10, 1 );

		add_filter( 'pwoosms_settings_sections', function ( $sections ) {
			return apply_filters( 'persianwoosms_settings_sections', $sections );
		}, 10, 1 );

		add_filter( 'pwoosms_main_settings', function ( $settings ) {
			return apply_filters( 'sms_main_settings_settings', $settings );
		}, 10, 1 );

		add_filter( 'pwoosms_super_admin_settings', function ( $settings ) {
			return apply_filters( 'sms_super_admin_settings_settings', $settings );
		}, 10, 1 );

		add_filter( 'pwoosms_buyer_settings', function ( $settings ) {
			return apply_filters( 'sms_buyer_settings_settings', $settings );
		}, 10, 1 );

		add_filter( 'pwoosms_product_admin_settings', function ( $settings ) {
			return apply_filters( 'sms_product_admin_settings_settings', $settings );
		}, 10, 1 );

		add_filter( 'pwoosms_notif_settings', function ( $settings ) {
			return apply_filters( 'sms_notif_settings_settings', $settings );
		}, 10, 1 );

		add_filter( 'pwoosms_settings_fields', function ( $settings_fields ) {
			return apply_filters( 'persianwoosms_settings_section_content', $settings_fields );
		}, 10, 1 );

		add_filter( 'pwoosms_shortcodes_list', function ( $shortcodes ) {
			return apply_filters( 'persian_woo_sms_shortcode_list', $shortcodes );
		}, 10, 1 );

		add_filter( 'pwoosms_order_sms_body_before_replace', function ( $content, $shortcodes, $shortcodes_values, $order_id, $order, $product_ids ) {
			return apply_filters( 'persian_woo_sms_content_replace', $content, $shortcodes, $shortcodes_values, $order_id, $order, $product_ids );
		}, 10, 6 );

		add_filter( 'pwoosms_order_sms_body_after_replace', function ( $content, $order_id, $order, $product_ids ) {
			return apply_filters( 'persian_woo_sms_content', $content, $order_id, $order, $product_ids );
		}, 10, 4 );
	}

	private function actions() {

		add_action( 'pwoosms_before_product_newsletter_form', function ( $product ) {
			do_action( 'ps_woo_sms_before_notif_form', $product );
		}, 10, 1 );

		add_action( 'pwoosms_after_product_newsletter_form', function ( $product ) {
			do_action( 'ps_woo_sms_after_notif_form', $product );
		}, 10, 1 );

		add_action( 'pwoosms_product_sms_tab', function ( $product_id ) {
			do_action( 'woocommerce_product_sms', $product_id );
		}, 10, 1 );

		if ( class_exists( 'WoocommerceIR_SMS_Settings' ) ) {
			if ( method_exists( 'WoocommerceIR_SMS_Settings', 'settingSections' ) ) {

				$sections = WoocommerceIR_SMS_Settings::settingSections();
				$form_ids = wp_list_pluck( $sections, 'id' );

				foreach ( (array) $form_ids as $form_id ) {

					add_action( 'pwoosms_settings_form_top_' . $form_id, function ( $form ) use ( $form_id ) {
						do_action( 'ps_woo_sms_form_top_' . $form_id, $form );
					}, 10, 1 );

					add_action( 'pwoosms_settings_form_bottom_' . $form_id, function ( $form ) use ( $form_id ) {
						do_action( 'ps_woo_sms_form_bottom_' . $form_id, $form );
					}, 10, 1 );

					add_action( 'pwoosms_settings_form_submit_' . $form_id, function ( $form ) use ( $form_id ) {
						echo '<div style="padding-right: 10px">';
						do_action( 'ps_woo_sms_form_submit_' . $form_id, $form );
						echo '</div>';
					}, 10, 1 );

				}
			}
		}
	}

}

new WoocommerceIR_SMS_Deprecated_Hooks();

global $PWooSMS, $persianwoosms, $persianwoohelper;
if ( ! empty( $PWooSMS ) ) {
	$persianwoosms = $persianwoohelper = $PWooSMS;
}