<?php
/**
 * Status
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use Pronamic\WordPress\Pay\Payments\PaymentStatus as PronamicStatus;

/**
 * Status class
 */
class Status {
	/**
	 * Status indicator for open
	 *
	 * @var string
	 */
	const OPEN = 'Open';

	/**
	 * Status indicator for completed settlement
	 *
	 * @var string
	 */
	const SETTLEMENT_COMPLETED = 'SettlementCompleted';

	/**
	 * Status indicator for cancelled
	 *
	 * @var string
	 */
	const CANCELLED = 'Cancelled';

	/**
	 * Status indicator for expired
	 *
	 * @var string
	 */
	const EXPIRED = 'Expired';

	/**
	 * Status indicator for error
	 *
	 * @var string
	 */
	const ERROR = 'Error';

	/**
	 * Transform an iDEAL status to a more general status.
	 *
	 * @param string $status iDEAL status.
	 * @return string|null Pay status.
	 */
	public static function transform_to_pronamic( $status ) {
		switch ( $status ) {
			case self::OPEN:
				return PronamicStatus::OPEN;
			case self::CANCELLED:
				return PronamicStatus::CANCELLED;
			case self::SETTLEMENT_COMPLETED:
				return PronamicStatus::SUCCESS;
			case self::EXPIRED:
				return PronamicStatus::EXPIRED;
			case self::ERROR:
				return PronamicStatus::FAILURE;
			default:
				return null;
		}
	}
}
