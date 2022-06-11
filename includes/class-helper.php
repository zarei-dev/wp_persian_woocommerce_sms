<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WoocommerceIR_SMS_Helper {

	private static $_instance = false;
	private static $all_options = array();

	public static function init() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function Options( $option, $section = '', $default = '' ) {

		if ( ! empty( $section ) && ( ! is_string( $section ) || stripos( $section, '_settings' ) === false ) ) {

			if ( $section == '__' ) {
				$skip = true;
			} else {
				$default = $section;
			}

			unset( $section );
		}

		if ( ! empty( $section ) ) {
			$options = get_option( $section );
		} else {

			if ( empty( self::$all_options ) ) {

				$sections = WoocommerceIR_SMS_Settings::settingSections();
				$sections = wp_list_pluck( $sections, 'id' );

				$options = array();
				foreach ( $sections as $section ) {
					$section = get_option( $section );
					if ( ! empty( $section ) ) {
						$options = array_merge( $options, $section );
					}
				}

				self::$all_options = $options;
			}

			$options = self::$all_options;
		}

		$option = isset( $options[ $option ] ) ? $options[ $option ] : $default;

		if ( empty( $skip ) && ! empty( $option ) && is_string( $option ) ) {
			$option = $this->maybeBool( $option );
		}


		return $option;
	}

	public function modifyStatus( $status ) {
		return str_ireplace( array( 'wc-', 'wc_' ), '', $status );
	}

	public function statusName( $status, $pending = false ) {

		$status = wc_get_order_status_name( $status );
		if ( $status == 'created' ) {
			$pending_label = _x( 'Pending payment', 'Order status', 'woocommerce' );
			$status        = $pending ? $pending_label : $pending_label . ' (بلافاصله بعد از ثبت سفارش)';
		}

		return $status;
	}

	public function GetAllStatuses( $pending = false ) {

		if ( ! function_exists( 'wc_get_order_statuses' ) ) {
			return array();
		}

		$statuses = wc_get_order_statuses();

		$pending_label = _x( 'Pending payment', 'Order status', 'woocommerce' );
		if ( ! empty( $statuses['wc-pending'] ) ) {
			$statuses['wc-pending'] = $pending ? $pending_label : $pending_label . ' (بعد از تغییر وضعیت سفارش)';
		}
		if ( empty( $statuses['wc-created'] ) ) {
			$statuses = array_merge( array( 'wc-created' => $pending ? 'بعد از ثبت سفارش' : $pending_label . ' (بلافاصله بعد از ثبت سفارش)' ), $statuses );
		}

		$opt_statuses = array();
		foreach ( (array) $statuses as $status_val => $status_name ) {
			$opt_statuses[ $this->modifyStatus( $status_val ) ] = $status_name;
		}

		return $opt_statuses;
	}

	public function GetAllSuperAdminStatuses( $pending = false ) {
		$opt_statuses        = $this->GetAllStatuses( $pending );
		$opt_statuses['low'] = 'کم بودن موجودی انبار';
		$opt_statuses['out'] = 'تمام شدن موجودی انبار';

		return $opt_statuses;
	}

	public function GetAllProductAdminStatuses( $pending = false ) {

		$opt_statuses = $this->GetAllSuperAdminStatuses( $pending );

		return $opt_statuses;
	}

	public function prepareAdminProductStatus( $statuses, $array = true ) {

		$delimator = '-sv-';

		if ( ! is_array( $statuses ) ) {
			$statuses = explode( $delimator, $statuses );
		}

		$statuses = array_map( 'trim', $statuses );
		$statuses = array_map( array( $this, 'sanitize_text_field' ), $statuses );
		$statuses = array_unique( array_filter( $statuses ) );

		//واسه مقایسه کردن لازم میشه
		sort( $statuses );

		if ( $array ) {
			return $statuses;
		}

		return implode( $delimator, $statuses );
	}

	public function GetBuyerAllowedStatuses( $pending = false ) {

		$statuses              = $this->GetAllStatuses( $pending );
		$order_status_settings = (array) $this->Options( 'order_status', array() );

		$allowed_statuses = array();
		foreach ( (array) $statuses as $status_val => $status_name ) {
			if ( in_array( $status_val, array_keys( $order_status_settings ) ) ) {
				$allowed_statuses[ $status_val ] = $status_name;
			}
		}

		return $allowed_statuses;
	}

	public function MaybeVariableProductTitle( $product ) {

		$product_id = $this->ProductId( $product );

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$attributes = $this->ProductProp( $product, 'variation_attributes' );
		$parent_id  = $this->ProductProp( $product, 'parent_id' );

		if ( ! empty( $attributes ) && ! empty( $parent_id ) ) {

			$parent = wc_get_product( $parent_id );

			$variation_attributes = $this->ProductProp( $parent, 'variation_attributes' );

			$variable_title = array();
			foreach ( (array) $attributes as $attribute_name => $options ) {

				$attribute_name = str_ireplace( 'attribute_', '', $attribute_name );

				foreach ( (array) $variation_attributes as $key => $value ) {
					$key = str_ireplace( 'attribute_', '', $key );

					if ( sanitize_title( $key ) == sanitize_title( $attribute_name ) ) {
						$attribute_name = $key;
						break;
					}
				}

				if ( ! empty( $options ) && substr( strtolower( $attribute_name ), 0, 3 ) !== 'pa_' ) {
					$variable_title[] = $attribute_name . ':' . $options;
				}
			}

			$product_title = get_the_title( $parent_id );

			if ( ! empty( $variable_title ) ) {
				$product_title .= ' (' . implode( ' - ', $variable_title ) . ')';
			}
		} else {
			$product_title = get_the_title( $product_id );
		}

		return html_entity_decode( urldecode( $product_title ) );
	}

	public function MayBeVariable( $product ) {

		$product_id = $this->ProductId( $product );
		$product    = wc_get_product( $product_id );
		if ( $product->is_type( 'variable' ) ) {

			unset( $product_id );

			$product_ids = array();
			foreach ( (array) $this->ProductProp( $product, 'children' ) as $product_id ) {
				//$product_ids[] = wc_get_product( $product_id );
				$product_ids[] = $product_id;
			}

			return $product_ids;//array
		} else {
			return $product_id;//int
		}
	}

	public function ProductHasProp( $product, $prop ) {

		$check = true;

		$product_ids = (array) $this->MayBeVariable( $product );
		foreach ( $product_ids as $product_id ) {

			$product = wc_get_product( $product_id );

			if ( $prop == 'is_not_low_stock' ) {

				if ( $check = ( PWooSMS()->IsStockManaging( $product ) && $product->is_in_stock() && $this->ProductStockQty( $product_id ) > get_option( 'woocommerce_notify_low_stock_amount' ) ) ) {
					break;
				}

			} elseif ( method_exists( $product, $prop ) ) {
				$check = $check && $product->$prop();
			} else {
				$check = false;
			}
		}

		return $check;
	}


	public function ProductSalePriceTime( $product, $type = '' ) {

		if ( is_numeric( $product ) ) {
			$product_id = $product;
			$product    = wc_get_product( $product_id );
		} else {
			$product_id = $this->ProductId( $product );
		}

		$timestamp = '';
		$method    = 'get_date_on_sale_' . $type;
		if ( method_exists( $product, $method ) ) {
			$timestamp = $product->$method();
			if ( method_exists( $timestamp, 'getOffsetTimestamp' ) ) {
				$timestamp = $timestamp->getOffsetTimestamp();
			} else {
				$timestamp = '';
			}
		}
		if ( empty( $timestamp ) ) {
			$timestamp = get_post_meta( $product_id, '_sale_price_dates_' . $type, true );
		}

		return $timestamp;
	}


	public static function multiSelectAndCheckbox( $field, $key, $args, $value ) {

		$after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required        = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
		} else {
			$required = '';
		}

		$custom_attributes = array();
		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( $args['type'] == "pwoosms_multiselect" ) {
			$value = is_array( $value ) ? $value : array( $value );
			if ( ! empty( $args['options'] ) ) {
				$options = '';
				foreach ( $args['options'] as $option_key => $option_text ) {
					$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( in_array( $option_key, $value ), 1, false ) . '>' . esc_attr( $option_text ) . '</option>';
				}
				$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) . '" id="' . esc_attr( $key ) . '_field">';
				if ( $args['label'] ) {
					$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) . '">' . $args['label'] . $required . '</label>';
				}
				$field .= '<select name="' . esc_attr( $key ) . '[]" id="' . esc_attr( $key ) . '" class="select" multiple="multiple" ' . implode( ' ', $custom_attributes ) . '>'
				          . $options
				          . ' </select>';

				if ( $args['description'] ) {
					$field .= '<span class="description">' . ( $args['description'] ) . '</span>';
				}

				$field .= '</p>' . $after;
			}
		}

		if ( $args['type'] == "pwoosms_multicheckbox" ) {
			$value = is_array( $value ) ? $value : array( $value );
			if ( ! empty( $args['options'] ) ) {
				$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) . '" id="' . esc_attr( $key ) . '_field">';
				if ( $args['label'] ) {
					$field .= '<label for="' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . implode( ' ', $args['label_class'] ) . '">' . $args['label'] . $required . '</label>';
				}
				foreach ( $args['options'] as $option_key => $option_text ) {
					$field .= '<input type="checkbox" class="input-checkbox" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '[]" id="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '"' . checked( in_array( $option_key, $value ), 1, false ) . ' />';
					$field .= '<label for="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '" class="checkbox ' . implode( ' ', $args['label_class'] ) . '">' . $option_text . '</label><br>';
				}
				if ( $args['description'] ) {
					$field .= '<span class="description">' . ( $args['description'] ) . '</span>';
				}
				$field .= '</p>' . $after;
			}
		}

		return $field;
	}


	public function multiSelectAdminField( $field ) {

		if ( ! isset( $field['placeholder'] ) ) {
			$field['placeholder'] = '';
		}
		if ( ! isset( $field['class'] ) ) {
			$field['class'] = 'short';
		}
		if ( ! isset( $field['options'] ) ) {
			$field['options'] = array();
		}

		if ( ! empty( $field['value'] ) ) {
			$field['value'] = array_filter( (array) $field['value'] );
		}
		//dont use else
		if ( empty( $field['value'] ) ) {
			$field['value'] = isset( $field['default'] ) ? $field['default'] : array();
		}

		$field['value']   = (array) $field['value'];
		$field['options'] = (array) $field['options'];

		echo '<p class="form-field ' . esc_attr($field['id']) . '_field"><label style="display:block;" for="' . esc_attr( $field['id'] ) . '">' . esc_attr( $field['label'] ). '</label>';
		echo '<select multiple="multiple" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['id']) . '[]" id="' . esc_attr($field['id']) . '" ' . '>';

		foreach ( $field['options'] as $status_value => $status_name ) {
			echo '<option value="' . esc_attr( $status_value ) . '"' . selected( in_array( $status_value, esc_attr($field['value']) ), true, false ) . '>' . esc_attr( $status_name ) . '</option>';
		}

		echo '</select>';
		echo '</p>';
	}

	public function Meta_Saved_Mobile( $meta, $post_id = 0, $empty_array = array() ) {

		if ( empty( $post_id ) ) {
			global $post;
			$post_id = is_object( $post ) && ! empty( $post->ID ) ? $post->ID : 0;
		}
		if ( empty( $post_id ) ) {
			return $empty_array;
		}

		$data = get_post_meta( $post_id, '_pwoosms_product_admin_meta_' . $meta, true );
		if ( ! empty( $data ) ) {
			return (array) $data;//mobile and statuses that set via admin
		}

		return $post_id;
	}

	public function User_Meta_Mobile( $post_id = 0 ) {

		$meta        = 'user';
		$empty_array = array( 'meta' => $meta, 'mobile' => '', 'statuses' => '' );
		$data        = $this->Meta_Saved_Mobile( $meta, $post_id, $empty_array );
		if ( is_array( $data ) ) {
			return $data;
		}

		$meta_key = $this->Options( "product_admin_{$meta}_meta" );
		if ( empty( $meta_key ) ) {
			unset( $empty_array['meta'] );

			return $empty_array;
		}

		$post_id = intval( $data );
		$post    = get_post( $post_id );

		if ( empty( $post->post_author ) ) {
			return $empty_array;
		}

		return array(
			'meta'     => $meta,
			'mobile'   => get_user_meta( $post->post_author, $meta_key, true ),
			'statuses' => $this->Options( 'product_admin_meta_order_status' )
		);
	}

	public function Post_Meta_Mobile( $post_id = 0 ) {

		$meta        = 'post';
		$empty_array = array( 'meta' => $meta, 'mobile' => '', 'statuses' => '' );
		$data        = $this->Meta_Saved_Mobile( $meta, $post_id, $empty_array );
		if ( is_array( $data ) ) {
			return $data;
		}

		$meta_key = $this->Options( "product_admin_{$meta}_meta" );
		if ( empty( $meta_key ) ) {
			unset( $empty_array['meta'] );

			return $empty_array;
		}

		$post_id = intval( $data );

		return array(
			'meta'     => $meta,
			'mobile'   => get_post_meta( $post_id, $meta_key, true ),
			'statuses' => $this->Options( 'product_admin_meta_order_status' )
		);
	}

	public function mayBeJalaliDate( $date_time ) {

		if ( empty( $date_time ) ) {
			return '';
		}

		$_date_time = explode( ' ', $date_time );
		$date       = ! empty( $_date_time[0] ) ? explode( '-', $_date_time[0], 3 ) : '';
		$time       = ! empty( $_date_time[1] ) ? $_date_time[1] : '';

		if ( count( $date ) != 3 || $date[0] < 2000 ) {
			return $date_time;
		}

		list( $year, $month, $day ) = $date;

		$date = $this->JalaliDate( $year, $month, $day, '/' ) . ' - ' . $time;

		return trim( trim( $date ), '- ' );
	}

	//از سایت jdf
	public function JalaliDate( $g_y, $g_m, $g_d, $mod = '' ) {
		$d_4   = $g_y % 4;
		$g_a   = array( 0, 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
		$doy_g = $g_a[ (int) $g_m ] + $g_d;
		if ( $d_4 == 0 and $g_m > 2 ) {
			$doy_g ++;
		}
		$d_33 = (int) ( ( ( $g_y - 16 ) % 132 ) * .0305 );
		$a    = ( $d_33 == 3 or $d_33 < ( $d_4 - 1 ) or $d_4 == 0 ) ? 286 : 287;
		$b    = ( ( $d_33 == 1 or $d_33 == 2 ) and ( $d_33 == $d_4 or $d_4 == 1 ) ) ? 78 : ( ( $d_33 == 3 and $d_4 == 0 ) ? 80 : 79 );
		if ( (int) ( ( $g_y - 10 ) / 63 ) == 30 ) {
			$a --;
			$b ++;
		}
		if ( $doy_g > $b ) {
			$jy    = $g_y - 621;
			$doy_j = $doy_g - $b;
		} else {
			$jy    = $g_y - 622;
			$doy_j = $doy_g + $a;
		}
		if ( $doy_j < 187 ) {
			$jm = (int) ( ( $doy_j - 1 ) / 31 );
			$jd = $doy_j - ( 31 * $jm ++ );
		} else {
			$jm = (int) ( ( $doy_j - 187 ) / 30 );
			$jd = $doy_j - 186 - ( $jm * 30 );
			$jm += 7;
		}

		$jd = $jd > 9 ? $jd : '0' . $jd;
		$jm = $jm > 9 ? $jm : '0' . $jm;

		return ( $mod == '' ) ? array( $jy, $jm, $jd ) : $jy . $mod . $jm . $mod . $jd;
	}

	public function ReplaceShortCodes( $content, $order_status, $order, $vendor_items_array = array() ) {

		$order_id = $this->OrderId( $order );
		$price    = strip_tags( $this->OrderProp( $order, 'formatted_order_total', array( '', false ) ) );
		$price    = html_entity_decode( $price );

		$all_product_list = $this->AllItems( $order );
		$all_product_ids  = ! empty( $all_product_list['product_ids'] ) ? $all_product_list['product_ids'] : array();
		$all_items        = ! empty( $all_product_list['items'] ) ? $all_product_list['items'] : array();
		$all_items_qty    = ! empty( $all_product_list['items_qty'] ) ? $all_product_list['items_qty'] : array();

		$vendor_product_ids = ! empty( $vendor_items_array['product_ids'] ) ? $vendor_items_array['product_ids'] : array();
		$vendor_items       = ! empty( $vendor_items_array['items'] ) ? $vendor_items_array['items'] : array();
		$vendor_items_qty   = ! empty( $vendor_items_array['items_qty'] ) ? $vendor_items_array['items_qty'] : array();
		$vendor_price       = ! empty( $vendor_items_array['price'] ) ? array_sum( (array) $vendor_items_array['price'] ) : 0;
		$vendor_price       = strip_tags( wc_price( $vendor_price ) );

		$payment_gateways = array();
		if ( WC()->payment_gateways() ) {
			$payment_gateways = WC()->payment_gateways->payment_gateways();
		}

		$payment_method  = $this->OrderProp( $order, 'payment_method' );
		$payment_method  = ( isset( $payment_gateways[ $payment_method ] ) ? esc_html( $payment_gateways[ $payment_method ]->get_title() ) : esc_html( $payment_method ) );
		$shipping_method = esc_html( $this->OrderProp( $order, 'shipping_method' ) );

		$country = WC()->countries;

		$bill_country = ( isset( $country->countries[ $this->OrderProp( $order, 'billing_country' ) ] ) ) ? $country->countries[ $this->OrderProp( $order, 'billing_country' ) ] : $this->OrderProp( $order, 'billing_country' );
		$bill_state   = ( $this->OrderProp( $order, 'billing_country' ) && $this->OrderProp( $order, 'billing_state' ) && isset( $country->states[ $this->OrderProp( $order, 'billing_country' ) ][ $this->OrderProp( $order, 'billing_state' ) ] ) ) ? $country->states[ $this->OrderProp( $order, 'billing_country' ) ][ $this->OrderProp( $order, 'billing_state' ) ] : $this->OrderProp( $order, 'billing_state' );

		$shipp_country = ( isset( $country->countries[ $this->OrderProp( $order, 'shipping_country' ) ] ) ) ? $country->countries[ $this->OrderProp( $order, 'shipping_country' ) ] : $this->OrderProp( $order, 'shipping_country' );
		$shipp_state   = ( $this->OrderProp( $order, 'shipping_country' ) && $this->OrderProp( $order, 'shipping_state' ) && isset( $country->states[ $this->OrderProp( $order, 'shipping_country' ) ][ $this->OrderProp( $order, 'shipping_state' ) ] ) ) ? $country->states[ $this->OrderProp( $order, 'shipping_country' ) ][ $this->OrderProp( $order, 'shipping_state' ) ] : $this->OrderProp( $order, 'shipping_state' );

		$post = get_post( $order_id );

		$tags = array(
			'{b_first_name}'  => $this->OrderProp( $order, 'billing_first_name' ),
			'{b_last_name}'   => $this->OrderProp( $order, 'billing_last_name' ),
			'{b_company}'     => $this->OrderProp( $order, 'billing_company' ),
			'{b_address_1}'   => $this->OrderProp( $order, 'billing_address_1' ),
			'{b_address_2}'   => $this->OrderProp( $order, 'billing_address_2' ),
			'{b_state}'       => $bill_state,
			'{b_city}'        => $this->OrderProp( $order, 'billing_city' ),
			'{b_postcode}'    => $this->OrderProp( $order, 'billing_postcode' ),
			'{b_country}'     => $bill_country,
			'{sh_first_name}' => $this->OrderProp( $order, 'shipping_first_name' ),
			'{sh_last_name}'  => $this->OrderProp( $order, 'shipping_last_name' ),
			'{sh_company}'    => $this->OrderProp( $order, 'shipping_company' ),
			'{sh_address_1}'  => $this->OrderProp( $order, 'shipping_address_1' ),
			'{sh_address_2}'  => $this->OrderProp( $order, 'shipping_address_2' ),
			'{sh_state}'      => $shipp_state,
			'{sh_city}'       => $this->OrderProp( $order, 'shipping_city' ),
			'{sh_postcode}'   => $this->OrderProp( $order, 'shipping_postcode' ),
			'{sh_country}'    => $shipp_country,
			'{phone}'         => get_post_meta( $order_id, '_billing_phone', true ),
			'{mobile}'        => $this->buyerMobile( $order_id ),
			'{email}'         => $this->OrderProp( $order, 'billing_email' ),
			'{order_id}'      => $this->OrderProp( $order, 'order_number' ),
			'{date}'          => $this->OrderDate( $order ),
			'{post_id}'       => $order_id,
			'{status}'        => $this->statusName( $order_status, true ),
			'{price}'         => $price,

			'{all_items}'     => implode( ' - ', $all_items ),
			'{all_items_qty}' => implode( ' - ', $all_items_qty ),
			'{count_items}'   => count( $all_items ),

			'{vendor_items}'       => implode( ' - ', $vendor_items ),
			'{vendor_items_qty}'   => implode( ' - ', $vendor_items_qty ),
			'{count_vendor_items}' => count( $vendor_items ),
			'{vendor_price}'       => $vendor_price,

			'{transaction_id}'  => get_post_meta( $order_id, '_transaction_id', true ),
			'{payment_method}'  => $payment_method,
			'{shipping_method}' => $shipping_method,
			'{description}'     => nl2br( esc_html( $post->post_excerpt ) ),
		);

		$content = apply_filters( 'pwoosms_order_sms_body_before_replace', $content, array_keys( $tags ), array_values( $tags ), $order_id, $order, $all_product_ids, $vendor_product_ids );

		$content = str_ireplace( array_keys( $tags ), array_values( $tags ), $content );
		$content = str_ireplace( array( '<br>', '<br/>', '<br />', '&nbsp;' ), array( '', '', '', ' ' ), $content );

		$content = apply_filters( 'pwoosms_order_sms_body_after_replace', $content, $order_id, $order, $all_product_ids, $vendor_product_ids );

		return $content;
	}

	public function buyerMobileMeta() {
		return apply_filters( 'pwoosms_mobile_meta', 'billing_phone' );
	}

	public function buyerMobile( $order_id ) {
		return get_post_meta( $order_id, '_' . $this->buyerMobileMeta(), true );
	}

	public function validateMobile( $mobile ) {

		$mobile = $this->modifyMobile( $mobile );

		return preg_match( '/9\d{9,}?$/', trim( $mobile ) );
		//return is_numeric( $mobile );
	}

	public function modifyMobile( $mobile ) {

		if ( is_array( $mobile ) ) {
			return array_map( array( $this, __FUNCTION__ ), $mobile );
		}

		$mobile = $this->EnglishNumberMobile( $mobile );

		$modified = preg_replace( '/\D/is', '', (string) $mobile );

		if ( substr( $mobile, 0, 1 ) == '+' ) {
			return '+' . $modified;
		} elseif ( substr( $modified, 0, 2 ) == '00' ) {
			return '+' . substr( $modified, 2 );
		} elseif ( substr( $modified, 0, 1 ) == '0' ) {
			return $modified;
		} elseif ( ! empty( $modified ) ) {
			$modified = '0' . $modified;
		}

		return $modified;
	}

	public function EnglishNumberMobile( $mobile ) {
		if ( is_array( $mobile ) ) {
			return array_map( array( $this, __FUNCTION__ ), $mobile );
		} else {

			$mobile = sanitize_text_field( $mobile );

			$mobile = str_ireplace( array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ),
				array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), $mobile ); //farsi
			$mobile = str_ireplace( array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ),
				array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), $mobile ); //arabi

			return $mobile;
		}
	}

	public function sanitize_text_field( $post ) {
		if ( is_array( $post ) ) {
			return array_map( array( $this, __FUNCTION__ ), $post );
		}

		return sanitize_text_field( $post );
	}

	public function hasNotifCond( $key, $product_id ) {
		return $this->Options( 'enable_notif_sms_main' ) && $this->maybeBool( $this->getValue( $key, $product_id ) );
	}

	public function getValue( $key, $product_id ) {

		//مقدار واقعی

		$key = ltrim( $key, '_' );

		$sms_set = get_post_meta( $product_id, '_is_sms_set', true );

		if ( ( is_string( $sms_set ) && $this->maybeBool( $sms_set ) ) || ( is_array( $sms_set ) && in_array( $key, $sms_set ) ) ) {
			return get_post_meta( $product_id, '_' . $key, true );
		}

		return $this->Options( $key, '__' );
	}

	public function maybeBool( $value ) {

		if ( empty( $value ) ) {
			return false;
		}

		if ( is_string( $value ) ) {

			if ( in_array( $value, array( 'on', 'true', 'yes' ) ) ) {
				return true;
			}

			if ( in_array( $value, array( 'off', 'false', 'no' ) ) ) {
				return false;
			}
		}

		return $value;
	}

	public function ReplaceTags( $key, $product_id, $parent_product_id ) {

		$sale_price_dates_from = ( $date = $this->ProductSalePriceTime( $product_id, 'from' ) ) ? date_i18n( 'Y-m-d', $date ) : '';
		$sale_price_dates_to   = ( $date = $this->ProductSalePriceTime( $product_id, 'to' ) ) ? date_i18n( 'Y-m-d', $date ) : '';

		$product = wc_get_product( $product_id );

		$sku = $this->ProductProp( $product, 'sku' );
		if ( empty( $sku ) ) {
			$sku = $this->ProductProp( $parent_product_id, 'sku' );
		}

		$tags = array(
			'{product_id}'    => $parent_product_id,
			'{sku}'           => $sku,
			'{product_title}' => $this->MaybeVariableProductTitle( $product ),
			'{regular_price}' => strip_tags( wc_price( $this->ProductProp( $product, 'regular_price' ) ) ),
			'{onsale_price}'  => strip_tags( wc_price( $this->ProductProp( $product, 'sale_price' ) ) ),
			'{onsale_from}'   => $this->mayBeJalaliDate( $sale_price_dates_from ),
			'{onsale_to}'     => $this->mayBeJalaliDate( $sale_price_dates_to ),
			'{stock}'         => $this->ProductStockQty( $product ),
		);

		$content = $this->getValue( $key, $parent_product_id );

		return str_replace( array( '<br>', '<br>', '<br />', '&nbsp;' ),
			array( '', '', '', ' ' ),
			str_replace( array_keys( $tags ), array_values( $tags ), $content ) );
	}


	public function ProductId( $product = '' ) {

		if ( empty( $product ) ) {
			$product_id = get_the_ID();
		} else if ( is_numeric( $product ) ) {
			$product_id = $product;
		} else if ( is_object( $product ) ) {
			$product_id = $this->ProductProp( $product, 'id' );
		} else {
			$product_id = false;
		}

		return $product_id;
	}

	public function ProductProp( $product, $prop ) {
		$method = 'get_' . $prop;

		return method_exists( $product, $method ) ? $product->$method() : ( ! empty( $product->{$prop} ) ? $product->{$prop} : '' );
	}

	public function IsStockManaging( $product ) {

		if ( 'yes' !== get_option( 'woocommerce_manage_stock' ) ) {
			return false;
		}

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( method_exists( $product, 'get_manage_stock' ) ) {
			$manage = $product->get_manage_stock();
		} elseif ( method_exists( $product, 'managing_stock' ) ) {
			$manage = $product->managing_stock();
		} else {
			$manage = true;
		}

		if ( strtolower( $manage ) == 'parent' ) {
			$manage = false;
		}

		return $manage;
	}

	public function ProductStockQty( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( method_exists( $product, 'get_stock_quantity' ) ) {
			$quantity = $product->get_stock_quantity();
		} else {
			$quantity = $this->ProductProp( $product, 'total_stock' );
		}

		if ( empty( $quantity ) ) {
			$quantity = ( (int) get_post_meta( $this->ProductId( $product ), '_stock', true ) );
		}

		return ! empty( $quantity ) ? $quantity : 0;
	}

	public function ProductAdminMobiles( $product_ids, $status = '' ) {

		$product_ids = array_unique( (array) $product_ids );

		$mobiles = array();
		foreach ( $product_ids as $product_id ) {

			$product_admin   = (array) get_post_meta( $product_id, '_pwoosms_product_admin_data', true );
			$product_admin[] = $this->User_Meta_Mobile( $product_id );
			$product_admin[] = $this->Post_Meta_Mobile( $product_id );
			$product_admin   = array_filter( $product_admin );

			foreach ( (array) $product_admin as $data ) {

				if ( ! empty( $data['mobile'] ) && ! empty( $data['statuses'] ) && $this->validateMobile( $data['mobile'] ) ) {

					$statuses = $this->prepareAdminProductStatus( $data['statuses'] );

					if ( empty( $status ) || in_array( $status, $statuses ) ) {
						$_mobiles = array_map( 'trim', explode( ',', $data['mobile'] ) );
						foreach ( $_mobiles as $_mobile ) {
							$mobiles[ $_mobile ][] = $product_id;
						}
					}
				}
			}
		}

		return $mobiles;
	}

	public function GetProdcutLists( $order, $field = '' ) {

		$products = array();
		$fields   = array();

		foreach ( (array) $this->OrderProp( $order, 'items' ) as $product ) {

			$parent_product_id = ! empty( $product['product_id'] ) ? $product['product_id'] : $this->ProductId( $product );
			$product_id        = $this->ProductProp( $product, 'variation_id' );
			$product_id        = ! empty( $product_id ) ? $product_id : $parent_product_id;

			$item = array(
				'id'         => $product_id,
				'product_id' => $parent_product_id,
				'qty'        => ! empty( $product['qty'] ) ? $product['qty'] : 0,
				'total'      => ! empty( $product['total'] ) ? $product['total'] : 0,
			);

			if ( ! empty( $field ) && isset( $item[ $field ] ) ) {
				$fields[] = $item[ $field ];
			}

			$products[ $parent_product_id ][] = $item;
		}

		if ( ! empty( $field ) ) {
			$products[ $field ] = $fields;
		}

		return $products;
	}

	public function prepareItems( &$items, $item_data ) {

		if ( ! empty( $item_data['id'] ) ) {
			$title                = $this->MaybeVariableProductTitle( $item_data['id'] );
			$items['items'][]     = $title;
			$items['items_qty'][] = $title . ' (' . $item_data['qty'] . ')';
			$items['price'][]     = $item_data['total'];
		}
	}

	public function AllItems( $order ) {

		$order_products = $this->GetProdcutLists( $order );

		$items = array();
		foreach ( (array) $order_products as $item_datas ) {
			foreach ( (array) $item_datas as $item_data ) {
				$this->prepareItems( $items, $item_data );
			}
		}

		$items['product_ids'] = array_keys( $order_products );

		return $items;
	}

	public function ProductAdminItems( $order_products, $product_ids ) {

		$product_ids = array_unique( $product_ids );

		$items = array();
		foreach ( $product_ids as $product_id ) {
			$item_datas = $order_products[ $product_id ];
			foreach ( (array) $item_datas as $item_data ) {
				$this->prepareItems( $items, $item_data );
			}
		}

		$items['product_ids'] = $product_ids;

		return $items;
	}

	public function OrderProp( $order, $prop, $args = array() ) {
		$method = 'get_' . $prop;

		if ( method_exists( $order, $method ) ) {
			if ( empty( $args ) || ! is_array( $args ) ) {
				return $order->$method();
			} else {
				return call_user_func_array( array( $order, $method ), $args );
			}
		}

		return ! empty( $order->{$prop} ) ? $order->{$prop} : '';
	}

	public function OrderId( $order ) {
		return $this->OrderProp( $order, 'id' );
	}

	public function OrderDate( $order ) {

		$order_date = $this->OrderProp( $order, 'date_paid' );
		if ( empty( $order_date ) ) {
			$order_date = $this->OrderProp( $order, 'date_created' );
		}
		if ( empty( $order_date ) ) {
			$order_date = $this->OrderProp( $order, 'date_modified' );
		}
		if ( ! empty( $order_date ) ) {
			if ( method_exists( $order_date, 'getOffsetTimestamp' ) ) {
				$order_date = gmdate( 'Y-m-d H:i:s', $order_date->getOffsetTimestamp() );
			}
		} else {
			$order_date = date_i18n( 'Y-m-d H:i:s' );
		}

		return $this->mayBeJalaliDate( $order_date );
	}

	public function orderNoteMetaBox( $post_id = 0 ) {

		if ( ! class_exists( 'WC_Meta_Box_Order_Notes' ) ) {
			return '';
		}

		if ( ! method_exists( 'WC_Meta_Box_Order_Notes', 'output' ) ) {
			return '';
		}

		global $post;
		if ( empty( $post ) || ! is_object( $post ) ) {
			$post = get_post( $post_id );
		}

		ob_start();
		WC_Meta_Box_Order_Notes::output( $post );

		return ob_get_clean();
	}

	public function SendSMS( $data ) {

		$message = ! empty( $data['message'] ) ? esc_textarea( $data['message'] ) : '';

		$mobile = ! empty( $data['mobile'] ) ? $data['mobile'] : '';
		if ( ! is_array( $mobile ) ) {
			$mobile = explode( ',', $mobile );
		}

		$mobile = $this->modifyMobile( $mobile );

		$mobile = explode( ',', implode( ',', (array) $mobile ) );//حتما یه خیریتی داشته
		$mobile = array_map( 'trim', $mobile );
		$mobile = array_unique( array_filter( $mobile ) );

		$gateway_method = $this->Options( 'sms_gateway' );
		$gateway_method = $gateway_method == 'none' ? '' : $gateway_method;

		$gateway_object = WoocommerceIR_SMS_Gateways::init();

		if ( empty( $mobile ) ) {
			$result = 'شماره موبایل خالی است.';
		} elseif ( empty( $message ) ) {
			$result = 'متن پیامک خالی است.';
		} elseif ( empty( $gateway_method ) ) {
			$result = 'تنظیمات درگاه پیامک انجام نشده است.';
		} elseif ( ! method_exists( $gateway_object, $gateway_method ) ) {
			$result = 'تابع درگاه پیامکی شما داخل کلاس درگاه های پیامکی وجود ندارد.';
		} else {

			try {

				$gateway_object->mobile  = $mobile;
				$gateway_object->message = $message;

				$result = $gateway_object->$gateway_method( $data );
			} catch ( SoapFault $e ) {
				$result = $e->getMessage();
			} catch ( Exception $e ) {
				$result = $e->getMessage();
			}
		}

		if ( $result !== true && ! is_string( $result ) ) {
			ob_start();
			var_dump( $result );
			$result = ob_get_clean();
		}

		if ( ! empty( $mobile ) && ! empty( $message ) ) {

			$gateways = WoocommerceIR_SMS_Gateways::get_sms_gateway();
			$sender   = ! empty( $gateway_method ) ? '(' . $gateway_object->senderNumber . ') ' . $gateways[ $gateway_method ] : '';

			WoocommerceIR_SMS_Archive::insertRecord( array(
				'post_id'  => ! empty( $data['post_id'] ) ? $data['post_id'] : '',
				'type'     => ! empty( $data['type'] ) ? $data['type'] : 0,
				'reciever' => implode( ',', (array) $mobile ),
				'message'  => $message,
				'sender'   => $sender,
				'result'   => $result === true ? '_ok_' : $result
			) );
		}

		return $result;
	}


	public function nusoap() {
		if ( ! class_exists( 'nusoap_client' ) ) {
			require_once PWOOSMS_INCLUDE_DIR . '/lib/nusoap.php';
		}
	}
}

function PWooSMS() {
	global $PWooSMS;

	return ( $PWooSMS = WoocommerceIR_SMS_Helper::init() );
}

function PWooSMS_Shortcode( $get = false, $strip_brac = false ) {

	$shortcode = 'woo_ps_sms';

	if ( $get ) {
		if ( $strip_brac ) {
			return $shortcode;
		}

		return "[$shortcode]";
	}

	echo do_shortcode( "[$shortcode]" );
}