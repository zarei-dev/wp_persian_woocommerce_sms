<?php

defined( 'ABSPATH' ) || exit;

class WoocommerceIR_SMS_Orders {
	public $SafePWOOCSS = array( 'style' => array() );
	private $enabled_buyers = false;
	private $enable_super_admin_sms = false;
	private $enable_product_admin_sms = false;

	public function __construct() {

		$this->enabled_buyers           = PWooSMS()->Options( 'enable_buyer' );
		$this->enable_super_admin_sms   = PWooSMS()->Options( 'enable_super_admin_sms' );
		$this->enable_product_admin_sms = PWooSMS()->Options( 'enable_product_admin_sms' );

		if ( $this->enabled_buyers || $this->enable_super_admin_sms || $this->enable_product_admin_sms ) {

			add_filter( 'woocommerce_checkout_fields', [ $this, 'mobileLabel' ], 0 );
			add_filter( 'woocommerce_billing_fields', [ $this, 'mobileLabel' ] );

			add_action( 'wp_enqueue_scripts', [ $this, 'checkoutScript' ] );
			add_action( 'woocommerce_after_order_notes', [ $this, 'checkoutFields' ] );
			add_action( 'woocommerce_checkout_process', [ $this, 'checkoutFieldsValidation' ] );
			add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'saveSmsOrderMeta' ] );

			/*بعد از تغییر وضعیت سفارش*/
			add_action( 'woocommerce_order_status_changed', [ $this, 'sendOrderSms' ], 99, 3 );

			/*بعد از ثبت سفارش*/
			add_action( 'woocommerce_checkout_order_processed', [ $this, 'sendOrderSms' ], 99, 1 );
			add_action( 'woocommerce_process_shop_order_meta', [ $this, 'sendOrderSms' ], 999, 1 );

			/*جلوگیری از ارسال بعد از ثبت مجدد سفارش از صفحه تسویه حساب*/
			add_action( 'woocommerce_resume_order', function () {
				remove_action( 'woocommerce_checkout_order_processed', [ $this, 'sendOrderSms' ], 99 );
			} );

			add_filter( 'woocommerce_form_field_pwoosms_multiselect', [
				'WoocommerceIR_SMS_Helper',
				'multiSelectAndCheckbox',
			], 11, 4 );
			add_filter( 'woocommerce_form_field_pwoosms_multicheckbox', [
				'WoocommerceIR_SMS_Helper',
				'multiSelectAndCheckbox',
			], 11, 4 );

			if ( is_admin() ) {
				add_action( 'woocommerce_admin_order_data_after_billing_address', [
					$this,
					'buyerSmsDetails',
				], 10, 1 );
				add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'changeSmsTextJS' ] );
				add_action( 'wp_ajax_change_sms_text', [ $this, 'changeSmsTextCallback' ] );
				//add_action( 'wp_ajax_nopriv_change_sms_text', array( $this, 'changeSmsTextCallback' ) );
			}
		}
	}

	public function mobileLabel( $fields ) {

		$mobile_meta = PWooSMS()->buyerMobileMeta();

		if ( ! empty( $fields[ $mobile_meta ]['label'] ) ) {
			$fields[ $mobile_meta ]['label'] = PWooSMS()->Options( 'buyer_phone_label', $fields[ $mobile_meta ]['label'] );
		}

		if ( ! empty( $fields['billing'][ $mobile_meta ]['label'] ) ) {
			$fields['billing'][ $mobile_meta ]['label'] = PWooSMS()->Options( 'buyer_phone_label', $fields['billing'][ $mobile_meta ]['label'] );
		}

		return $fields;
	}

	public function checkoutScript() {

		if ( ! function_exists( 'is_checkout' ) || ! function_exists( 'wc_enqueue_js' ) ) {
			return;
		}

		if ( PWooSMS()->Options( 'allow_buyer_select_status' ) && is_checkout() ) {

			wp_register_script( 'pwoosms-frontend-js', PWOOSMS_URL . '/assets/js/multi-select.js', [ 'jquery' ], PWOOSMS_VERSION, true );
			wp_localize_script( 'pwoosms-frontend-js', 'pwoosms',
				[
					'ajax_url'                  => admin_url( 'admin-ajax.php' ),
					'chosen_placeholder_single' => 'گزینه مورد نظر را انتخاب نمایید.',
					'chosen_placeholder_multi'  => 'گزینه های مورد نظر را انتخاب نمایید.',
					'chosen_no_results_text'    => 'هیچ گزینه ای وجود ندارد.',
				]
			);
			wp_enqueue_script( 'pwoosms-frontend-js' );

			if ( ! PWooSMS()->Options( 'force_enable_buyer' ) ) {
				wc_enqueue_js( "
					jQuery( '#buyer_sms_status_field' ).hide();
					jQuery( 'input[name=buyer_sms_notify]' ).change( function () {
						if ( jQuery( this ).is( ':checked' ) )
							jQuery( '#buyer_sms_status_field' ).show();
						else
							jQuery( '#buyer_sms_status_field' ).hide();
					} ).change();
				" );
			}
		}
	}

	public function checkoutFields( $checkout ) {

		if ( ! $this->enabled_buyers || count( PWooSMS()->GetBuyerAllowedStatuses() ) < 0 ) {
			return;
		}

		echo '<div id="checkoutFields">';

		$checkbox_text = PWooSMS()->Options( 'buyer_checkbox_text', 'میخواهم از وضعیت سفارش از طریق پیامک آگاه شوم.' );
		$required      = PWooSMS()->Options( 'force_enable_buyer' );
		if ( ! $required ) {
			woocommerce_form_field( 'buyer_sms_notify',
				[
					'type'        => 'checkbox',
					'class'       => [ 'buyer-sms-notify form-row-wide' ],
					'label'       => $checkbox_text,
					'label_class' => '',
					'required'    => false,
				], $checkout->get_value( 'buyer_sms_notify' )
			);
		}

		if ( PWooSMS()->Options( 'allow_buyer_select_status' ) ) {
			$multiselect_text        = PWooSMS()->Options( 'buyer_select_status_text_top' );
			$multiselect_text_bellow = PWooSMS()->Options( 'buyer_select_status_text_bellow' );
			$required                = PWooSMS()->Options( 'force_buyer_select_status' );
			$mode                    = PWooSMS()->Options( 'buyer_status_mode', 'selector' ) == 'selector' ? 'pwoosms_multiselect' : 'pwoosms_multicheckbox';
			woocommerce_form_field( 'buyer_sms_status', [
				'type'        => $mode ? $mode : '',
				'class'       => [ 'buyer-sms-status form-row-wide wc-enhanced-select' ],
				'label'       => $multiselect_text,
				'options'     => PWooSMS()->GetBuyerAllowedStatuses( true ),
				'required'    => $required,
				'description' => $multiselect_text_bellow,
			], $checkout->get_value( 'buyer_sms_status' ) );
		}

		echo '</div>';
	}

	public function checkoutFieldsValidation() {

		$mobile_meta = PWooSMS()->buyerMobileMeta();

		$_POST[ $mobile_meta ] = PWooSMS()->modifyMobile( sanitize_text_field( $_POST[ $mobile_meta ] ?? null ) );

		if ( ! $this->enabled_buyers || count( PWooSMS()->GetBuyerAllowedStatuses() ) < 0 ) {
			return;
		}

		$force_buyer = PWooSMS()->Options( 'force_enable_buyer' );

		if ( ! $force_buyer && ! empty( $_POST['buyer_sms_notify'] ) && empty( $_POST[ $mobile_meta ] ) ) {
			wc_add_notice( 'برای دریافت پیامک می بایست شماره موبایل را وارد نمایید.', 'error' );
		}

		$buyer_selected = $force_buyer || ( ! $force_buyer && ! empty( $_POST['buyer_sms_notify'] ) );

		if ( $buyer_selected && ! PWooSMS()->validateMobile( $_POST[ $mobile_meta ] ?? null ) ) {
			wc_add_notice( 'شماره موبایل معتبر نیست.', 'error' );
		}

		if ( $buyer_selected && empty( $_POST['buyer_sms_status'] ) && PWooSMS()->Options( 'allow_buyer_select_status' ) && PWooSMS()->Options( 'force_buyer_select_status' ) ) {
			wc_add_notice( 'انتخاب حداقل یکی از وضعیت های سفارش دریافت پیامک الزامی است.', 'error' );
		}
	}

	public function saveSmsOrderMeta( $order_id ) {

		if ( ! $this->enabled_buyers || count( PWooSMS()->GetBuyerAllowedStatuses() ) <= 0 ) {
			return;
		}

		update_post_meta( $order_id, '_force_enable_buyer', PWooSMS()->Options( 'force_enable_buyer', '__' ) );
		update_post_meta( $order_id, '_allow_buyer_select_status', PWooSMS()->Options( 'allow_buyer_select_status', '__' ) );

		if ( ! empty( $_POST['buyer_sms_notify'] ) || PWooSMS()->Options( 'force_enable_buyer' ) ) {
			update_post_meta( $order_id, '_buyer_sms_notify', 'yes' );
		} else {
			delete_post_meta( $order_id, '_buyer_sms_notify' );
		}

		if ( ! empty( $_POST['buyer_sms_status'] ) ) {
			$statuses = is_array( $_POST['buyer_sms_status'] ) ? array_map( 'sanitize_text_field', $_POST['buyer_sms_status'] ) : sanitize_text_field( $_POST['buyer_sms_status'] );
			update_post_meta( $order_id, '_buyer_sms_status', $statuses );
		} else {
			delete_post_meta( $order_id, '_buyer_sms_status' );
		}
	}

	public function buyerSmsDetails( WC_Order $order ) {

		if ( ! $this->enabled_buyers || count( PWooSMS()->GetBuyerAllowedStatuses() ) < 0 ) {
			return;
		}

		$mobile = PWooSMS()->buyerMobile( $order->get_id() );

		if ( empty( $mobile ) ) {
			return;
		}

		if ( ! PWooSMS()->validateMobile( $mobile ) ) {
			echo '<p>شماره موبایل مشتری معتبر نیست.</p>';

			return;
		}

		if ( PWooSMS()->maybeBool( get_post_meta( $order->get_id(), '_force_enable_buyer', true ) ) ) {
			echo '<p>مشتری حق انتخاب دریافت یا عدم دریافت پیامک را ندارد.</p>';
		} else {
			$want_sms = get_post_meta( $order->get_id(), '_buyer_sms_notify', true );
			echo '<p>آیا مشتری مایل به دریافت پیامک هست : ' . ( PWooSMS()->maybeBool( $want_sms ) ? 'بله' : 'خیر' ) . '</p>';
		}

		echo '<p>';
		if ( PWooSMS()->maybeBool( get_post_meta( $order->get_id(), '_allow_buyer_select_status', true ) ) ) {

			$buyer_sms_status = (array) get_post_meta( $order->get_id(), '_buyer_sms_status', true );
			$buyer_sms_status = array_filter( $buyer_sms_status );

			echo 'وضعیت های انتخابی توسط مشتری برای دریافت پیامک : ';
			if ( ! empty( $buyer_sms_status ) ) {
				$statuses = [];
				foreach ( $buyer_sms_status as $status ) {
					$statuses[] = PWooSMS()->statusName( $status );
				}

				echo esc_html( implode( ' - ', $statuses ) );
			} else {
				echo 'وضعیتی انتخاب نشده است.';
			}

		} else {
			echo 'مشتری حق انتخاب وضعیت های دریافت پیامک را ندارد و از تنظیمات افزونه پیروی میکند.';
			/*
			 //* زیاد شلوغ میشه بیخیال.
			$allowed_status = PWooSMS()->GetBuyerAllowedStatuses();
			if ( ! empty( $allowed_status ) ) {
				echo ' وضعیت مجاز برای دریافت پیامک با توجه به تنظیمات: ' . '<br>';
				echo esc_html( implode( ' - ', array_values( $allowed_status ) ) );
			}
			*/
		}
		echo '</p>';
	}

	public function sendOrderSms( int $order_id, $old_status = '', $new_status = 'created' ) {

		if ( current_action() == 'woocommerce_process_shop_order_meta' ) {
			if ( ! is_admin() ) {
				return;
			}
		} else {
			remove_action( 'woocommerce_process_shop_order_meta', [ $this, 'sendOrderSms' ], 999 );
		}

		$new_status = PWooSMS()->modifyStatus( $new_status );

		if ( ! $order_id ) {
			return;
		}

		$order = new WC_Order( $order_id );

		// Customer
		$order_page = ( $_POST['is_shop_order'] ?? null ) == 'true';

		if ( ( $order_page && ! empty( $_POST['sms_order_send'] ) ) || ( ! $order_page && $this->buyerCanGetSMS( $order_id, $new_status ) ) ) {

			$mobile  = PWooSMS()->buyerMobile( $order_id );
			$message = isset( $_POST['sms_order_text'] ) ? sanitize_textarea_field( $_POST['sms_order_text'] ) : PWooSMS()->Options( 'sms_body_' . $new_status );

			$data = [
				'post_id' => $order_id,
				'type'    => 2,
				'mobile'  => $mobile,
				'message' => PWooSMS()->ReplaceShortCodes( $message, $new_status, $order ),
			];

			if ( ( $result = PWooSMS()->SendSMS( $data ) ) === true ) {
				$order->add_order_note( sprintf( 'پیامک با موفقیت به مشتری با شماره %s ارسال گردید.', $mobile ) );
			} else {
				$order->add_order_note( sprintf( 'پیامک بخاطر خطا به مشتری با شماره %s ارسال نشد.<br>پاسخ وبسرویس: %s', $mobile, $result ) );
			}
		}


		//superAdmin
		if ( $this->enable_super_admin_sms && in_array( $new_status, (array) PWooSMS()->Options( 'super_admin_order_status' ) ) ) {

			$mobile  = PWooSMS()->Options( 'super_admin_phone' );
			$message = PWooSMS()->Options( 'super_admin_sms_body_' . $new_status );

			$data = [
				'post_id' => $order_id,
				'type'    => 4,
				'mobile'  => $mobile,
				'message' => PWooSMS()->ReplaceShortCodes( $message, $new_status, $order ),
			];

			if ( ( $result = PWooSMS()->SendSMS( $data ) ) === true ) {
				$order->add_order_note( sprintf( 'پیامک با موفقیت به مدیر کل با شماره %s ارسال گردید.', $mobile ) );
			} else {
				$order->add_order_note( sprintf( 'پیامک بخاطر خطا به مدیر کل با شماره %s ارسال نشد.<br>پاسخ وبسرویس: %s', $mobile, $result ) );
			}
		}

		//productAdmin
		if ( $this->enable_product_admin_sms ) {

			$order_products = PWooSMS()->GetProdcutLists( $order, 'product_id' );
			$mobiles        = PWooSMS()->ProductAdminMobiles( $order_products['product_id'], $new_status );

			foreach ( (array) $mobiles as $mobile => $product_ids ) {

				$vendor_items = PWooSMS()->ProductAdminItems( $order_products, $product_ids );
				$message      = PWooSMS()->Options( 'product_admin_sms_body_' . $new_status );

				$data = [
					'post_id' => $order_id,
					'type'    => 5,
					'mobile'  => $mobile,
					'message' => PWooSMS()->ReplaceShortCodes( $message, $new_status, $order, $vendor_items ),
				];

				if ( ( $result = PWooSMS()->SendSMS( $data ) ) === true ) {
					$order->add_order_note( sprintf( 'پیامک با موفقیت به مدیر محصول با شماره %s ارسال گردید.', $mobile ) );
				} else {
					$order->add_order_note( sprintf( 'پیامک بخاطر خطا به مدیر محصول با شماره %s ارسال نشد.<br>پاسخ وبسرویس: %s', $mobile, $result ) );
				}
			}
		}
	}

	public function buyerCanGetSMS( int $order_id, string $new_status ): bool {

		if ( ! $this->enabled_buyers ) {
			return false;
		}

		if ( ! $order_id ) {
			return false;
		}

		$order = new WC_Order( $order_id );

		$allowed_status = array_keys( PWooSMS()->GetBuyerAllowedStatuses() );

		if ( is_admin() ) {
			$status      = PWooSMS()->OrderProp( $order, 'status' );
			$created_via = PWooSMS()->OrderProp( $order, 'created_via' );
			if ( $created_via == 'admin' || ! in_array( $status, array_keys( PWooSMS()->GetAllStatuses() ) ) ) {
				update_post_meta( $order_id, '_force_enable_buyer', PWooSMS()->Options( 'force_enable_buyer', '__' ) );
				update_post_meta( $order_id, '_allow_buyer_select_status', PWooSMS()->Options( 'allow_buyer_select_status', '__' ) );
				update_post_meta( $order_id, '_buyer_sms_notify', 'yes' );
				update_post_meta( $order_id, '_buyer_sms_status', $allowed_status );
			}
		}


		if ( ! PWooSMS()->validateMobile( PWooSMS()->buyerMobile( $order_id ) ) ) {
			return false;
		}

		if ( in_array( $new_status, $allowed_status ) && PWooSMS()->maybeBool( get_post_meta( $order_id, '_buyer_sms_notify', true ) ) ) {

			$buyer_sms_status    = (array) get_post_meta( $order_id, '_buyer_sms_status', true );
			$allow_select_status = PWooSMS()->maybeBool( get_post_meta( $order_id, '_allow_buyer_select_status', true ) );

			if ( ! $allow_select_status || ( $allow_select_status && in_array( $new_status, $buyer_sms_status ) ) ) {
				return true;
			}
		}

		return false;
	}

	public function changeSmsTextJS( WC_Order $order ) {

		if ( $this->enabled_buyers && PWooSMS()->validateMobile( PWooSMS()->buyerMobile( $order->get_id() ) ) ) { ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $("#order_status").change(function () {
                        $("#pwoosms_textbox").html("<img src=\"<?php echo PWOOSMS_URL ?>/assets/images/ajax-loader.gif\" />");

                        $.ajax({
                            url: "<?php echo admin_url( "admin-ajax.php" ) ?>",
                            type: "post",
                            data: {
                                action: "change_sms_text",
                                security: "<?php echo wp_create_nonce( "change-sms-text" ) ?>",
                                order_id: "<?php echo intval( $order->get_id() ); ?>",
                                order_status: $("#order_status").val()
                            },
                            success: function (response) {
                                $("#pwoosms_textbox").html(response);
                            }
                        });
                    });
                });
            </script>
            <p class="form-field form-field-wide" id="pwoosms_textbox_p">
                <span id="pwoosms_textbox" class="pwoosms_textbox"></span>
            </p>
			<?php
		}
	}

	public function changeSmsTextCallback() {

		check_ajax_referer( 'change-sms-text', 'security' );

		$order_id = intval( $_POST['order_id'] ?? 0 );

		if ( empty( $order_id ) ) {
			die( 'خطای آیجکس رخ داده است.' );
		}

		$new_status = '';

		if ( isset( $_POST['order_status'] ) ) {
			$_order_status = is_array( $_POST['order_status'] ) ? array_map( 'sanitize_text_field', $_POST['order_status'] ) : sanitize_text_field( $_POST['order_status'] );
			$new_status    = PWooSMS()->modifyStatus( $_order_status );
		}

		$order   = new WC_Order( $order_id );
		$message = PWooSMS()->Options( 'sms_body_' . $new_status );
		$message = PWooSMS()->ReplaceShortCodes( $message, $new_status, $order );

		echo '<textarea id="sms_order_text" name="sms_order_text" style="width:100%;height:120px;"> ' . esc_attr( $message ) . ' </textarea>';
		echo '<input type="hidden" name="is_shop_order" value="true" />';

		if ( $this->buyerCanGetSMS( $order_id, $new_status ) ) {
			$sms_checked = 'checked="checked"';
			$description = 'با توجه به تنظیمات و انتخاب ها، مشتری باید این پیامک را دریافت کند. ولی میتوانید ارسال پیامک به وی را از طریق این چک باکس غیرفعال نمایید.';
		} else {
			$sms_checked = '';
			$description = 'با توجه به تنظیمات و انتخاب ها، مشتری نباید این پیامک را دریافت کند. ولی میتوانید ارسال پیامک به وی را از طریق این چک باکس فعال نمایید.';
		}

		echo '<input type="checkbox" id="sms_order_send" class="sms_order_send" name="sms_order_send" value="true" style="margin-top:2px;width:20px; float:right" ' . wp_kses( $sms_checked, $SafePWOOCSS ) . '/>
					<label class="sms_order_send_label" for="sms_order_send" >ارسال پیامک به مشتری</label>
					<span class="description">' . esc_attr( $description ) . '</span>';

		die();
	}
}

new WoocommerceIR_SMS_Orders();