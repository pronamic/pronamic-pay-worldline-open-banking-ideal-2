<?php
/**
 * Token response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use JsonSerializable;

/**
 * Token response class
 */
final class TokenResponse implements JsonSerializable {
	/**
	 * Access token.
	 *
	 * @var string
	 */
	public $access_token;

	/**
	 * Token type.
	 *
	 * @var string
	 */
	private $token_type;

	/**
	 * Expires in.
	 *
	 * @var int
	 */
	private $expires_in;

	/**
	 * Construct token response.
	 *
	 * @param string $access_token Access token.
	 * @param string $token_type   Token type.
	 * @param int    $expires_in   Expires in.
	 */
	public function __construct( $access_token, $token_type, $expires_in ) {
		$this->access_token = $access_token;
		$this->token_type   = $token_type;
		$this->expires_in   = $expires_in;
	}

	/**
	 * JSON serialize.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return (object) [
			'access_token' => $this->access_token,
			'token_type'   => $this->token_type,
			'expires_in'   => $this->expires_in,
		];
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
			$object_access->get_string( 'access_token' ),
			$object_access->get_string( 'token_type' ),
			$object_access->get_int( 'expires_in' )
		);
	}
}
