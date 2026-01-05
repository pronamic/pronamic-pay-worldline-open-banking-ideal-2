<?php
/**
 * Request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use DateTimeImmutable;

/**
 * Request class
 */
class Request {
	/**
	 * Method.
	 *
	 * @var string
	 */
	public $method;

	/**
	 * Endpoint.
	 *
	 * @var string
	 */
	public $endpoint;

	/**
	 * Request ID.
	 *
	 * @var string
	 */
	protected $request_id;

	/**
	 * Date.
	 *
	 * @var DateTimeImmutable
	 */
	protected $date;

	/**
	 * Construct request.
	 *
	 * @param string $method   Method.
	 * @param string $endpoint Endpoint.
	 */
	public function __construct( $method, $endpoint ) {
		$this->method   = $method;
		$this->endpoint = $endpoint;

		$this->request_id = \wp_generate_uuid4();
		$this->date       = new DateTimeImmutable();
	}

	/**
	 * Get headers.
	 *
	 * @return array<string, string>
	 */
	public function get_headers() {
		return [];
	}

	/**
	 * Get request body.
	 *
	 * @return string
	 */
	public function get_body() {
		return '';
	}

	/**
	 * Get signatures headers.
	 *
	 * @return array<string, string>
	 */
	public function get_signature_headers() {
		return \array_change_key_case( $this->get_headers(), \CASE_LOWER );
	}

	/**
	 * Get signature string.
	 *
	 * @return string
	 */
	public function get_signature_string() {
		$pieces = [];

		foreach ( $this->get_signature_headers() as $name => $value ) {
			$pieces[] = $name . ': ' . $value;
		}

		return \implode( "\n", $pieces );
	}

	/**
	 * Get digest.
	 *
	 * @return string
	 */
	public function get_digest() {
		$payload = $this->get_body();

		$digest = \base64_encode( \hash( 'sha256', $payload, true ) );

		return $digest;
	}
}
