<?php
/**
 * Remittance information structured
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use JsonSerializable;

/**
 * Remittance information structured class
 */
final class RemittanceInformationStructured implements JsonSerializable {
	/**
	 * Reference.
	 *
	 * @var string
	 */
	public string $reference;

	/**
	 * Construct remittance information structured.
	 *
	 * @param string $reference Reference.
	 */
	public function __construct( string $reference ) {
		$this->reference = $reference;
	}

	/**
	 * JSON serialize.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return (object) [
			'Reference' => $this->reference,
		];
	}
}
