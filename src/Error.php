<?php
/**
 * Error
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Error class
 */
final class Error extends \Exception {
	/**
	 * Error code.
	 *
	 * @var string
	 */
	public $error_code;

	/**
	 * Error message.
	 *
	 * @var string
	 */
	public $error_message;

	/**
	 * Details.
	 * 
	 * @var string|null
	 */
	public $details;

	/**
	 * Construct error.
	 *
	 * @param string      $error_code    Code.
	 * @param string      $error_message Message.
	 * @param string|null $details       Details.
	 */
	public function __construct( $error_code, $error_message, $details = null ) {
		$message = ( null === $details ) ? $error_message : $error_message . ' - ' . $details;
		$code    = (int) $error_code;

		parent::__construct( $message, $code );

		$this->error_code    = $error_code;
		$this->error_message = $error_message;
		$this->details       = $details;
	}

	/**
	 * Create error from object.
	 * 
	 * @param object $data Object.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$details = $object_access->has_property( 'details' ) ? $object_access->get_string( 'details' ) : null;

		$error = new self(
			$object_access->get_string( 'code' ),
			$object_access->get_string( 'message' ),
			$details
		);

		return $error;
	}
}
