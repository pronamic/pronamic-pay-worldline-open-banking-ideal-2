<?php
/**
 * Initiation response links
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Initiation response links class
 */
final class InitiationResponseLinks {
	/**
	 * Redirect URL.
	 *
	 * @var string|null
	 */
	public $redirect_url;

	/**
	 * Create initiation response links from object.
	 * 
	 * @param object $data Object.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$links = new self();

		if ( $object_access->has_property( 'RedirectUrl' ) ) {
			$links->redirect_url = $object_access->get_access( 'RedirectUrl' )->get_string( 'Href' );
		}

		return $links;
	}
}
