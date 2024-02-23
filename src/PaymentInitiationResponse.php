<?php
/**
 * Payment initiation response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Payment initiation response class
 */
final class PaymentInitiationResponse {
	/**
	 * Common payment data response.
	 *
	 * @var CommonPaymentDataResponse
	 */
	public $common_payment_data;

	/**
	 * Common payment data response.
	 *
	 * @var InitiationResponseLinks
	 */
	public $links;

	/**
	 * Construct payment initiation response.
	 *
	 * @param CommonPaymentDataResponse $common_payment_data Common payment data response.
	 * @param InitiationResponseLinks   $links               Initiation response links.
	 */
	public function __construct( $common_payment_data, $links ) {
		$this->common_payment_data = $common_payment_data;
		$this->links               = $links;
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
			CommonPaymentDataResponse::from_object( $object_access->get_property( 'CommonPaymentData' ) ),
			InitiationResponseLinks::from_object( $object_access->get_property( 'Links' ) )
		);
	}
}
