<?php
/**
 * Debtor account
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Debtor account class
 */
final class DebtorAccount {
	/**
	 * Identification.
	 *
	 * @var string
	 */
	public $identification;

	/**
	 * Scheme name.
	 *
	 * @var string|null
	 */
	public $scheme_name;

	/**
	 * Construct debtor account.
	 *
	 * @param string $identification Identification.
	 */
	public function __construct( $identification ) {
		$this->identification = $identification;
	}

	/**
	 * Create debtor account from object.
	 *
	 * @param object $data Object.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$object = new self(
			$object_access->get_string( 'Identification' )
		);

		if ( $object_access->has_property( 'SchemeName' ) ) {
			$object->scheme_name = $object_access->get_string( 'SchemeName' );
		}

		return $object;
	}
}
