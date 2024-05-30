<?php
/**
 * Debtor information
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use JsonSerializable;

/**
 * Debtor information class
 */
final class DebtorInformation implements JsonSerializable {
	/**
	 * Agent.
	 *
	 * @var string|null
	 */
	public ?string $agent;

	/**
	 * JSON serialize.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return (object) [
			'Agent' => $this->agent,
		];
	}
}
