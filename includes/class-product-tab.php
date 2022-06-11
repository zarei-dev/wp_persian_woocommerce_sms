<?php

defined( 'ABSPATH' ) || exit;

class WoocommerceIR_SMS_Product_Tab {

	private $product_metas = [];
	private $enable_notification = false;
	private $enable_product_admin_sms = false;

	public function __construct() {

		add_action( 'init', [ $this, 'updateMeta__3_8' ] );

		if ( ! is_admin() ) {
			return;
		}

		$this->enable_notification      = PWooSMS()->Options( 'enable_notif_sms_main' );
		$this->enable_product_admin_sms = PWooSMS()->Options( 'enable_product_admin_sms' );

		if ( $this->enable_notification || $this->enable_product_admin_sms ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'script' ] );
			add_action( 'woocommerce_product_write_panel_tabs', [ $this, 'tabNav' ] );
			add_action( 'woocommerce_product_data_panels', [ $this, 'tabContent' ] );
			add_action( 'woocommerce_product_write_panels', [ $this, 'tabContent' ] );
			add_action( 'woocommerce_process_product_meta', [ $this, 'updateTabData' ], 10, 1 );
		}
	}

	public function updateMeta__3_8() {

		if ( get_option( 'pwoosms_update_product_admin_meta' ) ) {
			return;
		}

		global $wpdb;
		$update = $wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key=REPLACE(meta_key, '_hannanstd_woo_products_tabs', '_pwoosms_product_admin_data')" );
		if ( $update !== false ) {
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value=REPLACE(meta_value, 's:5:\"title\"', 's:6:\"mobile\"') WHERE meta_key='_pwoosms_product_admin_data'" );
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value=REPLACE(meta_value, 's:7:\"content\"', 's:8:\"statuses\"') WHERE meta_key='_pwoosms_product_admin_data'" );
			update_option( 'pwoosms_update_product_admin_meta', '1' );
		}
	}

	public function script() {
		global $post;
		if ( is_object( $post ) && $post->post_type == 'product' ) {
			wp_register_script( 'repeatable-sms-tabs', PWOOSMS_URL . '/assets/js/product-tab.js', [ 'jquery' ], 'all' );
			wp_enqueue_script( 'repeatable-sms-tabs' );
			wp_register_style( 'repeatable-sms-tabs-styles', PWOOSMS_URL . '/assets/css/product-tab.css', '', 'all' );
			wp_enqueue_style( 'repeatable-sms-tabs-styles' );
		}
	}

	public function tabNav() {
		echo '<li class="pwoosms_tabs"><a href="#pwoosms"><span>پیامک</span></a></li>';
	}

	public function tabContent() {

		global $post;
		$product_id = $post->ID;

		if ( current_action() == 'woocommerce_product_data_panels' ) {
			remove_action( 'woocommerce_product_write_panels', [ $this, __FUNCTION__ ] );
		}
		?>

        <div id="pwoosms" class="panel wc-metaboxes-wrapper woocommerce_options_panel">
			<?php
			$this->notificationSettings( $product_id );
			do_action( 'pwoosms_product_sms_tab', $product_id );
			$this->productAdminSettings( $product_id );
			?>
        </div>
		<?php
	}

	private function notificationSettings( $product_id ) {

		if ( $this->enable_notification ) { ?>

            <div class="pwoosms-tab-product-admin">
                <p><strong>تنظیمات خبرنامه محصول: </strong></p>
            </div>

			<?php
			$this->product_metas[] = 'enable_notif_sms';
			woocommerce_wp_radio( [
				'label'         => 'فرم عضویت در خبرنامه',
				'wrapper_class' => 'pswoosms_tab_radio',
				'id'            => end( $this->product_metas ),
				'value'         => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
				'options'       => [
					'on'        => 'نمایش خودکار در بدنه محصول',
					'thumbnail' => 'نمایش خودکار زیر تصویر شاخص',
					'no'        => sprintf( 'نمایش دستی (راهنمای این گزینه در "تنظیمات افزونه >> خبرنامه محصول >> فرم عضویت در خبرنامه" آمده است.)' ),
				],
			] );

			$this->product_metas[] = 'notif_title';
			woocommerce_wp_text_input( [
				'desc_tip'    => true,
				'label'       => 'متن عضویت در خبرنامه',
				'description' => 'این متن در صفحه محصول به صورت چک باکس ظاهر خواهد شد و کاربر با انتخاب آن میتواند شماره موبایل و گروه های مورد نظر خود را برای عضویت در خبرنامه محصول وارد نماید.',
				'id'          => end( $this->product_metas ),
				'value'       => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );


			$this->product_metas[] = 'notif_only_loggedin';
			woocommerce_wp_checkbox( [
				'cbvalue'     => 'on',
				'desc_tip'    => true,
				'label'       => 'عضویت فقط برای اعضای سایت',
				'description' => 'با فعالسازی این گزینه، فقط کاربران لاگین شده قادر به عضویت در خبرنامه محصول خواهند بود.',
				'id'          => end( $this->product_metas ),
				'value'       => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			$this->product_metas[] = 'notif_only_loggedin_text';
			woocommerce_wp_text_input( [
				'desc_tip'    => true,
				'label'       => 'متن جلوگیری از عضویت مهمانان',
				'description' => 'در صورتی که گزینه "عضویت فقط برای اعضای سایت" را فعال کرده باشید، هنگامیکه کاربران مهمان قصد عضویت در خبرنامه محصول را داشته باشند، با این متن وارد شده مواجه خواهند شد.',
				'id'          => end( $this->product_metas ),
				'value'       => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			echo '<p class="pwoosms-tab-help-toggle" style="cursor: pointer"></span>شورت کد های مورد استفاده در متن پیامک ها<span class="dashicons dashicons-editor-help"></p>';

			echo '<div class="pwoosms-tab-help" style="display: none;">
				<p><code>{product_id}</code> : آیدی محصول ، <code>{sku}</code> : شناسه محصول ، <code>{product_title}</code> : عنوان محصول ، <code>{regular_price}</code> قیمت اصلی ، <code>{onsale_price}</code> : قیمت فروش فوق العاده<br><code>{onsale_from}</code> : تاریخ شروع فروش فوق العاده ، <code>{onsale_to}</code> : تاریخ اتمام فروش فوق العاده ، <code>{stock}</code> : موجودی انبار</p>
			</div>';

			echo '<div class="setting-div"></div>';

			$this->product_metas[] = 'enable_onsale';
			woocommerce_wp_checkbox( [
				'cbvalue'     => 'on',
				'desc_tip'    => true,
				'label'       => 'زمانیکه که محصول حراج شد',
				'description' => 'با فعالسازی این گزینه، در صورت حراج نبودن محصول، گزینه "زمانیکه که محصول حراج شد" در فرم عضویت خبرنامه نمایش داده خواهد شد.',
				'id'          => end( $this->product_metas ),
				'value'       => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			$this->product_metas[] = 'notif_onsale_text';
			woocommerce_wp_text_input( [
				'desc_tip' => true,
				'label'    => 'متن گزینه "زمانیکه محصول حراج شد"',
				'id'       => end( $this->product_metas ),
				'value'    => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			$this->product_metas[] = 'notif_onsale_sms';
			woocommerce_wp_textarea_input( [
				'class'    => 'short',
				'desc_tip' => true,
				'label'    => 'متن پیامک "زمانیکه محصول حراج شد"',
				'id'       => end( $this->product_metas ),
				'value'    => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			echo '<div class="setting-div"></div>';

			$this->product_metas[] = 'enable_notif_no_stock';
			woocommerce_wp_checkbox( [
				'cbvalue'     => 'on',
				'desc_tip'    => true,
				'label'       => 'زمانیکه که محصول موجود شد',
				'description' => 'با فعالسازی این گزینه، در صورت ناموجود بودن محصول، گزینه "زمانیکه که محصول موجود شد" در فرم عضویت خبرنامه نمایش داده خواهد شد.',
				'id'          => end( $this->product_metas ),
				'value'       => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			$this->product_metas[] = 'notif_no_stock_text';
			woocommerce_wp_text_input( [
				'desc_tip' => true,
				'label'    => 'متن گزینه "زمانیکه محصول موجود شد"',
				'id'       => end( $this->product_metas ),
				'value'    => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			$this->product_metas[] = 'notif_no_stock_sms';
			woocommerce_wp_textarea_input( [
				'desc_tip' => true,
				'label'    => 'متن پیامک "زمانیکه محصول موجود شد"',
				'id'       => end( $this->product_metas ),
				'value'    => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			echo '<div class="setting-div"></div>';

			$this->product_metas[] = 'enable_notif_low_stock';
			woocommerce_wp_checkbox( [
				'cbvalue'     => 'on',
				'desc_tip'    => true,
				'label'       => 'زمانیکه محصول رو به اتمام است',
				'description' => 'با فعالسازی این گزینه، در صورتی که موجودی انبار زیاد بود، گزینه "زمانیکه که محصول رو به اتمام است" در فرم عضویت خبرنامه نمایش داده خواهد شد.',
				'id'          => end( $this->product_metas ),
				'value'       => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );


			$this->product_metas[] = 'notif_low_stock_text';
			woocommerce_wp_text_input( [
				'desc_tip' => true,
				'label'    => 'متن گزینه "زمانیکه محصول رو به اتمام است"',
				'id'       => end( $this->product_metas ),
				'value'    => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );


			$this->product_metas[] = 'notif_low_stock_sms';
			woocommerce_wp_textarea_input( [
				'desc_tip' => true,
				'label'    => 'متن پیامک "زمانیکه محصول رو به اتمام است"',
				'id'       => end( $this->product_metas ),
				'value'    => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			echo '<div class="setting-div"></div>';


			$this->product_metas[] = 'notif_options';
			woocommerce_wp_textarea_input( [
				'desc_tip'    => true,
				'style'       => 'height:100px;',
				'label'       => 'گزینه های دلخواه',
				'description' => 'شما میتوانید گزینه های دلخواه خود را برای نمایش در صفحه محصولات ایجاد نمایید و به صورت دستی به مشتریانی که در گزینه های بالا عضو شده اند پیامک ارسال کنید.<br>
		برای اضافه کردن گزینه ها، همانند نمونه بالا ابتدا یک کد عددی دلخواه تعریف کنید سپس بعد از قرار دادن عبارت ":" متن مورد نظر را بنویسید.<br>
		دقت کنید که کد عددی هر گزینه بسیار مهم بوده و از تغییر کد مربوط به هر گزینه بعد از ذخیره تنظیمات خود داری نمایید.',
				'id'          => end( $this->product_metas ),
				'value'       => PWooSMS()->getValue( end( $this->product_metas ), $product_id ),
			] );

			echo '<input type="hidden" name="sms_notification_metas" value="' . esc_attr( implode( ',', $this->product_metas ) ) . '">';

			echo '<hr>';
		}
	}

	private function productAdminSettings( $product_id ) {

		if ( $this->enable_product_admin_sms ) { ?>

            <div class="pwoosms-tab-product-admin">
                <p><strong>تنظیمات فروشندگان و مدیران محصول: </strong></p>
            </div>

			<?php
			$all_statuses   = PWooSMS()->GetAllProductAdminStatuses();
			$default_status = PWooSMS()->Options( 'product_admin_meta_order_status' );

			/*فروشندگان ست شده با متا*/
			$meta_tab_data = [];

			$meta_mobile = PWooSMS()->User_Meta_Mobile( $product_id );
			if ( ! empty( $meta_mobile['meta'] ) ) {
				$meta_tab_data[] = $meta_mobile;
			}

			$meta_mobile = PWooSMS()->Post_Meta_Mobile( $product_id );
			if ( ! empty( $meta_mobile['meta'] ) ) {
				$meta_tab_data[] = $meta_mobile;
			}

			foreach ( $meta_tab_data as $tab ) {

				$meta  = $tab['meta'];
				$label = 'شماره موبایل';

				if ( $meta == 'user' ) {
					$label = $label . '<span style="color: steelblue">' . ' (User Meta)' . '</span>';
				}

				if ( $meta == 'post' ) {
					$label = $label . '<span style="color: steelblue">' . ' (Post Meta)' . '</span>';
				}

				woocommerce_wp_text_input( [
					'id'          => 'pwoosms_tab_mobile_meta_' . $meta,
					'class'       => 'pwoosms_tab_mobile',
					'label'       => $label,
					'value'       => $tab['mobile'],
					'placeholder' => 'با کاما جدا کنید',
				] );

				PWooSMS()->multiSelectAdminField( [
					'id'      => 'pwoosms_tab_status_meta_' . $meta,
					'class'   => 'pwoosms_tab_status',
					'label'   => 'وضعیت سفارش',
					'value'   => PWooSMS()->prepareAdminProductStatus( $tab['statuses'] ),
					/*'default' => $default_status,*/
					'options' => $all_statuses,
					'style'   => 'width:70%;height:10.5em;',
				] );
			}
			if ( ! empty( $meta_tab_data ) ) {
				echo '<div class="setting-div"></div>';
			}

			/*فروشندگان وارد شده دستی*/
			$i        = 1;
			$tab_data = array_filter( (array) get_post_meta( $product_id, '_pwoosms_product_admin_data', true ) );
			foreach ( $tab_data as $tab ) { ?>

                <section class="button-holder-sms">
                    <a href="#" onclick="return false;" class="delete_this_sms_tab sms_tab_counter">(حذف)</a>
                </section>

				<?php
				woocommerce_wp_text_input( [
					'id'          => 'pwoosms_tab_mobile_' . $i,
					'class'       => 'pwoosms_tab_mobile',
					'label'       => 'شماره موبایل',
					'value'       => $tab['mobile'],
					'placeholder' => 'با کاما جدا کنید',
				] );

				PWooSMS()->multiSelectAdminField( [
					'id'      => 'pwoosms_tab_status_' . $i,
					'class'   => 'pwoosms_tab_status',
					'label'   => 'وضعیت سفارش',
					'value'   => PWooSMS()->prepareAdminProductStatus( $tab['statuses'] ),
					'default' => $default_status,
					'options' => $all_statuses,
					'style'   => 'width:70%;height:10.5em;',
				] );

				if ( $i != count( $tab_data ) ) {
					echo '<div class="pwoosms-tab-divider"></div>';
				}

				$i ++;
			}
			?>


            <div id="duplicate_this_row_sms">

                <a href="#" onclick="return false;" class="delete_this_sms_tab sms_tab_counter">(حذف)</a>

				<?php
				woocommerce_wp_text_input( [
					'id'          => 'hidden_duplicator_row_mobile',
					'class'       => 'pwoosms_tab_mobile',
					'label'       => 'شماره موبایل',
					'placeholder' => 'با کاما جدا کنید',
				] );

				PWooSMS()->multiSelectAdminField( [
					'id'      => 'hidden_duplicator_row_statuses',
					'class'   => 'pwoosms_tab_status',
					'label'   => 'وضعیت سفارش',
					'value'   => '',
					'default' => $default_status,
					'options' => $all_statuses,
					'style'   => 'width:70%;height:10.5em;',
				] );
				?>

                <section class="button-holder-sms"></section>

            </div>

            <p>
                <a href="#" class="button-secondary" id="add_another_sms_tab">
                    <span class="dashicons dashicons-plus-alt"></span>
                    افزودن فروشنده
                </a>
            </p>

			<?php echo '<input type="hidden" value="' . count( esc_attr( $tab_data ) ) . '" id="sms_tab_counter" name="sms_tab_counter" >';
		}
	}

	public function updateTabData( $product_id = 0 ) {

		if ( $this->enable_notification && ! empty( $_POST['sms_notification_metas'] ) ) {
			$updated = [];
			foreach ( explode( ',', $_POST['sms_notification_metas'] ) as $product_meta ) {
				$product_meta = ltrim( $product_meta, '_' );
				$this_meta    = sanitize_text_field( $_POST[ $product_meta ] ?? '' );
				if ( wp_unslash( PWooSMS()->maybeBool( $this_meta ) ) != wp_unslash( PWooSMS()->Options( $product_meta ) ) ) {
					$updated[] = $product_meta;
					update_post_meta( $product_id, '_' . $product_meta, esc_textarea( $this_meta ) );
				} else {
					delete_post_meta( $product_id, '_' . $product_meta );
				}
			}

			if ( ! empty( $updated ) ) {
				update_post_meta( $product_id, '_is_sms_set', $updated );
			} else {
				delete_post_meta( $product_id, '_is_sms_set' );
			}
		}

		if ( $this->enable_product_admin_sms ) {

			if ( isset( $_POST['sms_tab_counter'] ) ) {
				$tab_data = [];
				$count    = intval( $_POST['sms_tab_counter'] );
				for ( $i = 1; $i <= $count; $i ++ ) {

					if ( empty( $_POST[ 'pwoosms_tab_mobile_' . $i ] ) ) {
						continue;
					}

					$mobile   = stripslashes( PWooSMS()->sanitize_text_field( $_POST[ 'pwoosms_tab_mobile_' . $i ] ) );
					$statuses = ! empty( $_POST[ 'pwoosms_tab_status_' . $i ] ) ? sanitize_text_field($_POST[ 'pwoosms_tab_status_' . $i ]) : '';

					$tab_data[ $i ] = [
						'mobile'   => $mobile,
						'statuses' => PWooSMS()->prepareAdminProductStatus( $statuses, false ),
					];
				}

				if ( ! empty( $tab_data ) ) {
					update_post_meta( $product_id, '_pwoosms_product_admin_data', array_values( $tab_data ) );
				} else {
					delete_post_meta( $product_id, '_pwoosms_product_admin_data' );
				}
			}

			/*ذخیره شماره های مربوط به متا*/
			foreach ( [ 'user', 'post' ] as $meta ) {
				if ( isset( $_POST[ 'pwoosms_tab_mobile_meta_' . $meta ] ) ) {

					$mobile   = ! empty( $_POST[ 'pwoosms_tab_mobile_meta_' . $meta ] ) ? sanitize_text_field($_POST[ 'pwoosms_tab_mobile_meta_' . $meta ]) : '';
					$statuses = ! empty( $_POST[ 'pwoosms_tab_status_meta_' . $meta ] ) ? sanitize_text_field($_POST[ 'pwoosms_tab_status_meta_' . $meta ]) : '';
					$statuses = PWooSMS()->prepareAdminProductStatus( $statuses, false );

					$old_value    = $meta == 'post' ? PWooSMS()->Post_Meta_Mobile( $product_id ) : PWooSMS()->User_Meta_Mobile( $product_id );
					$old_mobile   = ! empty( $old_value['mobile'] ) ? $old_value['mobile'] : '';
					$old_statuses = ! empty( $old_value['statuses'] ) ? $old_value['statuses'] : '';
					$old_statuses = PWooSMS()->prepareAdminProductStatus( $old_statuses, false );

					//این شرط مهمه. نباید حذف بشه
					if ( $mobile != $old_mobile || $statuses != $old_statuses ) {
						update_post_meta( $product_id, '_pwoosms_product_admin_meta_' . $meta, [
							'meta'     => $meta,
							'mobile'   => PWooSMS()->sanitize_text_field( $mobile ),
							'statuses' => $statuses,
						] );
					}
				}
			}
		}
	}
}

new WoocommerceIR_SMS_Product_Tab();