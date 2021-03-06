<?php
/**
 * LLMS_Transaction model class file
 *
 * @package LifterLMS/Models/Classes
 *
 * @since 3.0.0
 * @version 5.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * LifterLMS order transactions
 *
 * @since 3.0.0
 *
 * @property string $api_mode                   API Mode of the gateway when the transaction was made [test|live]/
 * @property float  $amount                     Transaction charge amount.
 * @property string $currency                   Transaction's currency code.
 * @property string $gateway_completed_date     Datetime string when the transaction was completed by the gateway (if gateway supports).
 * @property string $gateway_customer_id        Gateway's unique ID for the customer who placed the order.
 * @property float  $gateway_fee_amount         Fee charged to the user by the gateway for the transaction (if gateway supports).
 * @property string $gateway_source_id          Source Identifier from the gateway -- eg: credit card id or account id.
 * @property string $gateway_source_description Short description of the source from the gateway. EG: Visa 1234.
 * @property string $gateway_transaction_id     Gateway's unique ID for the transaction.
 * @property int    $order_id                   ID of the related LLMS_Order.
 * @property string $payment_type               Type of payment. [recurring|single|trial].
 * @property string $payment_gateway            LifterLMS Payment Gateway ID (eg "paypal" or "stripe").
 * @property float  $refund_amount              Amount refunded, will always be 0 until a refund is actually recorded.
 * @property array  $refund_data                Array of arrays. Contains refund data for each refund recorded for this transaction.
 */
class LLMS_Transaction extends LLMS_Post_Model {

	/**
	 * DB Post Type.
	 *
	 * @var string
	 */
	protected $db_post_type = 'llms_transaction';

	/**
	 * Model Name/Type.
	 *
	 * @var string
	 */
	protected $model_post_type = 'transaction';

	/**
	 * Post model properties.
	 *
	 * @var array
	 */
	protected $properties = array(
		'api_mode'                   => 'text',
		'amount'                     => 'float',
		'currency'                   => 'text',
		'gateway_completed_date'     => 'text',
		'gateway_customer_id'        => 'text',
		'gateway_fee_amount'         => 'float',
		'gateway_source_id'          => 'text',
		'gateway_source_description' => 'text',
		'gateway_transaction_id'     => 'text',
		'order_id'                   => 'absint',
		'payment_type'               => 'text',
		'payment_gateway'            => 'text',
		'refund_amount'              => 'float',
		'refund_data'                => 'array',
	);

	/**
	 * Determine if the transaction can be refunded
	 * Status must not be "failed" and total refunded amount must be less than order amount
	 *
	 * @return   boolean
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function can_be_refunded() {
		$status = $this->get( 'status' );
		// Can't refund failed or pending transactions.
		if ( 'llms-txn-failed' === $status || 'llms-txn-pending' === $status ) {
			return false;
		} elseif ( $this->get_refundable_amount( array(), 'float' ) <= 0 ) {
			return false;
		}
		return true;
	}

	/**
	 * Get the amount of the transaction that can be refunded
	 *
	 * @return   float
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function get_refundable_amount() {
		$amount   = $this->get_price( 'amount', array(), 'float' );
		$refunded = $this->get_price( 'refund_amount', array(), 'float' );
		return $amount - $refunded;
	}

	/**
	 * An array of default arguments to pass to $this->create()
	 * when creating a new post
	 *
	 * @since 3.0.0
	 * @since 3.37.6 Add a default date information using `llms_current_time()`.
	 *               Remove ordering placeholders from strftime().
	 * @since 5.9.0 Remove usage of deprecated `strftime()`.
	 *
	 * @param int $order_id LLMS_Order ID of the related order.
	 * @return array
	 */
	protected function get_creation_args( $order_id = 0 ) {

		$date = llms_current_time( 'mysql' );

		$title = sprintf(
			// Translators: %1$d = Order ID; %2$s = Transaction creation date.
			__( 'Transaction for Order #%1$d &ndash; %2$s', 'lifterlms' ),
			$order_id,
			date_format( date_create( $date ), 'M d, Y @ h:i A' )
		);

		return apply_filters(
			"llms_{$this->model_post_type}_get_creation_args",
			array(
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_author'    => 0,
				'post_content'   => '',
				'post_date'      => $date,
				'post_excerpt'   => '',
				'post_password'  => uniqid( 'order_' ),
				'post_status'    => 'llms-' . apply_filters( 'llms_default_order_status', 'txn-pending' ),
				'post_title'     => $title,
				'post_type'      => $this->get( 'db_post_type' ),
			),
			$this
		);
	}

	/**
	 * Get the total amount of the transaction after deducting refunds
	 *
	 * @param  array  $price_args  optional array of arguments that can be passed to llms_price()
	 * @param  string $format      optional format conversion method [html|raw|float]
	 * @return mixed
	 * @since  3.0.0
	 */
	public function get_net_amount( $price_args = array(), $format = 'html' ) {
		$amount = $this->get_price( 'amount', array(), 'float' );
		$refund = $this->get_price( 'refund_amount', array(), 'float' );
		return llms_price( $amount - $refund, $price_args, $format );
	}

	/**
	 * Retrieve an instance of LLMS_Order for the transaction's parent order
	 *
	 * @return   obj
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function get_order() {
		return new LLMS_Order( $this->get( 'order_id' ) );
	}

	/**
	 * Retrieve the payment gateway instance for the transactions payment gateway
	 *
	 * @since 3.0.0
	 *
	 * @return LLMS_Payment_Gateway or WP_Error
	 */
	public function get_gateway() {
		$gateways = llms()->payment_gateways();
		$gateway  = $gateways->get_gateway_by_id( $this->get( 'payment_gateway' ) );
		if ( $gateway && $gateway->is_enabled() || is_admin() ) {
			return $gateway;
		} else {
			return new WP_Error( 'error', sprintf( __( 'Payment gateway %s could not be located or is no longer enabled', 'lifterlms' ), $this->get( 'payment_gateway' ) ) );
		}
	}

	/**
	 * Process a Refund
	 * Called from the admin panel by clicking a refund (manual or gateway) button
	 *
	 * @param    float  $amount   amount to refund
	 * @param    string $note     optional note to record in the gateway (if possible) and as an order note
	 * @param    string $method   method used to refund, either "manual" (available for all transactions) or "gateway" (where supported)
	 * @return   string|WP_Error      a refund ID on success or a WP_Error object
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function process_refund( $amount, $note = '', $method = 'manual' ) {

		// Ensure the transaction is still eligible for a refund.
		if ( ! $this->can_be_refunded() ) {
			return new WP_Error( 'error', __( 'The selected transaction is not eligible for a refund.', 'lifterlms' ) );
		}

		$amount = floatval( $amount );

		// Ensure we can refund the requested amount.
		$refundable = $this->get_refundable_amount();
		if ( $amount > $refundable ) {
			return new WP_Error( 'error', sprintf( __( 'Requested refund amount was %1$s, the maximum possible refund for this transaction is %2$s.', 'lifterlms' ), llms_price( $amount ), llms_price( $refundable ) ) );
		}

		// Validate the method & process the refund.
		switch ( $method ) {

			// We're okay here.
			case 'manual':
				$refund_id    = apply_filters( 'llms_manual_refund_id', uniqid() );
				$method_title = __( 'manual refund', 'lifterlms' );
				break;

			// Check gateway to ensure it's valid and supports refunds.
			case 'gateway':
				$gateway = $this->get_gateway();
				if ( is_wp_error( $gateway ) ) {
					return new WP_Error( 'error', sprintf( __( 'Selected gateway "%s" is inactive or invalid.', 'lifterlms' ), $method ) );
				} else {
					if ( ! $gateway->supports( 'refunds' ) ) {
						return new WP_Error( 'error', sprintf( __( 'Selected gateway "%s" does not support refunds.', 'lifterlms' ), $gateway->get_admin_title() ) );
					} else {
						$refund_id    = $gateway->process_refund( $this, $amount, $note );
						$method_title = $gateway->get_admin_title();
					}
				}

				break;

			default:
				/**
				 * Allow custom refund methods for fancy developer folk
				 */
				$refund_id    = apply_filters( 'llms_' . $method . '_refund_id', false, $method, $this, $amount, $note );
				$method_title = apply_filters( 'llms_' . $method . '_title', $method );

		}

		// Output an error.
		if ( is_wp_error( $refund_id ) ) {

			return $refund_id;

		} elseif ( is_string( $refund_id ) ) {

			// Filter the note before recording it.
			$orig_note = apply_filters( 'llms_transaction_refund_note', $note, $this, $amount, $method );

			$order = $this->get_order();

			$note = sprintf( __( 'Refunded %1$s for transaction #%2$d via %3$s [Refund ID: %4$s]', 'lifterlms' ), strip_tags( llms_price( $amount ) ), $this->get( 'id' ), $method_title, $refund_id );

			if ( $orig_note ) {
				$note .= "\r\n";
				$note .= __( 'Refund Notes: ', 'lifterlms' );
				$note .= "\r\n";
				$note .= $orig_note;
			}

			// Record the note.
			$order->add_note( $note, true );

			// Update the refunded amount.
			$new_amount = ! $this->get( 'refund_amount' ) ? $amount : $this->get( 'refund_amount' ) + $amount;
			$this->set( 'refund_amount', $new_amount );

			// Record refund metadata.
			$refund_data               = $this->get_array( 'refund_data' );
			$refund_data[ $refund_id ] = apply_filters(
				'llms_transaction_refund_data',
				array(
					'amount' => $amount,
					'date'   => current_time( 'mysql' ),
					'id'     => $refund_id,
					'method' => $method,
				),
				$this,
				$amount,
				$method
			);
			$this->set( 'refund_data', $refund_data );

			// Update status.
			$this->set( 'status', 'llms-txn-refunded' );

			return $refund_id;

		} else {

			return new WP_Error( 'error', __( 'An unknown error occurred during refund processing', 'lifterlms' ) );

		}

	}

	/**
	 * Wrapper for $this-get() which allows translation of the database value before outputting on screen
	 *
	 * @param    string $key  key to retrieve
	 * @return   string
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	public function translate( $key ) {

		$val = $this->get( $key );

		switch ( $key ) {

			case 'payment_type':
				if ( 'single' === $val ) {
					$val = __( 'Single', 'lifterlms' );
				} elseif ( 'recurring' === $val ) {
					$val = __( 'Recurring', 'lifterlms' );
				} elseif ( 'trial' === $val ) {
					$val = __( 'Trial', 'lifterlms' );
				}
				break;

			default:
				$val = $val;
		}

		return $val;

	}

}
