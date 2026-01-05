<?php
/**
 * Payment status request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use DateTimeImmutable;

/**
 * Payment initiation request class
 */
final class PaymentStatusRequest extends Request {
	/**
	 * Construct status request.
	 *
	 * @link https://financial-services.developer.worldline.com/node/274#operation/paymentStatus
	 * @param string $payment_id Payment ID.
	 */
	public function __construct( $payment_id ) {
		parent::__construct( 'GET', '/xs2a/routingservice/services/ob/pis/v3/payments/' . $payment_id . '/status' );
	}

	/**
	 * Get headers.
	 *
	 * @return array<string, string>
	 */
	public function get_headers() {
		return [
			'Digest'                => 'SHA-256=' . $this->get_digest(),
			'X-Request-ID'          => $this->request_id,
			'MessageCreateDateTime' => $this->date->format( \DATE_ATOM ),
		];
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
}
