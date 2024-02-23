<?php
/**
 * Token request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Token request class
 */
final class TokenRequest extends Request {
	/**
	 * App.
	 *
	 * @var string
	 */
	private $app;

	/**
	 * Client.
	 *
	 * @var string
	 */
	private $client;

	/**
	 * ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Construct token request.
	 *
	 * @param string $app    App.
	 * @param string $client Client.
	 * @param string $id     ID.
	 */
	public function __construct( $app, $client, $id ) {
		parent::__construct( 'POST', '/xs2a/routingservice/services/authorize/token' );

		$this->app    = $app;
		$this->client = $client;
		$this->id     = $id;
	}

	/**
	 * Get headers.
	 * 
	 * @return array<string, string>
	 */
	public function get_headers() {
		return [
			'App'    => $this->app,
			'Client' => $this->client,
			'Id'     => $this->id,
			'Date'   => $this->date->format( \DATE_RFC1123 ),
		];
	}

	/**
	 * Get body.
	 * 
	 * @return string
	 */
	public function get_body() {
		return 'grant_type=client_credentials';
	}
}
