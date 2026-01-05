<?php
/**
 * Debtor information response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Debtor information response class
 */
final class DebtorInformationResponse {
	/**
	 * Name.
	 *
	 * @var string|null
	 */
	public $name;

	/**
	 * Account.
	 *
	 * @var DebtorAccount|null
	 */
	public $account;

	/**
	 * Create debtor information response from object.
	 *
	 * @param object $data Object.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$object = new self();

		if ( $object_access->has_property( 'Name' ) ) {
			$object->name = $object_access->get_string( 'Name' );
		}

		if ( $object_access->has_property( 'Account' ) ) {
			$object->account = DebtorAccount::from_object( $object_access->get_property( 'Account' ) );
		}

		return $object;
	}
}
