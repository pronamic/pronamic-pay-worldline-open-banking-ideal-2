<?php
/**
 * Payment detailed information
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Payment detailed information class
 */
final class PaymentDetailedInformation {
	/**
	 * Common payment data response.
	 *
	 * @var CommonPaymentDataResponse
	 */
	public $common_payment_data;

	/**
	 * Construct payment initiation response.
	 *
	 * @param CommonPaymentDataResponse $common_payment_data Common payment data response.
	 */
	public function __construct( $common_payment_data ) {
		$this->common_payment_data = $common_payment_data;
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
			CommonPaymentDataResponse::from_object( $object_access->get_property( 'CommonPaymentData' ) )
		);
	}
}
