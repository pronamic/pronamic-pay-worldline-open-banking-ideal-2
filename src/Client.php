<?php
/**
 * Client
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use OpenSSLAsymmetricKey;
use Pronamic\WordPress\Http\Response;
use Pronamic\WordPress\Http\Facades\Http;
use Pronamic\WordPress\Pay\Gateways\IDealAdvancedV3\Certificate;

/**
 * Client class
 */
final class Client {
	/**
	 * Config.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Construct client.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * Get signature for request.
	 *
	 * @param Request $request Request.
	 * @return string
	 * @throws \Exception Throws an exception if OpenSSL cannot generate a signature.
	 */
	private function get_signature( Request $request ) {
		$private_key = \openssl_pkey_get_private( $this->config->private_key, $this->config->private_key_password );

		if ( false === $private_key ) {
			throw new \Exception( 'Could not get private key: ' . \esc_html( (string) \openssl_error_string() ) );
		}

		$string_to_sign = $request->get_signature_string();

		$result = \openssl_sign( $string_to_sign, $signature, $private_key, 'sha256WithRSAEncryption' );

		if ( false === $result ) {
			throw new \Exception( 'Could not sign: ' . \esc_html( (string) \openssl_error_string() ) );
		}

		return \sprintf(
			'keyId="%s", algorithm="SHA256withRSA", headers="%s", signature="%s"',
			\openssl_x509_fingerprint( $this->config->certificate ),
			\implode( ' ', \array_keys( $request->get_signature_headers() ) ),
			\base64_encode( $signature )
		);
	}

	/**
	 * Get URL.
	 * 
	 * @param Request $request Request.
	 * @return string
	 */
	private function get_url( Request $request ) {
		return $this->config->base_domain . $request->endpoint;
	}

	/**
	 * Get access token.
	 *
	 * @return TokenResponse
	 * @throws Error Throws an exception if no access token can be obtained.
	 */
	private function get_access_token() {
		$request = new TokenRequest(
			$this->config->app,
			$this->config->client,
			$this->config->id
		);

		$headers = $request->get_headers();

		$headers['Authorization'] = 'Signature ' . $this->get_signature( $request );

		$result = Http::request(
			$this->get_url( $request ),
			[
				'method'  => $request->method,
				'headers' => $headers,
				'body'    => $request->get_body(),
			]
		);

		if ( '200' !== (string) $result->status() ) {
			$error = Error::from_object( $result->json() );

			throw $error;
		}

		return TokenResponse::from_object( $result->json() );
	}

	/**
	 * Request.
	 * 
	 * @param Request $request Request.
	 * @return Response
	 */
	private function request( Request $request ) {
		$token_response = $this->get_access_token();

		$payload = $request->get_body();
		$headers = $request->get_headers();

		$headers['Authorization'] = 'Bearer ' . $token_response->access_token;
		$headers['Signature']     = $this->get_signature( $request );
		$headers['Content-Type']  = 'application/json';

		$result = Http::request(
			$this->get_url( $request ),
			[
				'method'  => $request->method,
				'headers' => $headers,
				'body'    => $request->get_body(),
			]
		);

		return $result;
	}

	/**
	 * Create payment.
	 *
	 * @link https://financial-services.developer.worldline.com/node/274#operation/paymentInitiate
	 * @param PaymentInitiationRequest $request Payment initiation request.
	 * @return PaymentInitiationResponse
	 * @throws Error Throws an exception if payment creation failed.
	 */
	public function create_payment( PaymentInitiationRequest $request ) {
		$result = $this->request( $request );

		if ( '201' !== (string) $result->status() ) {
			$error = Error::from_object( $result->json() );

			throw $error;
		}

		return PaymentInitiationResponse::from_object( $result->json() );
	}

	/**
	 * Get payment status.
	 *
	 * @param PaymentStatusRequest $request Payment status request.
	 * @return PaymentDetailedInformation
	 * @throws Error Throws an exception if requesting payment status fails.
	 */
	public function get_payment_status( PaymentStatusRequest $request ) {
		$result = $this->request( $request );

		if ( '200' !== (string) $result->status() ) {
			$error = Error::from_object( $result->json() );

			throw $error;
		}

		return PaymentDetailedInformation::from_object( $result->json() );
	}
}
