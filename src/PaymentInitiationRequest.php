<?php
/**
 * Payment initiation request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use DateTimeImmutable;
use JsonSerializable;

/**
 * Payment initiation request class
 */
final class PaymentInitiationRequest extends Request implements JsonSerializable {
	/**
	 * Common payment data.
	 *
	 * @var PaymentInitiationRequestBasic
	 */
	private $common_payment_data;

	/**
	 * The callback URL for the redirection back to the initiating party after authorization.
	 * 
	 * This is equivalent to the Merchant Return URL from iDEAL 1.0. This is the URL where
	 * the User is returned to once the payment has been completed. There is an option to
	 * provide this in the MSP Portal, or as an alternative to be provided in the API request.
	 * The value provided in the API request will take precedence to the value in the MSP
	 * Portal.
	 * 
	 * @link https://financial-services.developer.worldline.com/node/274#operation/paymentInitiate
	 * @var string|null
	 */
	public $initiating_party_return_url;

	/**
	 * Construct payment initiation request.
	 *
	 * @param PaymentInitiationRequestBasic $common_payment_data Common payment data.
	 */
	public function __construct( PaymentInitiationRequestBasic $common_payment_data ) {
		parent::__construct( 'POST', '/xs2a/routingservice/services/ob/pis/v3/payments' );

		$this->common_payment_data = $common_payment_data;
	}

	/**
	 * Get headers.
	 *
	 * @return array<string, string>
	 */
	public function get_headers() {
		$headers = [
			'Digest'                => 'SHA-256=' . $this->get_digest(),
			'X-Request-ID'          => $this->request_id,
			'MessageCreateDateTime' => $this->date->format( \DATE_ATOM ),
		];

		if ( null !== $this->initiating_party_return_url ) {
			$headers['InitiatingPartyReturnURL'] = $this->initiating_party_return_url;
		}

		return $headers;
	}

	/**
	 * Get signature headers.
	 *
	 * @return array<string, string>
	 */
	public function get_signature_headers() {
		$headers = parent::get_signature_headers();

		$headers['(request-target)'] = \strtolower( $this->method ) . ' ' . $this->endpoint;

		return $headers;
	}

	/**
	 * JSON serialize.
	 * 
	 * @return object
	 */
	public function jsonSerialize() {
		return (object) [
			'PaymentProduct'    => [
				'IDEAL',
			],
			'CommonPaymentData' => $this->common_payment_data,
			'IDEALPayments'     => [
				'UseDebtorToken' => false,
				'FlowType'       => 'Standard',
			],
		];
	}

	/**
	 * Get body.
	 *
	 * @return string
	 * @throws \Exception Throws an exception if JSON encode fails.
	 */
	public function get_body() {
		$result = \wp_json_encode( $this );

		if ( false == $result ) {
			throw new \Exception( 'An error occurred while encoding the payment initiation request to JSON.' );
		}

		return $result;
	}
}
