<?php
/**
 * Cart Abandonment
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Cart abandonment tracking table class.
 */
class Cartflows_Ca_Cart_Abandonment_Table extends WP_List_Table {



	/**
	 *  Constructor function.
	 */
	function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'id',
				'plural'   => 'ids',
			)
		);
	}

	/**
	 * Default columns.
	 *
	 * @param object $item        item.
	 * @param string $column_name column name.
	 */
	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Column name surname.
	 *
	 * @param  object $item item.
	 * @return string
	 */
	function column_nameSurname( $item ) {

		$item_details = unserialize( $item['other_fields'] );

		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		$view_url = add_query_arg(
			array(
				'page'       => WCF_CA_PAGE_NAME,
				'action'     => WCF_ACTION_REPORTS,
				'sub_action' => WCF_SUB_ACTION_REPORTS_VIEW,
				'session_id' => sanitize_text_field( $item['session_id'] ),
			),
			admin_url( '/admin.php' )
		);

		$actions = array(
			'view'   => sprintf( '<a href="%s">%s</a>', esc_url( $view_url ), __( 'View', 'cartflows-ca' ) ),
			'delete' => sprintf( '<a onclick="return confirm(\'Are you sure to delete this order?\');" href="?page=%s&action=delete&id=%s">%s</a>', esc_html( $page ), esc_html( $item['id'] ), __( 'Delete', 'cartflows-ca' ) ),
		);

		if ( WCF_CART_ABANDONED_ORDER === $item['order_status'] && ! $item['unsubscribed'] ) {
			$actions['unsubscribe'] = sprintf( '<a onclick="return confirm(\'Are you sure to unsubscribe this user? \');" href="?page=%s&action=unsubscribe&id=%s">%s</a>', esc_html( $page ), esc_html( $item['id'] ), __( 'Unsubscribe', 'cartflows-ca' ) );

		}

		return sprintf(
			'<a href="%s"><span class="dashicons dashicons-admin-users"></span> %s %s %s </a>',
			esc_url( $view_url ),
			esc_html( $item_details['wcf_first_name'] ),
			esc_html( $item_details['wcf_last_name'] ),
			$this->row_actions( $actions )
		);
	}

	/**
	 * Render date column
	 *
	 * @param  object $item - row (key, value array).
	 * @return HTML
	 */
	function column_time( $item ) {
		$database_time = $item['time'];
		$date_time     = new DateTime( $database_time );
		$date          = $date_time->format( 'd.m.Y' );
		$time          = $date_time->format( 'H:i:s' );

		return sprintf(
			'<span class="dashicons dashicons-clock"></span> %s %s',
			esc_html( $time ),
			esc_html( $date )
		);
	}

	/**
	 * This is how checkbox column renders.
	 *
	 * @param  object $item item.
	 * @return HTML
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			esc_html( $item['id'] )
		);
	}

	/**
	 * [OPTIONAL] Return array of bult actions if has any
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'cartflows-ca' ),
		);
		return $actions;
	}

	/**
	 * Whether the table has items to display or not
	 *
	 * @return bool
	 */
	public function has_items() {
		return ! empty( $this->items );
	}

	/**
	 * Fetch data from the database to render on view.
	 *
	 * @param string $cart_type abandoned|completed.
	 * @param string $from_date from date.
	 * @param string $to_date to date.
	 */
	function prepare_items( $cart_type = WCF_CART_ABANDONED_ORDER, $from_date = '', $to_date = '' ) {
		global $wpdb;
		$cart_abandonment_table_name = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;

		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$paged   = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
		$orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING );
		$order   = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_STRING );

		$paged   = $paged ? max( 0, $paged - 1 ) : 0;
		$orderby = ( $orderby && in_array( $orderby, array_keys( $this->get_sortable_columns() ), true ) ) ? $orderby : 'id';
		$order   = ( $order && in_array( $order, array( 'asc', 'desc' ), true ) ) ? $order : 'desc';

		$this->items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $cart_abandonment_table_name WHERE `order_status` = %s AND DATE(`time`) >= %s AND DATE(`time`) <= %s ORDER BY $orderby $order LIMIT %d OFFSET %d", $cart_type, $from_date, $to_date, $per_page, $paged * $per_page), ARRAY_A); // phpcs:ignore

        $total_items = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $cart_abandonment_table_name WHERE `order_status` = %s AND DATE(`time`) >= %s AND DATE(`time`) <= %s", $cart_type, $from_date, $to_date)); // phpcs:ignore

		// [REQUIRED] configure pagination
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Table columns.
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'nameSurname'  => __( 'Name', 'cartflows-ca' ),
			'email'        => __( 'Email', 'cartflows-ca' ),
			'cart_total'   => __( 'Cart Total', 'cartflows-ca' ),
			'order_status' => __( 'Order Status', 'cartflows-ca' ),
			'time'         => __( 'Time', 'cartflows-ca' ),
		);
		return $columns;
	}

	/**
	 * Table sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable = array(
			'nameSurname'  => array( 'name', true ),
			'cart_total'   => array( 'cart_total', true ),
			'cart_total'   => array( 'Cart Total', true ),
			'order_status' => array( 'Order Status', true ),
			'time'         => array( 'time', true ),
		);
		return $sortable;
	}

	/**
	 * Processes bulk actions
	 */
	function process_bulk_action() {
		global $wpdb;
		$table_name = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;

		if ( 'delete' === $this->current_action() ) {

			$ids = array();
			if ( isset( $_REQUEST['id'] ) && is_array( $_REQUEST['id'] ) ) {
				$ids = array_map( 'intval', $_REQUEST['id'] );
			} elseif ( isset( $_REQUEST['id'] ) ) {
				$ids = array( intval( $_REQUEST['id'] ) );
			}
			$ids = implode( ',', $ids );

			if ( ! empty( $ids ) ) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)"); // phpcs:ignore
			}
		}
	}
}
