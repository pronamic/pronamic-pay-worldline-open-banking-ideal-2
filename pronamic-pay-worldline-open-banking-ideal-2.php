<?php
/**
 * Pronamic Pay - Worldline Open Banking - iDEAL 2.0
 *
 * @author    Pronamic
 * @copyright 2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 *
 * @wordpress-plugin
 * Plugin Name:       Pronamic Pay - Worldline Open Banking - iDEAL 2.0
 * Plugin URI:        https://wp.pronamic.directory/plugins/pronamic-pay-worldline-open-banking-ideal-2/
 * Description:       This plugin contains the Pronamic Pay integration for the Worldline Open Banking Platform and iDEAL 2.0.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author:            Pronamic
 * Author URI:        https://www.pronamic.eu/
 * Text Domain:       pronamic-pay-worldline-open-banking-ideal-2
 * Domain Path:       /languages/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://wp.pronamic.directory/plugins/pronamic-pay-worldline-open-banking-ideal-2/
 * GitHub URI:        https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Gateway.
 */
add_filter(
	'pronamic_pay_gateways',
	function ( $gateways ) {
		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking\Integration(
			[
				'id'          => 'rabobank-ideal-2-test',
				'name'        => 'Rabobank - Rabo iDEAL Professional - iDEAL 2.0 - Test',
				'mode'        => 'test',
				'base_domain' => 'https://routingservice-rabo.awltest.de',
				'app'         => 'IDEAL',
				'client'      => 'RaboiDEAL',
			]
		);

		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking\Integration(
			[
				'id'          => 'rabobank-ideal-2',
				'name'        => 'Rabobank - Rabo iDEAL Professional - iDEAL 2.0',
				'mode'        => 'live',
				'base_domain' => 'https://ideal.rabobank.nl',
				'app'         => 'IDEAL',
				'client'      => 'RaboiDEAL',
			]
		);

		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking\Integration(
			[
				'id'          => 'worldline-ideal-2-test',
				'name'        => 'WorldLine - Rabo iDEAL Professional - iDEAL 2.0 - Test',
				'mode'        => 'test',
				'base_domain' => 'https://digitalroutingservice.awltest.de',
				'app'         => 'IDEAL',
				'client'      => 'RaboiDEAL',
			]
		);

		return $gateways;
	}
);
