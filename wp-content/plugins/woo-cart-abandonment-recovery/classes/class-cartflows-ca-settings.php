<?php
/**
 * Settings.
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

/**
 * Class Cartflows_Ca_Utils.
 */
class Cartflows_Ca_Settings {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;


	/**
	 * Cartflows_Ca_Settings constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'wcf_initialize_settings' ) );
		add_filter( 'plugin_action_links_' . CARTFLOWS_CA_BASE, array( $this, 'add_action_links' ), 999 );
	}

	/**
	 * Adding action links for plugin list page.
	 *
	 * @param array $links links.
	 * @return array
	 */
	public function add_action_links( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'admin.php?page=' . WCF_CA_PAGE_NAME ) . '">Settings</a>',
		);

		return array_merge( $mylinks, $links );
	}
	/**
	 * Add new settings for cart abandonment settings.
	 *
	 * @since 1.1.5
	 */
	function wcf_initialize_settings() {

		// Start: Settings for cart abandonment.
		add_settings_section(
			WCF_CA_GENERAL_SETTINGS_SECTION,
			__( 'Cart Abandonment Settings', 'cartflows-ca' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_status',
			__( 'Enable Tracking', 'cartflows-ca' ),
			array( $this, 'wcf_ca_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GENERAL_SETTINGS_SECTION,
			array( __( 'Start capturing abandoned carts. <br/><br/> <span class="description"><strong>Note:</strong> Cart will be considered abandoned if order is not completed in <strong>15 minutes</strong>.</span>', 'cartflows-ca' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_status'
		);

		// End: Settings for cart abandonment.
		// Start: Settings for email templates.
		add_settings_section(
			WCF_CA_EMAIL_SETTINGS_SECTION,
			__( 'Email Settings', 'cartflows-ca' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_from_name',
			__( '"From" Name', 'cartflows-ca' ),
			array( $this, 'wcf_ca_from_name_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_EMAIL_SETTINGS_SECTION,
			array( 'Name will appear in email sent.', 'cartflows-ca' )
		);

		add_settings_field(
			'wcf_ca_from_email',
			__( '"From" Address', 'cartflows-ca' ),
			array( $this, 'wcf_ca_from_email_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_EMAIL_SETTINGS_SECTION,
			array( 'Email which send from.', 'cartflows-ca' )
		);

		add_settings_field(
			'wcf_ca_reply_email',
			__( '"Reply To" Address', 'cartflows-ca' ),
			array( $this, 'wcf_ca_reply_email_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_EMAIL_SETTINGS_SECTION,
			array( 'When a user clicks reply, which email address should that reply be sent to?', 'cartflows-ca' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_from_name'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_from_email',
			array( $this, 'wcf_ca_from_email_validation' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_reply_email',
			array( $this, 'wcf_ca_reply_email_validation' )
		);
		// End: Settings for email templates.
		// Start: Settings for coupon code.
		add_settings_field(
			'wcf_ca_zapier_tracking_status',
			__( 'Enable Webhook', 'cartflows-ca' ),
			array( $this, 'wcf_ca_zapier_tracking_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( __( 'Allows you to trigger webhook automatically upon cart abandonment and recovery.', 'cartflows-ca' ) )
		);

		add_settings_field(
			'wcf_ca_zapier_cart_abandoned_webhook',
			__( 'Webhook URL', 'cartflows-ca' ),
			array( $this, 'wcf_ca_zapier_cart_abandoned_webhook_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( '', 'cartflows-ca' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_zapier_tracking_status'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_zapier_cart_abandoned_webhook'
		);

		add_settings_section(
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			__( 'Coupon Code Settings', 'cartflows-ca' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_coupon_code_status',
			__( 'Create Coupon Code', 'cartflows-ca' ),
			array( $this, 'wcf_ca_coupon_code_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( __( 'Auto-create the special coupon for the abandoned cart to send over the emails.', 'cartflows-ca' ) )
		);

		add_settings_field(
			'wcf_ca_discount_type',
			__( 'Discount Type', 'cartflows-ca' ),
			array( $this, 'wcf_ca_discount_type_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( '', 'cartflows-ca' )
		);

		add_settings_field(
			'wcf_ca_coupon_amount',
			__( 'Coupon Amount', 'cartflows-ca' ),
			array( $this, 'wcf_ca_coupon_amount_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( '', 'cartflows-ca' )
		);

		add_settings_field(
			'wcf_ca_coupon_expiry',
			__( 'Coupon Expires After', 'cartflows-ca' ),
			array( $this, 'wcf_ca_coupon_expiry_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( '<br/><br/> <span class="description"><strong>Note: </strong> Enter zero (0) to restrict coupon from expiring.</span>', 'cartflows-ca' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_expiry'
		);
		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_expiry_unit'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_code_status'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_discount_type'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_amount',
			array( $this, 'wcf_ca_coupon_amount_validation' )
		);
		// End: Settings for coupon code.
		// Start: Settings for Zapier.
		add_settings_section(
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			__( 'Webhook Settings', 'cartflows-ca' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		// End: Settings for webhook.
		// Start: GDPR Settings.
		add_settings_section(
			WCF_CA_GDPR_SETTINGS_SECTION,
			__( 'GDPR Settings', 'cartflows-ca' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_gdpr_status',
			__( 'Enable GDPR Integration', 'cartflows-ca' ),
			array( $this, 'wcf_ca_gdpr_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GDPR_SETTINGS_SECTION,
			array( __( 'Ask confirmation from the user before tracking data. <br/><br/> <span class="description"><strong>Note:</strong> By checking this, it will show up confirmation text below the email id on checkout page.</span>', 'cartflows-ca' ) )
		);

		add_settings_field(
			'wcf_ca_gdpr_message',
			__( 'GDPR Message', 'cartflows-ca' ),
			array( $this, 'wcf_ca_gdpr_message_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GDPR_SETTINGS_SECTION,
			array( '', 'cartflows-ca' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_gdpr_status'
		);
		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_gdpr_message'
		);

	}

	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_coupon_code_status_callback( $args ) {
		$wcf_ca_coupon_code_status = get_option( 'wcf_ca_coupon_code_status' );
		$html                      = '';
		printf(
			'<input type="checkbox" id="wcf_ca_coupon_code_status" name="wcf_ca_coupon_code_status" value="on"
            ' . checked( 'on', $wcf_ca_coupon_code_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_coupon_code_status"> ' . $args[0] . '</label>';
		echo $html;
	}


	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_zapier_cart_abandoned_webhook_callback( $args ) {
		$wcf_ca_zapier_cart_abandoned_webhook = get_option( 'wcf_ca_zapier_cart_abandoned_webhook' );
		echo '<input type="text" class="wcf-ca-trigger-input" id="wcf_ca_zapier_cart_abandoned_webhook" name="wcf_ca_zapier_cart_abandoned_webhook" value="' . sanitize_text_field( $wcf_ca_zapier_cart_abandoned_webhook ) . '" />';
		echo '<button id="wcf_ca_trigger_web_hook_abandoned_btn" type="button" class="button"> Trigger Sample </button>';
		echo '<span style="margin-left: 10px;" id="wcf_ca_abandoned_btn_message"></span>';
		$html = '<label for="wcf_ca_zapier_cart_abandoned_webhook"> ' . $args[0] . '</label>';
		echo $html;
	}


	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_zapier_tracking_status_callback( $args ) {
		$wcf_ca_zapier_tracking_status = get_option( 'wcf_ca_zapier_tracking_status' );

		$html = '';
		printf(
			'<input type="checkbox" id="wcf_ca_zapier_tracking_status" name="wcf_ca_zapier_tracking_status" value="on"
            ' . checked( 'on', $wcf_ca_zapier_tracking_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_zapier_tracking_status"> ' . $args[0] . '</label>';
		echo $html;
	}


	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_coupon_amount_callback( $args ) {
		$wcf_ca_coupon_amount = get_option( 'wcf_ca_coupon_amount' );
		printf(
			'<input type="number" class="wcf-ca-trigger-input wcf-ca-email-inputs" id="wcf_ca_coupon_amount" name="wcf_ca_coupon_amount" value="%s" />',
			isset( $wcf_ca_coupon_amount ) ? esc_attr( $wcf_ca_coupon_amount ) : ''
		);
		$html = '<label for="wcf_ca_coupon_amount"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_coupon_expiry_callback( $args ) {
		$wcf_ca_coupon_expiry = intval( get_option( 'wcf_ca_coupon_expiry' ) );
		printf(
			'<input type="number" class="wcf-ca-trigger-input wcf-ca-coupon-inputs" id="wcf_ca_coupon_expiry" name="wcf_ca_coupon_expiry" value="%s" autocomplete="off" />',
			isset( $wcf_ca_coupon_expiry ) ? esc_attr( $wcf_ca_coupon_expiry ) : ''
		);

		$coupon_expiry_unit = get_option( 'wcf_ca_coupon_expiry_unit' );
		$items              = array(
			'hours' => 'Hour(s)',
			'days'  => 'Day(s)',
		);
		echo "<select id='wcf_ca_coupon_expiry_unit' name='wcf_ca_coupon_expiry_unit'>";
		foreach ( $items as $key => $item ) {
			$selected = ( $coupon_expiry_unit === $key ) ? 'selected="selected"' : '';
			echo "<option value='$key' $selected>$item</option>";
		}
		echo '</select>';

		$html = '<label for="wcf_ca_coupon_expiry_unit"> ' . $args[0] . '</label>';
		echo $html;
	}



	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_gdpr_message_callback( $args ) {
		$wcf_ca_gdpr_message = get_option( 'wcf_ca_gdpr_message' );

		printf(
			'<textarea rows="2" cols="60" id="wcf_ca_gdpr_message" name="wcf_ca_gdpr_message" spellcheck="false">%s</textarea>',
			isset( $wcf_ca_gdpr_message ) ? esc_attr( $wcf_ca_gdpr_message ) : ''
		);
		$html = '<label for="wcf_ca_gdpr_message"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_discount_type_callback( $args ) {

		$discount_type = get_option( 'wcf_ca_discount_type' );
		$items         = array(
			'percent'    => 'Percentage discount',
			'fixed_cart' => 'Fixed cart discount',
		);
		echo "<select id='wcf_ca_discount_type' name='wcf_ca_discount_type'>";
		foreach ( $items as $key => $item ) {
			$selected = ( $discount_type === $key ) ? 'selected="selected"' : '';
			echo "<option value='$key' $selected>$item</option>";
		}
		echo '</select>';
	}

	/**
	 * Validation for cart abandonment `cut-off` settings.
	 *
	 * @param array $input input.
	 * @since 1.1.5
	 */
	function wcf_ca_coupon_amount_validation( $input ) {

		$output = '';
		if ( ( is_numeric( $input ) && $input >= 1 ) ) {
			$output = stripslashes( $input );
		} else {
			add_settings_error(
				'wcf_ca_coupon_amount',
				'error found',
				__( 'Coupon code should be numeric and has to be greater than or equals to 1.', 'cartflows-ca' )
			);
		}
		return $output;
	}

	/**
	 * Callback for cart abandonment options.
	 *
	 * @since 1.1.5
	 */
	function wcf_cart_abandonment_options_callback() {
		echo '<hr/>';
	}


	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_status_callback( $args ) {
		$wcf_ca_status = get_option( 'wcf_ca_status' );
		$html          = '';
		printf(
			'<input type="checkbox" id="wcf_ca_status" name="wcf_ca_status" value="on"
            ' . checked( 'on', $wcf_ca_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_status"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	function wcf_ca_gdpr_status_callback( $args ) {
		$wcf_ca_gdpr_status = get_option( 'wcf_ca_gdpr_status' );
		$html               = '';
		printf(
			'<input type="checkbox" id="wcf_ca_gdpr_status" name="wcf_ca_gdpr_status" value="on"
            ' . checked( 'on', $wcf_ca_gdpr_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_gdpr_status"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for email from name.
	 *
	 * @param array $args Arguments.
	 */
	public static function wcf_ca_from_name_callback( $args ) {
		$wcf_ca_from_name = get_option( 'wcf_ca_from_name' );
		printf(
			'<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="text" id="wcf_ca_from_name" name="wcf_ca_from_name" value="%s" />',
			isset( $wcf_ca_from_name ) ? esc_attr( $wcf_ca_from_name ) : ''
		);
		$html = '<label for="wcf_ca_from_name"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for email from.
	 *
	 * @param array $args Arguments.
	 */
	public static function wcf_ca_from_email_callback( $args ) {
		$wcf_ca_from_email = get_option( 'wcf_ca_from_email' );
		printf(
			'<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="text" id="wcf_ca_from_email" name="wcf_ca_from_email" value="%s" />',
			isset( $wcf_ca_from_email ) ? esc_attr( $wcf_ca_from_email ) : ''
		);
		$html = '<label for="wcf_ca_from_email"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for email reply.
	 *
	 * @param array $args Arguments.
	 * @since 3.5
	 */
	public static function wcf_ca_reply_email_callback( $args ) {
		$wcf_ca_reply_email = get_option( 'wcf_ca_reply_email' );
		printf(
			'<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="text" id="wcf_ca_reply_email" name="wcf_ca_reply_email" value="%s" />',
			isset( $wcf_ca_reply_email ) ? esc_attr( $wcf_ca_reply_email ) : ''
		);

		$html = '<label for="wcf_ca_reply_email"> ' . $args[0] . '</label>';
		echo $html;
	}


	/**
	 * Validation for email.
	 *
	 * @param array $input input.
	 * @since 1.1.5
	 */
	function wcf_ca_from_email_validation( $input ) {

		if ( $input && ! is_email( $input ) ) {
			add_settings_error(
				'wcf_ca_from_email',
				'error found',
				__( 'Invalid email "From" address field', 'cartflows-ca' )
			);
		}
		return sanitize_email( $input );
	}

	/**
	 * Validation for reply email.
	 *
	 * @param array $input input.
	 * @since 1.1.5
	 */
	function wcf_ca_reply_email_validation( $input ) {

		if ( $input && ! is_email( $input ) ) {
			add_settings_error(
				'wcf_ca_reply_email',
				'error found',
				__( 'Invalid email "Reply" address field', 'cartflows-ca' )
			);
		}
		return sanitize_email( $input );
	}

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}




}
Cartflows_Ca_Settings::get_instance();
