<?php
/**
 * Payment initiation request basic
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use JsonSerializable;

/**
 * Payment initiation request basic class
 */
final class PaymentInitiationRequestBasic implements JsonSerializable {
	/**
	 * Amount.
	 * 
	 * @var string
	 */
	public string $amount;

	/**
	 * Remittance information.
	 * 
	 * @var string|null
	 */
	public $remittance_information;

	/**
	 * Remittance information structured.
	 * 
	 * @var RemittanceInformationStructured|null
	 */
	public $remittance_information_structured;

	/**
	 * Construct payment initiation request basic.
	 * 
	 * @param string $amount Amount.
	 */
	public function __construct( string $amount ) {
		$this->amount = $amount;
	}

	/**
	 * JSON serialize.
	 * 
	 * @return object
	 */
	public function jsonSerialize() {
		$data = [
			'Amount' => [
				'Type'     => 'Fixed',
				'Amount'   => $this->amount,
				'Currency' => 'EUR',
			],
		];

		if ( null !== $this->remittance_information ) {
			$data['RemittanceInformation'] = $this->remittance_information;
		}

		if ( null !== $this->remittance_information_structured ) {
			$data['RemittanceInformationStructured'] = $this->remittance_information_structured;
		}

		return (object) $data;
	}
}
