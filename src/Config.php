<?php
/**
 * Config
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use JsonSerializable;
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Config class
 */
final class Config extends GatewayConfig implements JsonSerializable {
	/**
	 * Base domain.
	 * 
	 * @var string
	 */
	public string $base_domain;

	/**
	 * App.
	 * 
	 * @var string
	 */
	public string $app;

	/**
	 * Client.
	 * 
	 * @var string
	 */
	public string $client;

	/**
	 * ID.
	 * 
	 * @var string
	 */
	public string $id;

	/**
	 * Private key.
	 *
	 * @var string
	 */
	public string $private_key;

	/**
	 * Private key password.
	 *
	 * @var string
	 */
	public string $private_key_password = '';

	/**
	 * Certificate.
	 *
	 * @var string
	 */
	public string $certificate;

	/**
	 * Reference.
	 *
	 * @var string
	 */
	public string $reference = '';

	/**
	 * Construct config.
	 * 
	 * @param string $base_domain Base domain.
	 * @param string $app         App.
	 * @param string $client      Client.
	 * @param string $id          ID.
	 * @param string $private_key Private key.
	 * @param string $certificate Certificate.
	 */
	public function __construct(
		string $base_domain,
		string $app,
		string $client,
		string $id,
		string $private_key,
		string $certificate
	) {
		$this->base_domain = $base_domain;
		$this->app         = $app;
		$this->client      = $client;
		$this->id          = $id;
		$this->private_key = $private_key;
		$this->certificate = $certificate;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @link https://www.w3.org/TR/json-ld11/#specifying-the-type
	 * @return object
	 */
	public function jsonSerialize(): object {
		return (object) [
			'@type'                => __CLASS__,
			'base_domain'          => $this->base_domain,
			'app'                  => $this->app,
			'client'               => $this->client,
			'id'                   => $this->id,
			'private_key'          => $this->private_key,
			'private_key_password' => $this->private_key_password,
			'certificate'          => $this->certificate,
		];
	}
}
