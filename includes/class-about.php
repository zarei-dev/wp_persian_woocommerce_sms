<?php

defined( 'ABSPATH' ) || exit;

class WoocommerceIR_SMS_About {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'adminInit' ] );
		add_filter( 'pwoosms_settings_sections', [ $this, 'addSection' ], 9999, 1 );
		add_action( 'pwoosms_settings_form_bottom_sms_about', [ $this, 'aboutPage' ] );
		add_action( 'wp_ajax_pwoosms_hide_about_page', [ $this, 'ajaxCallback' ] );
	}

	public static function aboutPage() { ?>

        <div class="wrap about-wrap">

            <h1>افزونه حرفه ای پیامک ووکامرس</h1>

            <div class="about-text">

                <p>
                    این افزونه به صورت رایگان از سوی
                    <a target="_blank" href="http://woocommerce.ir/">ووکامرس فارسی</a>
                    ارائه شده است. هر گونه کپی برداری و کسب درآمد از آن توسط سایرین غیر مجاز می باشد.
                </p>

                <p>برنامه نویسان : <strong>حنّان ابراهیمی ستوده</strong> - <strong>محمد مجیدی</strong></p>
            </div>

            <div class="wp-badge"
                 style="background: #fff url('<?php echo PWOOSMS_URL . '/assets/images/logo.png'; ?>'); width: 128px !important; height: 10px !important;"></div>

            <h2 class="nav-tab-wrapper">
                <a href="<?php echo remove_query_arg( [] ); ?>" class="nav-tab nav-tab-active">برخی ویژگی
                    های افزونه</a>
                <a target="_blank" href="https://woosupport.ir" class="nav-tab">سایت ووکامرس فارسی</a>
                <a target="_blank"
                   href="http://forum.persianscript.ir/topic/25199-%D8%AA%D8%A7%D9%BE%DB%8C%DA%A9-%D9%BE%D8%B4%D8%AA%DB%8C%D8%A8%D8%A7%D9%86%DB%8C-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%BE%DB%8C%D8%A7%D9%85%DA%A9-%D9%88%D9%88%DA%A9%D8%A7%D9%85%D8%B1%D8%B3/?tab=comments#comment-129964"
                   class="nav-tab">انجمن پشتیبانی ووکامرس</a>
            </h2>

            <br><br>
            <div class="feature-section two-col">
                <div class="col" style="max-width: 30%; min-width: 30%;">
                    <img src="<?php echo PWOOSMS_URL . '/assets/images/2.png'; ?>"/>
                </div>
                <div class="col" style="max-width: 65%; min-width: 65%;">
                    <h3>ارسال پیامک بعد از ثبت و یا تغییر وضعیت سفارشات</h3>
                    <p>بعد از ثبت و یا تغییر وضعیت سفارش به مدیر کل، مشتری (خریدار) و مدیر محصول (فروشنده) به صورت
                        خودکار پیامک
                        ارسال کنید.
                        <br>قابلیت انتخاب وضعیت های سفارش دلخواه برای دریافت پیامک
                        <br>قابلیت انتخاب توسط مشتری برای دریافت و یا عدم دریافت پیامک برای وضعیت های مورد نظر
                        <br>
                        <br>قابلیت شخصی سازی متن های پیامک برای هر وضعیت سفارش
                        <br>قابلیت شخصی سازی متن های پیامک برای مدیر کل، خریدار و فروشنده
                        <br>قابلیت استفاده از شورتکد های متعدد داخل متن پیامک برای ارائه جزییات دقیق تر سفارش
                        <br>
                        <br>قابلیت دریافت پیامک در صورت کم شدن یا ناموجود شدن موجودی انبار هر محصول نیز در این افزونه
                        اضافه شده
                        است.
                    </p>
                </div>
            </div>


            <div class="feature-section two-col">
                <div class="col">
                    <h3>سیستم خبرنامه پیشرفته محصولات</h3>
                    <p>توسط این امکان میتوانید کاربران سایت خود را در حین موجود شدن محصولات، تخفیف و سایر رویداد های
                        متنوع، از
                        طریق پیامک با خبر
                        نمایید.</p>
                    <p>همچنین قادر خواهید بود که گزینه های دلخواه و مورد نظر خود را بسازید و کاربران را در آن گزینه ها
                        به اشتراک
                        در بیاورید.</p>
                    <br>
                    <p>در نسخه جدید قابلیت مشاهده، افزودن و ویرایش مشترکین خبرنامه نیز اضافه شده است.</p>
                    <br>
                    <p>نحوه نمایش این گزینه ها در صفحه محصولات به سه روش؛ خودکار، شورتکد و ابزارک صورت خواهد گرفت.</p>
                </div>
                <div class="col">
                    <img src="<?php echo PWOOSMS_URL . '/assets/images/4.png'; ?>"/>
                </div>
            </div>


            <div class="feature-section two-col">

                <div class="col">
                    <img src="<?php echo PWOOSMS_URL . '/assets/images/1.png'; ?>"/>
                </div>

                <div class="col">
                    <h3>پنل تنظیمات ساده</h3>
                    <p>در نسخه جدید پیامک ووکامرس جهت دسترسی سریع به تنظیمات پیامک و یکپارچگی کامل آن با فروشگاه ساز
                        ووکامرس،
                        پنل تنظیمات آن به صورت زیر منوی افزونه ووکامرس در آمده است.</p>
                    <p>همچنین جهت دسترسی سریع تر میتوانید گزینه "پیامک ووکامرس" را در ادمین بار فعال نمایید.</p>
                </div>
            </div>


            <div class="feature-section two-col">
                <div class="col">
                    <h3>اختصاص پیامک به فروشندگان و مدیران محصول</h3>
                    <p>شما علاوه بر اینکه میتوانید از قسمت تنظیمات افزونه، شماره موبایل، متن پیامک و وضعیت های سفارش را
                        برای
                        مدیران کل مشخص نمایید، میتوانید برای هر محصول دلخواه نیز شماره موبایل جدیدی (شماره موبایل
                        فروشنده محصول)
                        وارد کنید تا در صورت سفارش آن محصول
                        به آن شماره ها نیز پیامک ارسال شود.</p>
                    <hr>
                    <p>همچنین در نسخه های جدید علاوه بر وارد کردن دستی شماره فروشندگان، قابلیت تنظیم خودکار پیامک
                        فروشندگان نیز
                        اضافه شده است.</p>


                </div>
                <div class="col">
                    <img src="<?php echo PWOOSMS_URL . '/assets/images/3.png'; ?>"/>
                </div>
            </div>

            <hr>
            <div class="changelog">
                <h2>سایر امکانات افزونه 😊</h2>

                <div class="two-col">
                    <div class="col">
                        <h2>پشتیبانی از وبسرویس های متنوع</h2>
                        <p>افزونه پیامک ووکامرس تا کنون بیش از ۵۰ سامانه پیامکی را تحت پوشش خود قرار داده است. و از اکثر
                            سامانه
                            های پیامکی محبوب پشتیبانی میکند.</p>
                    </div>
                    <div class="col">
                        <h2>ارسال پیامک دسته جمعی</h2>
                        <p>شما از طریق منوی ووکامرس >> سفارشات، میتوانید سفارشات مورد نظر خود را مارک نموده و سپس از
                            طریق ابزار
                            اقدامات دسته جمعی، گزینه ارسال پیامک را انتخاب نمایید و به سفارشات مورد نظر به صورت دسته
                            جمعی پیامک
                            ارسال نمایید.</p>
                    </div>
                    <div class="col">
                        <h2>متاباکس ارسال پیامک</h2>
                        <p>داخل صفحه سفارشات و محصولات (پست تایپ های ووکامرس)، متاباکس ارسال پیامک اضافه خواهد شد که از
                            طریق آن
                            میتوانید به خریدار و یا مشترکین محصولات پیامک ارسال کنید.</p>
                    </div>

                    <div class="col">
                        <h2>مشاهده آرشیو پیامک های ارسالی</h2>
                        <p>تمام پیامک های در قسمت آرشیو پیامک های ارسالی قابل مشاهده خواهند بود تا در صورتی که پیامک ها
                            بنا به
                            هر دلیلی با خطا مواجه شدند، جزییات مشکل قابل مشاهده باشند.</p>
                    </div>

                </div>

            </div>


            <div class="changelog under-the-hood feature-list">

                <div class="rating">

                    <h3> و امکانات بی نظیر دیگر ....</h3>

                    <a href="https://wordpress.org/support/plugin/persian-woocommerce-sms/reviews/?rate=5#new-post"
                       target="_blank">
                        <div class="wporg-ratings" data-rating="5" style="color:#ffb900;">
                            <span style="color:#0073aa;">با دادن امتیاز ۵ ستاره به این افزونه، انگیزه ما را در بهبود امکانات بعدی، چند برابر کنید.</span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                            <span class="dashicons dashicons-star-filled"></span>
                        </div>
                    </a>
                </div>
            </div>
            <hr>

            <div class="changelog under-the-hood feature-list">
                <div class="last-feature">

                    <p>
                        در صورت تمایل به اضافه شدن پنل وبسرویس پیامکی خود با ایمیل info@woocommerce.ir و یا
                        hannanstd@gmail.com
                        در تماس باشید.

                        <a target="_blank"
                           href="http://forum.persianscript.ir/topic/25199-%D8%AA%D8%A7%D9%BE%DB%8C%DA%A9-%D9%BE%D8%B4%D8%AA%DB%8C%D8%A8%D8%A7%D9%86%DB%8C-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%BE%DB%8C%D8%A7%D9%85%DA%A9-%D9%88%D9%88%DA%A9%D8%A7%D9%85%D8%B1%D8%B3/?tab=comments#comment-129964">تاپیک
                            پشتیبانی افزونه پیامک ووکامرس</a>
                    </p>

                    <div class="return-to-dashboard">
                        <a href="<?php echo admin_url( 'admin.php?page=persian-woocommerce-sms-pro' ); ?>">رفتن به
                            پیکربندی
                            &larr; پیامک ووکامرس</a>
                    </div>
                    <br><br>
                    <hr>
                    <p>
                        <label for="pwoosms_hide_about_page">
                            <input type="checkbox" id="pwoosms_hide_about_page" value="1">
                            دیگر این صفحه را نشانم نده!
                        </label>
                    </p>
                </div>
            </div>


            <div class="clear"></div>
        </div>
        <style type="text/css">
            a {
                text-decoration: none !important;
            }

            p {
                line-height: 28px !important;
                text-align: justify;
            }
        </style>
        <script type="text/javascript">
            jQuery(document).on('change', '#pwoosms_hide_about_page', function () {
                jQuery.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'pwoosms_hide_about_page'
                    }
                }).done(function () {
                    window.location = "<?php echo admin_url( 'admin.php?page=persian-woocommerce-sms-pro' ); ?>";
                });
            })
        </script>
	<?php }

	public function addSection( $sections ) {

		if ( ! get_option( 'pwoosms_hide_about_page' ) ) {
			$sections[] = [
				'id'       => 'sms_about',
				'title'    => 'درباره',
				'form_tag' => false,
			];
		}

		return $sections;
	}

	public function adminInit() {
		if ( ! get_option( 'pwoosms_redirect_about_page' ) ) {

			delete_option( 'pwoosms_hide_about_page' );
			update_option( 'pwoosms_redirect_about_page', '1' );

			if ( ! headers_sent() ) {
				wp_redirect( admin_url( 'admin.php?page=persian-woocommerce-sms-pro&tab=about' ) );
				exit();
			}
		}
	}

	public function ajaxCallback() {
		update_option( 'pwoosms_hide_about_page', '1' );
		die();
	}
}

new WoocommerceIR_SMS_About();