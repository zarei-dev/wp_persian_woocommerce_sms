<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WoocommerceIR_SMS_Contacts_List_Table extends WP_List_Table {

	public static $table = 'woocommerce_ir_sms_contacts';
	private static $users = [];

	public function __construct() {
		parent::__construct( [
			'singular' => 'لیست مشترکین خبرنامه پیامکی محصولات ووکامرس',
			'plural'   => 'لیست مشترکین خبرنامه پیامکی محصولات ووکامرس',
			'ajax'     => false,
		] );
	}

	public function no_items() {
		echo 'موردی یافت نشد.';
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			default:
				return print_r( $item, true );
		}
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="item[]" value="%s" />', $item['id']
		);
	}

	public function column_mobile( $item ) {

		if ( empty( $item['mobile'] ) ) {
			return '-';
		}

		$mobile = $this->mobile_with_user( $item['mobile'] );

		$product_ids = self::request_product_id( true );
		if ( count( $product_ids ) == 1 ) {
			$mobile .= $this->column_product_id( $item, false );
		}

		return '<div style="direction:ltr !important; text-align:' . ( is_rtl() ? 'right' : 'left' ) . ';">' . $mobile . '</div>';
	}

	private function mobile_with_user( $_mobile ) {

		$mobile = self::prepareMobile( $_mobile );
		$user   = ! empty( self::$users[ $mobile ] ) ? self::$users[ $mobile ] : (object) [];
		$mobile = PWooSMS()->modifyMobile( $mobile );

		if ( ! empty( $user->ID ) ) {

			$user_id = $user->ID;

			$full_name = get_user_meta( $user_id, 'billing_first_name', true ) . ' ' . get_user_meta( $user_id, 'billing_last_name', true );
			$full_name = trim( $full_name );
			if ( empty( $full_name ) && ! empty( $user->display_name ) ) {
				$full_name = ucwords( $user->display_name );
			}

			if ( ! empty( $full_name ) ) {
				$mobile = '(' . $full_name . ')&lrm;  ' . $_mobile;
				$mobile = '<a target="_blank" href="' . get_edit_user_link( $user_id ) . '">' . $mobile . '</a>';
			}
		}

		return $mobile;
	}

	public static function prepareMobile( $mobile ) {
		return substr( ltrim( $mobile, '0' ), - 10 );
	}

	public static function request_product_id( $array = false ) {

		$product_ids = ! empty( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : array();
		if ( ! is_array( $product_ids ) ) {
			$product_ids = explode( ',', (string) $product_ids );
		}
		$product_ids = array_map( 'intval', $product_ids );
		$product_ids = array_unique( array_filter( $product_ids ) );
		if ( $array ) {
			return $product_ids;
		}

		return implode( ',', $product_ids );
	}

	public function column_product_id( $item, $this_column = true ) {

		$product_id = intval( $item['product_id'] );

		$column_value = '';

		if ( $this_column ) {
			$column_value = '-';
			if ( $product_id ) {
				$title        = get_the_title( $product_id );
				$title        = ! empty( $title ) ? $product_id . ' :: ' . $title : $product_id;
				$column_value = '<a title="مشاهده لیست مشترکین این محصول" href="' . add_query_arg( [ 'product_id' => $product_id ] ) . '">' . $title . '</a>';
			}
		}

		$query_args  = [ 'edit' => absint( $item['id'] ) ];
		$product_ids = self::request_product_id();
		if ( ! empty( $product_ids ) ) {
			$query_args['product_id'] = $product_ids;
		}

		$edit_url = add_query_arg( $query_args,
			admin_url( 'admin.php?page=persian-woocommerce-sms-pro&tab=contacts' ) );

		$delete_url = add_query_arg( [
			'action'   => 'delete',
			'item'     => absint( $item['id'] ),
			'_wpnonce' => wp_create_nonce( 'pwoosms_delete_contact' ),
		] );

		$actions = [
			'edit'   => sprintf( '<a href="%s">%s</a>', $edit_url, 'ویرایش مشترک' ),
			'delete' => sprintf( '<a href="%s">%s</a>', $delete_url, 'حذف مشترک' ),
		];

		if ( ! empty( $product_id ) ) {
			$actions['edit_product'] = sprintf( '<a target="_blank" href="%s">%s</a>', get_edit_post_link( $product_id ), 'مدیریت محصول' );
		}

		return sprintf( '%1$s %2$s', $column_value, $this->row_actions( $actions ) );
	}

	public function column_groups( $item ) {

		if ( empty( $item['groups'] ) || empty( $item['product_id'] ) ) {
			return '-';
		}

		$product_id  = intval( $item['product_id'] );
		$groups      = explode( ',', $item['groups'] );
		$group_names = [];
		foreach ( $groups as $group_id ) {
			$name = WoocommerceIR_SMS_Contacts::groupName( $group_id, $product_id, true );
			if ( empty( $name ) ) {
				$name = WoocommerceIR_SMS_Contacts::groupName( $group_id, $product_id, false );
				if ( ! empty( $name ) ) {
					$name .= ' (غیرفعال)';
				} else {
					$name = 'گروه حذف شده';
				}
			}
			$group_names[] = $name;
		}

		return implode( ' | ', array_filter( $group_names ) );
	}

	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$this->process_bulk_action();

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
		] );
		$this->items = $this->get_items( $per_page, $current_page );
	}

	public function get_columns() {

		$columns = [
			'cb'         => '<input type="checkbox" />',
			'product_id' => 'محصول',
			'mobile'     => 'موبایل',
			'groups'     => 'گروه',
		];

		$product_ids = self::request_product_id( true );

		if ( count( $product_ids ) == 1 ) {
			unset( $columns['product_id'] );
		}

		return $columns;
	}

	public function get_sortable_columns() {
		return [
			'product_id' => [ 'product_id', false ],
			'mobile'     => [ 'mobile', false ],
			'groups'     => [ 'groups', false ],
		];
	}

	public function process_bulk_action() {

		$action = $this->current_action();

		if ( 'delete' === $action ) {

			if ( ! empty( $_REQUEST ) && ! wp_verify_nonce( sanitize_text_field( $_REQUEST['_wpnonce'] ), 'pwoosms_delete_contact' ) ) {
				die( 'خطایی رخ داده است. بعدا تلاش کنید.' );
			}

			$this->delete_item( absint( $_REQUEST['item'] ) );

			echo '<div class="updated notice is-dismissible below-h2"><p>آیتم حذف شد.</p></div>';
		} else if ( $action == 'bulk_delete' ) {

			if ( ! empty( $_REQUEST ) && ! wp_verify_nonce( sanitize_text_field( $_REQUEST['_wpnonce'] ), 'bulk-' . $this->_args['plural'] ) ) {
				die( 'خطایی رخ داده است. بعدا تلاش کنید.' );
			}

			$delete_ids = array_map( 'intval', $_REQUEST['item'] ?? [] );

			foreach ( (array) $delete_ids as $id ) {
				$this->delete_item( absint( $id ) );
			}

			echo '<div class="updated notice is-dismissible below-h2"><p>آیتم ها حذف شدند.</p></div>';
		}
	}

	public function delete_item( $id ) {
		global $wpdb;

		$wpdb->delete( self::table(), [ 'id' => $id ] );
	}

	public static function table(): string {
		global $wpdb;

		return $wpdb->prefix . self::$table;
	}

	public function record_count() {

		global $wpdb;

		return $wpdb->get_var( $this->get_query( true ) );
	}

	private function get_query( $count = false ) {
		global $wpdb;

		$table = self::table();

		$where = [];
		if ( isset( $_REQUEST['s'] ) ) {
		  $s       =  sanitize_text_field( $_REQUEST['s'] );
		  $s       =  self::prepareMobile( $s );
		  $where[] =  '(mobile LIKE "%' . $wpdb->prepare( "%s",$s ). '%")';
		}

		if ( ! empty( $_REQUEST['product_id'] ) ) {
			$where[] = '`product_id` IN (' . self::request_product_id() . ')';
		}

		$where = ! empty( $where ) ? '(' . implode( ' AND ', $where ) . ')' : '';

		$order_by = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( trim( $_REQUEST['orderby'] ) ) : '';

		$select = $count ? 'count(*)' : '*';

		if ( $order_by == 'groups' ) {

			$sql = $wpdb->prepare( "SELECT %s, SUBSTRING_INDEX(SUBSTRING_INDEX(t.groups, ',', n.n), ',', -1) groups
                    FROM %s t CROSS JOIN (SELECT a.N + b.N * 10 + 1 n FROM
                    (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a,
                    (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
                    ORDER BY n) n WHERE (n.n <= 1 + (LENGTH(t.groups) - LENGTH(REPLACE(t.groups, ',', ''))))", $select, $table );

			if ( ! empty( $where ) ) {
				$sql .= " AND {$where}";
			}

		} else {
			$sql = "SELECT $select FROM `$table`";
			if ( ! empty( $where ) ) {
				$sql .= " WHERE {$where}";
			}
		}

		if ( ! empty( $order_by ) ) {
			$order = $_REQUEST['order'] == 'DESC' ? ' DESC' : 'ASC';
			$sql .= $wpdb->prepare( " ORDER BY %s $order", $order_by );
			if ( $order_by != 'product_id' ) {
				$sql .= ", product_id $order";
			}
		} else {
			$sql .= ' ORDER BY id DESC';
		}

		return $sql;
	}

	public function get_items( int $per_page = 20, int $page_number = 1 ) {
		global $wpdb;

		$sql = $this->get_query();
		//$sql     .= $wpdb->prepare("LIMIT %d" , $per_page);
		//$sql     .= $wpdb->prepare(" OFFSET ( %d - 1 ) * %d" , $page_number , $per_page);
		$results = $wpdb->get_results( $sql, 'ARRAY_A' );

		$this->set_users_mobile( $results );

		return $results;
	}

	public function set_users_mobile( $result ) {

		$mobiles = array_unique( wp_list_pluck( $result, 'mobile' ) );

		$meta_key  = PWooSMS()->buyerMobileMeta();
		$user_meta = [ 'relation' => 'OR' ];
		foreach ( $mobiles as $mobile ) {
			$user_meta[] = [
				'key'     => $meta_key,
				'value'   => self::prepareMobile( $mobile ),
				'compare' => 'LIKE',
			];
		}

		$users = ( new WP_User_Query( [ 'meta_query' => $user_meta ] ) )->get_results();

		$_users = [];
		foreach ( $users as $user ) {
			if ( ! empty( $user->ID ) ) {
				$_mobile = get_user_meta( $user->ID, $meta_key, true );
				$_mobile = self::prepareMobile( $_mobile );
				foreach ( $mobiles as $mobile ) {
					$mobile = self::prepareMobile( $mobile );
					if ( stripos( $_mobile, $mobile ) !== false ) {
						$_mobile = $mobile;
						break;
					}
				}
				$_users[ $_mobile ] = $user;
			}
		}

		self::$users = $_users;

		return $_users;
	}

	public function get_bulk_actions() {
		return [
			'bulk_delete' => 'حذف',
		];
	}
}

class WoocommerceIR_SMS_Contacts {

	public function __construct() {
		add_action( 'pwoosms_settings_form_bottom_sms_contacts', [ $this, 'contactsTable' ] );
		add_action( 'init', [ $this, 'createTable' ] );
		add_action( 'init', [ $this, 'moveOldContants__3_8' ] );
		add_action( 'wp_ajax_change_sms_text', [ $this, 'changeSmsTextCallback' ] );
	}

	public static function groupName( $group_id, $product_id, $cond = true ) {
		$groups = self::getGroups( $product_id, false, $cond );

		return isset( $groups[ $group_id ] ) ? $groups[ $group_id ] : '';
	}

	public static function getGroups( $product_id, $check = true, $cond = true ) {

		$groups = [];
		if ( ! $check || ! PWooSMS()->ProductHasProp( $product_id, 'is_on_sale' ) ) {
			if ( ! $cond || PWooSMS()->hasNotifCond( 'enable_onsale', $product_id ) ) {
				$groups['_onsale'] = PWooSMS()->getValue( 'notif_onsale_text', $product_id );
			}
		}

		if ( ! $check || ! PWooSMS()->ProductHasProp( $product_id, 'is_in_stock' ) ) {
			if ( ! $cond || PWooSMS()->hasNotifCond( 'enable_notif_no_stock', $product_id ) ) {
				$groups['_in'] = PWooSMS()->getValue( 'notif_no_stock_text', $product_id );
			}
		}

		if ( ! $check || PWooSMS()->ProductHasProp( $product_id, 'is_not_low_stock' ) ) {
			if ( ! $cond || PWooSMS()->hasNotifCond( 'enable_notif_low_stock', $product_id ) ) {
				$groups['_low'] = PWooSMS()->getValue( 'notif_low_stock_text', $product_id );
			}
		}

		foreach ( explode( PHP_EOL, (string) PWooSMS()->getValue( 'notif_options', $product_id ) ) as $option ) {
			$options = explode( ":", $option, 2 );
			if ( count( $options ) == 2 ) {
				$groups["$options[0]"] = $options[1];
			}
		}

		return array_filter( $groups );
	}

	/*------------------------------------------------------------------------------*/

	public static function getContactByMobile( $product_id, $mobile ) {

		$table = WoocommerceIR_SMS_Contacts_List_Table::table();

		$product_id = intval( $product_id );

		$mobile = WoocommerceIR_SMS_Contacts_List_Table::prepareMobile( $mobile );

		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE product_id=%d AND mobile LIKE '%s'", $product_id, "%$mobile%" ), ARRAY_A );
	}

	
	
	public static function getContactsMobiles( int $product_id, string $group ) {
		global $wpdb;

			$table      = WoocommerceIR_SMS_Contacts_List_Table::table();
			$query = "SELECT mobile FROM `$table` WHERE product_id=%d";
			$query .= " AND $table.groups=%s";
			$sql = $wpdb->prepare( $query, $product_id, $group );
			$mobiles = $wpdb->get_col( $sql );
			$mobiles = array_unique( array_filter( $mobiles ) );

		return $mobiles;
  }

	public function createTable() {

		if ( get_option( 'pwoosms_table_contacts_created' ) ) {
			return;
		}

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		}

		global $wpdb;

		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$table = WoocommerceIR_SMS_Contacts_List_Table::table();
		dbDelta( "CREATE TABLE IF NOT EXISTS {$table} (
			`id` mediumint(8) unsigned NOT NULL auto_increment,
			`product_id` mediumint(8) unsigned,
			`mobile` VARCHAR(250),
			`groups` VARCHAR(250),
			PRIMARY KEY  (id)
		) $charset_collate;" );

		update_option( 'pwoosms_table_contacts_created', '1' );
	}

	/*-------------------------------------------------------------------------------*/

	public function moveOldContants__3_8() {

		/*انتقال مخاطبین از پست متا به تیبل مجزا بعد از بروز رسانی به نسخه 4.0.0*/

		if ( get_option( 'pwoosms_table_contacts_updated' ) ) {
			return;
		}

		global $wpdb;

		if ( ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", WoocommerceIR_SMS_Contacts_List_Table::table() ) ) ) {
			return;
		}

		/*به نظرم در هر بار درخواست، مشترکین 50 تا محصول منتقل بشن کافیه. چون ممکنه یه محصول خودش ۱۰۰۰ تا مشترک داشته باشه*/
		$sql = $wpdb->prepare("SELECT * FROM %s WHERE `meta_key`='_hannanstd_sms_notification' LIMIT 50" , $wpdb->postmeta);

		$results = $wpdb->get_results( $sql, 'ARRAY_A' );
		if ( empty( $results ) ) {
			update_option( 'pwoosms_table_contacts_updated', '1' );
		}

		foreach ( $results as $result ) {

			$contacts = [];
			foreach ( explode( '***', (string) $result['meta_value'] ) as $contact ) {
				//تو نسخه های قبلی نوع پیام (تلگرام - پیامک) با این جدا میشد.
				[ $contact ] = explode( '_vsh_', $contact, 1 );
				if ( strlen( $contact ) < 2 ) {
					continue;
				}
				[ $mobile, $groups ] = explode( '|', $contact, 2 );
				if ( PWooSMS()->validateMobile( $mobile ) ) {
					$groups              = explode( ',', $groups );
					$_groups             = ! empty( $contacts[ $mobile ] ) ? $contacts[ $mobile ] : [];
					$contacts[ $mobile ] = array_unique( array_merge( $groups, $_groups ) );
				}
			}

			$insert     = true;
			$meta_id    = $result['meta_id'];
			$product_id = $result['post_id'];

			foreach ( $contacts as $mobile => $groups ) {
				$insert = self::insertContact( [
						'product_id' => $product_id,
						'mobile'     => $mobile,
						'groups'     => $groups,
					] ) && $insert;
			}

			if ( $insert ) {
				$wpdb->update( $wpdb->postmeta, [
					'meta_key' => '_pwoosms_newsletter_contacts__moved',
				], [
					'meta_id' => intval( $meta_id ),
				] );
			}
		}
	}

	public static function insertContact( $data ) {

		if ( empty( $data['product_id'] ) || empty( $data['mobile'] ) || empty( $data['groups'] ) ) {
			return false;
		}

		global $wpdb;

		return $wpdb->insert( WoocommerceIR_SMS_Contacts_List_Table::table(), [
			'product_id' => intval( $data['product_id'] ),
			'mobile'     => PWooSMS()->modifyMobile( $data['mobile'] ),
			'groups'     => self::prepareGroups( $data['groups'] ),
		], [ '%d', '%s', '%s' ] );
	}

	private static function prepareGroups( $groups ) {

		if ( ! is_array( $groups ) ) {
			$groups = explode( ',', (string) $groups );
		}

		$groups = array_map( 'sanitize_text_field', $groups );
		$groups = array_map( 'trim', $groups );
		$groups = array_unique( array_filter( $groups ) );
		$groups = implode( ',', $groups );

		return $groups;
	}

	public function contactsTable() {

		$updated = get_option( 'pwoosms_table_contacts_updated' );
		if ( ! $updated ) { ?>
            <div class="notice notice-info below-h2">
                <p>
                    <strong>
                        در حال انتقال دیتابیس مشترکین خبرنامه سایت شما از جدول post_meta به یک جدول مستقل هستیم.
                        این عمل با توجه به حجم مشترکین شما ممکن است کمی زمانبر باشد.
                        لطفا لحظات دیگری پس از انتقال کامل مشترکین مراجعه نمایید.
                    </strong>
                </p>
            </div>
			<?php return;
		} elseif ( $updated == '1' ) { ?>
            <div class="notice notice-success is-dismissible below-h2">
                <p>
                    <strong>
                        انتقال دیتابیس مشترکین خبرنامه سایت شما از جدول post_meta به یک جدول مستقل با موفقیت انجام شد.
                    </strong>
                </p>
            </div>
			<?php update_option( 'pwoosms_table_contacts_updated', '2' );
		}

		/*----------------------------------------------------------------------------*/
		if ( isset( $_GET['edit'] ) ) {
			$this->editContact( intval( $_GET['edit'] ) );
		} elseif ( isset( $_GET['add'] ) ) {
			$this->addContact( intval( $_GET['add'] ) );
		} else {

			$list = new WoocommerceIR_SMS_Contacts_List_Table();
			$list->prepare_items();

			echo '<style type="text/css">';
			echo '.wp-list-table .column-id { width: 5%; }';
			echo '</style>';


			$product_id = WoocommerceIR_SMS_Contacts_List_Table::request_product_id( true );
			$product_id = count( $product_id ) == 1 ? reset( $product_id ) : 0;

			if ( ! empty( $product_id ) && $title = get_the_title( $product_id ) ) {
				echo sprintf( '<h1>مشترکین محصول "%s"</h1>', esc_attr( $title ) ) . '<br><br>';
			}

			$query_args = [ 'add' => $product_id ];
			if ( ! empty( $product_id ) ) {
				$query_args['product_id'] = $product_id;
			}

			$add_url = add_query_arg( $query_args, admin_url( 'admin.php?page=persian-woocommerce-sms-pro&tab=contacts' ) ); ?>
            <a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">افزودن مشترک جدید</a>

			<?php if ( ! empty( $_GET['product_id'] ) || isset( $_GET['add'] ) || isset( $_GET['edit'] ) ) : ?>
                <a class="page-title-action"
                   href="<?php echo remove_query_arg( [ 'product_id', 'add', 'edit' ] ); ?>">
                    بازگشت به لیست همه مشترکین
                </a>
			<?php endif; ?>

            <form method="post">
                <input type="hidden" name="page" value="WoocommerceIR_SMS_Contacts_list_table">
				<?php
				$list->search_box( 'جستجوی شماره موبایل', 'search_id' );
				$list->display();
				?>
            </form>
		<?php } ?>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.delete a, a.delete, .button.action').on('click', function (e) {
                    var action1 = $('select[name="action"]').val();
                    var action2 = $('select[name="action2"]').val();
                    if ($(this).is('a') || action1 === 'bulk_delete' || action2 === 'bulk_delete') {
                        if (!confirm('آیا از انجام عملیات حذف مطمئن هستید؟ این عمل غیرقابل برگشت است.')) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            });
        </script>
		<?php
	}

	private function editContact( $contact_id = 0 ) {

		$operation  = empty( $contact_id ) ? 'add' : 'edit';
		$return_url = remove_query_arg( [ 'add', 'edit', 'added' ] );

		$data = $operation == 'edit' ? self::getContactById( $contact_id ) : [];

		if ( $operation == 'edit' ) {
			$product_id = ! empty( $data['product_id'] ) ? intval( $data['product_id'] ) : 0;
		} else {
			$product_id = intval( $_GET['add'] ?? 0 );
		}

		if ( ! empty( $_POST['_wpnonce'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'pwoosms_contact_nonce' ) ) {
				wp_die( 'خطایی رخ داده است.' );
			}

			$mobile = sanitize_text_field( $_POST['mobile'] ?? null );

			if ( empty( $mobile ) ) {
				$error = 'شماره موبایل الزامی است.';
			} elseif ( ! PWooSMS()->validateMobile( $mobile ) ) {
				$error = 'شماره موبایل وارد شده معتبر نیست.';
			}

			$groups = self::prepareGroups( sanitize_text_field( $_POST['groups'] ?? '' ) );
			if ( empty( $groups ) ) {
				$error = 'انتخاب حداقل یک گروه الزامی است.';
			}

			if ( empty( $error ) ) {

				$params = [
					'product_id' => $product_id,
					'mobile'     => $mobile,
					'groups'     => $groups,
				];

				if ( $operation == 'edit' ) {
					$save = self::updateContact( array_merge( [ 'id' => $contact_id ], $params ) );
				} else {
					$save = self::insertContact( $params );
				}

				if ( $save !== false ) {
					if ( $operation == 'edit' ) {
						$saved = true;
					} else {
						global $wpdb;
						$contact_id = $wpdb->insert_id;
						wp_redirect( add_query_arg( [ 'edit' => $contact_id, 'added' => 'true' ], $return_url ) );
						exit();
					}
				} else {
					$error = 'در حین ذخیره خطایی رخ داده است. مجددا تلاش کنید.';
				}
			}

			if ( ! empty( $error ) ) { ?>
                <div class="notice notice-error below-h2">
                    <p><strong>خطا: </strong><?php echo esc_attr ( $error ); ?></p>
                </div>
				<?php
			}
		} else {
			$mobile = ! empty( $data['mobile'] ) ? PWooSMS()->modifyMobile( $data['mobile'] ) : '';
			$groups = ! empty( $data['groups'] ) ? $data['groups'] : '';
		}

		$contact_groups = array_map( 'strval', explode( ',', $groups ) );
		$contact_groups = array_map( 'trim', $contact_groups );

		if ( ! empty( $saved ) || ! empty( $_GET['added'] ) ) { ?>
            <div class="notice notice-success below-h2">
                <p><strong>مشترک ذخیره شد.</strong>
                    <a href="<?php echo esc_url( $return_url ); ?>">بازگشت به لیست مشترکین</a>
                </p>
            </div>
			<?php
		}

		$title = $operation == 'edit' ? 'ویرایش مشترک خبرنامه محصول "%s"' : 'افزودن مشترک جدید برای خبرنامه محصول "%s"'; ?>
        <h3><?php printf( $title, get_the_title( $product_id ) ); ?></h3>

        <form action="<?php echo remove_query_arg( [ 'added' ] ); ?>" method="post">
            <table class="form-table">
                <tbody>
                <tr>
                    <th><label for="mobile">شماره موبایل</label></th>
                    <td><input type="text" id="mobile" name="mobile" value="<?php echo esc_attr ( $mobile ); ?>"
                               style="text-align: left; direction: ltr"></td>
                </tr>
                <tr>
                    <th><label for="mobile">گروه ها</label></th>
                    <td>
						<?php
						$all_groups    = (array) WoocommerceIR_SMS_Contacts::getGroups( $product_id, false, false );
						$active_groups = (array) WoocommerceIR_SMS_Contacts::getGroups( $product_id, false, true );

						foreach ( $all_groups as $group => $label ) {
							$group = strval( $group ); ?>
                            <label for="groups_<?php echo esc_attr( $group ); ?>">
                                <input type="checkbox" name="groups[]" id="groups_<?php echo esc_attr( $group ); ?>"
                                       value="<?php echo esc_attr( $group ); ?>" <?php checked( in_array( $group, $contact_groups ) ) ?>>
								<?php
								echo esc_attr( $label );
								if ( ! in_array( $group, array_keys( $active_groups ) ) ) {
									echo ' (غیرفعال)';
								}
								?>
                            </label><br>
							<?php
						}
						?>
                    </td>
                </tr>
                </tbody>
            </table>

			<?php
			wp_nonce_field( 'pwoosms_contact_nonce', '_wpnonce' );
			$title = $operation == 'edit' ? 'بروز رسانی مشترک' : 'افزودن مشترک';
			?>

            <p class="submit">
                <input name="submit" class="button button-primary" value="<?php echo esc_attr( $title ); ?>" type="submit">
                <a href="<?php echo esc_url( $return_url ); ?>" class="button button-secondary">بازگشت</a>

				<?php if ( ! empty( $contact_id ) ) :

					$delete_url = add_query_arg( [
						'action'   => 'delete',
						'item'     => absint( $contact_id ),
						'_wpnonce' => wp_create_nonce( 'pwoosms_delete_contact' ),
					], $return_url ); ?>

                    <a class="delete" href="<?php echo esc_url( $delete_url ); ?>"
                       style="text-decoration: none; color: red">حذف
                        این مشترک</a>
				<?php endif; ?>
            </p>

        </form>
		<?php
	}

	/*-----------------------------------------------------------------------------------*/

	public static function getContactById( $contact_id ) {
		global $wpdb;
		$table = WoocommerceIR_SMS_Contacts_List_Table::table();

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id=%d", intval( $contact_id ) ), ARRAY_A );
	}

	public static function updateContact( array $data ) {
		global $wpdb;

		if ( empty( $data['id'] ) || empty( $data['mobile'] ) || empty( $data['groups'] ) ) {
			return false;
		}

		return $wpdb->update( WoocommerceIR_SMS_Contacts_List_Table::table(), [
			'mobile' => PWooSMS()->modifyMobile( $data['mobile'] ),
			'groups' => self::prepareGroups( $data['groups'] ),
		], [ 'id' => intval( $data['id'] ) ], [ '%s', '%s' ], [ '%d' ] );
	}

	private function addContact( $product_id = 0 ) {

		if ( ! empty( $product_id ) ) {
			$this->editContact();

			return;
		}
		?>

        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="select_product_id">یک محصول انتخاب کنید</label>
                </th>
                <td>
                    <select id="select_product_id" class="wc-product-search">
                        <option value="">یک محصول انتخاب کنید</option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('select#select_product_id').on('change', function () {
                    document.location = '<?php echo remove_query_arg( [ 'add' ] );?>' + "&add=" + $(this).val();
                });
            });
        </script>
		<?php
	}
}

new WoocommerceIR_SMS_Contacts();