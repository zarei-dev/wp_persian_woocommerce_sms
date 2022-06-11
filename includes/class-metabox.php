<?php

defined( 'ABSPATH' ) || exit;

class WoocommerceIR_SMS_Metabox {

	private $enable_metabox = false;
	private $enable_notification = false;
	private $enable_product_admin_sms = false;

	public function __construct() {

		if ( ! is_admin() ) {
			return;
		}

		$this->enable_metabox           = PWooSMS()->Options( 'enable_metabox' );//سفارش - مشتری
		$this->enable_notification      = PWooSMS()->Options( 'enable_notif_sms_main' );//خبرنامه
		$this->enable_product_admin_sms = PWooSMS()->Options( 'enable_product_admin_sms' );//مدیر محصول

		if ( $this->enable_metabox || $this->enable_notification || $this->enable_product_admin_sms ) {
			add_action( 'add_meta_boxes', [ $this, 'addMetabox' ] );
			add_action( 'wp_ajax_pwoosms_metabox', [ $this, 'ajaxCallback' ] );
			//add_action( 'wp_ajax_nopriv_pwoosms_metabox', array( $this, 'ajaxCallback' ) );
		}
	}

	public function addMetabox( string $post_type ) {

		if ( $post_type == 'shop_order' && $this->enable_metabox ) {
			add_meta_box( 'send_sms_to_buyer', 'ارسال پیامک به مشتری',
				[ $this, 'orderMetaboxHtml' ], 'shop_order', 'side', 'high' );
		}

		if ( $post_type == 'product' && ( $this->enable_notification || $this->enable_product_admin_sms ) ) {
			add_meta_box( 'send_sms_to_buyer', 'ارسال پیامک به مشترکین این محصول',
				[ $this, 'productMetaboxHtml' ], 'product', 'side', 'high' );
		}
	}

	public function ajaxCallback() {

		check_ajax_referer( 'pwoosms_metabox', 'security' );

		if ( empty( $_POST['post_id'] ) || empty( $_POST['post_type'] ) ) {
			wp_send_json_error( [ 'message' => 'خطای ایجکس رخ داده است.' ] );
		}

		$message = sanitize_text_field( $_POST['message'] ?? '' );

		switch ( $_POST['post_type'] ) {

			case 'shop_order':
				$this->orderMetaboxResult( intval( $_POST['post_id'] ), $message );
				break;

			case 'product':
				$this->productMetaboxResult( intval( $_POST['post_id'] ), $message, sanitize_text_field( $_POST['group'] ?? '' ) );
				break;

			default:
				wp_send_json_error( [ 'message' => 'خطای ایجکس رخ داده است.' ] );
		}
	}

	public function orderMetaboxResult( $order_id, $message ) {

		$order  = new WC_Order( $order_id );
		$mobile = PWooSMS()->buyerMobile( $order_id );

		$data = [
			'post_id' => $order_id,
			'type'    => 3,
			'mobile'  => $mobile,
			'message' => $message,
		];

		if ( ( $result = PWooSMS()->SendSMS( $data ) ) === true ) {

			$order->add_order_note( sprintf( 'پیامک با موفقیت به مشتری با شماره موبایل %s ارسال شد.<br>متن پیامک: %s', $mobile, $message ) );
			wp_send_json_success( [
				'message'    => 'پیامک با موفقیت ارسال شد.',
				'order_note' => PWooSMS()->orderNoteMetaBox( $order_id ),
			] );

		} else {

			$order->add_order_note( sprintf( 'پیامک به مشتری با شماره موبایل %s ارسال نشد.<br>متن پیامک: %s<br>پاسخ وبسرویس: %s', $mobile, $message, $result ) );
			wp_send_json_error( [
				'message'    => sprintf( 'ارسال پیامک با خطا مواجه شد. %s', $result ),
				'order_note' => PWooSMS()->orderNoteMetaBox( $order_id ),
			] );

		}
	}

	/*سفارش*/

	public function productMetaboxResult( int $product_id, string $message, string $group ) {

		$group = '_in';

		if ( empty( $group ) ) {
			wp_send_json_error( [ 'message' => 'یک گروه برای دریافت پیامک انتخاب کنید.' ] );
		}

		if ( $group == '_product_admins' ) {
			$type    = 6;
			$mobiles = array_keys( PWooSMS()->ProductAdminMobiles( $product_id ) );
		} else {

			switch ( $group ) {

				case '_onsale'://حراج
					$type = 10;
					break;

				case '_in'://موجود شدن
					$type = 12;
					break;

				case '_low'://کم بودن موجودی
					$type = 14;
					break;

				default:
					$type = 15;
			}

			$mobiles = WoocommerceIR_SMS_Contacts::getContactsMobiles( $product_id, $group );
		}

		$data = [
			'post_id' => $product_id,
			'type'    => $type,
			'mobile'  => $mobiles,
			'message' => $message,
		];

		if ( ( $result = PWooSMS()->SendSMS( $data ) ) === true ) {
			wp_send_json_success( [ 'message' => sprintf( 'پیامک با موفقیت به %s شماره موبایل ارسال شد.', count( $mobiles ) ) ] );
		} else {
			wp_send_json_error( [ 'message' => sprintf( 'ارسال پیامک با خطا مواجه شد. %s', $result ) ] );
		}
	}

	public function orderMetaboxHtml( $post ) {
		$order_id = $post->ID;
		$mobile   = PWooSMS()->buyerMobile( $order_id );

		if ( empty( $mobile ) ) {
			echo '<p>شماره ای برای ارسال پیامک وجود ندارد.</p>';

			return;
		}

		if ( ! PWooSMS()->validateMobile( $mobile ) ) {
			echo '<p>شماره موبایل مشتری معتبر نیست.</p>';

			return;
		}

		$this->metaBoxHtml( $order_id, 'shop_order', sprintf( '<p>ارسال پیامک به شماره %s</p>', $mobile ) );
	}

	/*محصول*/

	private function metaBoxHtml( int $post_id, $post_type, $html_above = '', $html_below = '' ) { ?>

        <div id="pwoosms_metabox_result"></div>

		<?php

		echo $html_above;

		 ?>

        <p>
            <textarea rows="5" cols="20" class="input-text" id="pwoosms_message"
                      name="pwoosms_message" style="width: 100%; height: 78px;" title=""></textarea>
        </p>

		<?php echo $html_below ; ?>

        <div class="wide" id="pwoosms_divider" style="text-align: left">
            <input type="submit" class="button save_order button-primary" name="pwoosms_submit"
                   id="pwoosms_submit" value="ارسال پیامک">
        </div>

        <div class="pwoosms_loading">
            <img src="<?php echo PWOOSMS_URL . '/assets/images/ajax-loader.gif'; ?>">
        </div>

        <style type="text/css">
            .pwoosms_loading {
                position: absolute;
                background: rgba(255, 255, 255, 0.5);
                top: 0;
                left: 0;
                z-index: 9999;
                display: none;
                width: 100%;
                height: 100%;
            }

            .pwoosms_loading img {
                position: absolute;
                top: 40%;
                left: 47%;
            }

            #pwoosms_metabox_result {
                padding: 6px;
                width: 93%;
                display: none;
                border-radius: 2px;
                border: 1px solid #fff;
            }

            #pwoosms_metabox_result.success {
                color: #155724;
                background-color: #d4edda;
                border-color: #c3e6cb;
            }

            #pwoosms_metabox_result.fault {
                color: #721c24;
                background-color: #f8d7da;
                border-color: #f5c6cb;
            }

            #pwoosms_divider {
                width: 100%;
                border-top: 1px solid #e9e9e9;
                padding-top: 5px;
            }
        </style>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#pwoosms_submit').on('click', function (e) {
                    e.preventDefault();
                    var notes = $('#woocommerce-order-notes .inside');
                    var result = $('div#pwoosms_metabox_result');
                    var loading = $('.pwoosms_loading');
                    loading.show();
                    loading.clone().prependTo(notes);
                    var self = $(this);
                    var post_type = '<?php echo esc_attr( $post_type ); ?>';
                    result.removeClass('fault', 'success');
                    self.attr('disabled', true);
                    $.post('<?php echo admin_url( "admin-ajax.php" );?>', {
                        action: 'pwoosms_metabox',
                        security: '<?php echo wp_create_nonce( 'pwoosms_metabox' );?>',
                        post_id: '<?php echo intval( $post_id );?>',
                        post_type: post_type,
                        message: $('#pwoosms_message').val(),
                        group: $('#select_group').val()
                    }, function (res) {
                        result.addClass(res.success ? 'success' : 'fault').html(res.data.message).show();
                        self.attr('disabled', false);
                        if (typeof res.data.order_note != "undefined" && res.data.order_note.length) {
                            notes.html(res.data.order_note);
                        }
                        loading.hide();
                    });
                });
            });
        </script>
		<?php
	}

	public function productMetaboxHtml( $post ) {

		$product_id = $post->ID;

		ob_start(); ?>
        <p>
            <label for="select_group">ارسال پیامک به:</label><br>
            <select name="select_group" class="wc-enhanced-select" id="select_group" style="width: 100%;">

				<?php if ( $this->enable_product_admin_sms ) { ?>
                    <option value="_product_admins">به مدیران این محصول</option>
				<?php }

				if ( $this->enable_notification ) {

					$groups = WoocommerceIR_SMS_Contacts::getGroups( $product_id, false, true );

					if ( ! empty( $groups ) ) { ?>
                        <optgroup label="به مشترکین گروه های زیر:">
							<?php foreach ( $groups as $code => $text ) { ?>
                                <option value="<?php echo $code; ?>"><?php echo esc_attr( $text ); ?></option>
							<?php } ?>
                        </optgroup>
					<?php }
				}
				?>

            </select>
        </p>
		<?php
		$html_above = ob_get_clean();

		$html_below = '';
		if ( $this->enable_notification ) {
			$contact_url = admin_url( 'admin.php?page=persian-woocommerce-sms-pro&tab=contacts&product_id=' . $product_id );
			$html_below  = '<p><a style="text-decoration: none" href="' . $contact_url . '" target="_blank">مشاهده مشترکین خبرنامه این محصول</a></p>';
		}

		$this->metaBoxHtml( $product_id, 'product', $html_above, $html_below );
	}

}

new WoocommerceIR_SMS_Metabox();