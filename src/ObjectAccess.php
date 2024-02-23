<?php
/**
 * Object access
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

/**
 * Object access class
 */
class ObjectAccess {
	/**
	 * Object.
	 *
	 * @var object Object.
	 */
	private $value;

	/**
	 * Construct object access.
	 *
	 * @param object $value Object.
	 */
	public function __construct( object $value ) {
		$this->value = $value;
	}

	/**
	 * Checks if the object has a property.
	 *
	 * @param string $property Property.
	 * @return bool True if the property exists, false if it doesn't exist.
	 */
	public function has_property( string $property ) {
		return \property_exists( $this->value, $property );
	}

	/**
	 * Get property.
	 *
	 * @param string $property Property.
	 * @return mixed
	 * @throws \Exception Throws exception when property does not exists.
	 */
	public function get_property( string $property ) {
		if ( ! \property_exists( $this->value, $property ) ) {
			throw new \Exception(
				\sprintf(
					'Object does not have `%s` property.',
					\esc_html( $property )
				)
			);
		}

		return $this->value->{$property};
	}

	/**
	 * Get string.
	 *
	 * @param string $property Property.
	 * @return string
	 * @throws \Exception Throws exception when property is not a string.
	 */
	public function get_string( string $property ) {
		$value = $this->get_property( $property );

		if ( ! \is_string( $value ) ) {
			throw new \Exception(
				\sprintf(
					'Property `%s` must be a string.',
					\esc_html( $property )
				)
			);
		}

		return $value;
	}

	/**
	 * Get integer.
	 *
	 * @param string $property Property.
	 * @return int
	 * @throws \Exception Throws exception when property is not an integer.
	 */
	public function get_int( string $property ) {
		$value = $this->get_property( $property );

		if ( ! \is_numeric( $value ) ) {
			throw new \Exception(
				\sprintf(
					'Property `%s` must be numeric.',
					\esc_html( $property )
				)
			);
		}

		return (int) $value;
	}

	/**
	 * Get access.
	 *
	 * @param string $property Property.
	 * @return self
	 */
	public function get_access( string $property ) {
		return new self( $this->get_property( $property ) );
	}
}
