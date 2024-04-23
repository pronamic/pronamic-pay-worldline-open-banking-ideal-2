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
	 * ASPSP/iDEAL Hub Payment ID.
	 *
	 * @var string|null
	 */
	public $aspsp_payment_id;

	/**
	 * Debtor information response.
	 *
	 * @var DebtorInformationResponse|null
	 */
	public $debtor_information;

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
	 * Create common payment data response from object.
	 *
	 * @param object $data Object.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$object = new self(
			$object_access->get_string( 'PaymentStatus' ),
			$object_access->get_string( 'PaymentId' ),
		);

		if ( $object_access->has_property( 'AspspPaymentId' ) ) {
			$object->aspsp_payment_id = $object_access->get_string( 'AspspPaymentId' );
		}

		if ( $object_access->has_property( 'DebtorInformation' ) ) {
			$object->debtor_information = DebtorInformationResponse::from_object( $object_access->get_property( 'DebtorInformation' ) );
		}

		return $object;
	}
}
