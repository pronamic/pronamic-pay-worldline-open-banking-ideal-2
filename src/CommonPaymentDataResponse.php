<?php
/**
 * Common payment data response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Common payment data response class
 */
final class CommonPaymentDataResponse {
	/**
	 * Payment status.
	 *
	 * @var string
	 */
	public $payment_status;

	/**
	 * Payment ID.
	 *
	 * @var string
	 */
	public $payment_id;

	/**
	 * Construct payment initiation response.
	 *
	 * @param string $payment_status Payment status.
	 * @param string $payment_id     Payment ID.
	 */
	public function __construct( $payment_status, $payment_id ) {
		$this->payment_status = $payment_status;
		$this->payment_id     = $payment_id;
	}

	/**
	 * Create token response from object.
	 * 
	 * @param object $data Object.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		return new self(
			$object_access->get_string( 'PaymentStatus' ),
			$object_access->get_string( 'PaymentId' ),
		);
	}
}
