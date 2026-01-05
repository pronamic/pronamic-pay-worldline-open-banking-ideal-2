<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use Pronamic\WordPress\Pay\AbstractGatewayIntegration;
use Pronamic\WordPress\Pay\Core\Gateway as PronamicGateway;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Integration class
 */
final class Integration extends AbstractGatewayIntegration {
	/**
	 * Open Banking service base domain.
	 *
	 * @var string
	 */
	private $service_base_domain;

	/**
	 * Open Banking service app.
	 *
	 * @var string
	 */
	private $service_app;

	/**
	 * Open Banking service client.
	 *
	 * @var string
	 */
	private $service_client;

	/**
	 * Initiating Party ID label.
	 *
	 * @var string|null
	 */
	private $initiating_party_label;

	/**
	 * Merchant options.
	 *
	 * @var array<string, string>|null
	 */
	private $merchant_options;

	/**
	 * Construct iDEAL 2.0 integration.
	 *
	 * @param array<string, mixed> $args Arguments.
	 * @return void
	 */
	public function __construct( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'id'                     => 'ideal-2',
				'name'                   => 'iDEAL 2.0',
				'mode'                   => PronamicGateway::MODE_LIVE,
				'url'                    => 'https://www.ideal.nl/en/',
				'product_url'            => 'https://www.ideal.nl/en/',
				'manual_url'             => null,
				'dashboard_url'          => null,
				'provider'               => null,
				'app'                    => null,
				'base_url'               => null,
				'client'                 => null,
				'initiating_party_label' => null,
				'supports'               => [
					'payment_status_request',
				],
			]
		);

		parent::__construct( $args );

		$this->service_app         = $args['app'];
		$this->service_base_domain = $args['base_domain'];
		$this->service_client      = $args['client'];

		if ( \is_string( $args['initiating_party_label'] ) ) {
			$this->initiating_party_label = $args['initiating_party_label'];
		}

		if ( \array_key_exists( 'merchant_options', $args ) ) {
			$this->merchant_options = $args['merchant_options'];
		}

		$this->set_mode( $args['mode'] );

		\add_action( 'current_screen', [ $this, 'maybe_download_certificate' ] );
		\add_action( 'current_screen', [ $this, 'maybe_download_secret_key' ] );

		\add_filter( 'pronamic_pay_worldline_open_banking_redirect_url', [ $this, 'maybe_override_worldline_open_banking_redirect_url_for_test' ], 10, 2 );
	}

	/**
	 * Setup.
	 */
	public function setup() {
		\add_filter( 'pronamic_gateway_configuration_display_value_' . $this->get_id(), [ $this, 'gateway_configuration_display_value' ], 10, 2 );
	}

	/**
	 * Gateway configuration display value.
	 *
	 * @param string $display_value Display value.
	 * @param int    $post_id       Gateway configuration post ID.
	 * @return string
	 */
	public function gateway_configuration_display_value( $display_value, $post_id ) {
		$initiating_party_id = (string) $this->get_meta( $post_id, 'worldline_initiating_party_id' );

		return $initiating_party_id;
	}

	/**
	 * Get settings fields.
	 *
	 * @return array<int, array<string, mixed>>>
	 */
	public function get_settings_fields() {
		$fields = parent::get_settings_fields();

		// Merchant ID.
		$merchant_field = [
			'section'  => 'general',
			'title'    => null === $this->initiating_party_label ? \__( 'Initiating Party ID', 'pronamic-pay-worldline-open-banking-ideal-2' ) : $this->initiating_party_label,
			'meta_key' => '_pronamic_gateway_worldline_initiating_party_id',
			'type'     => 'text',
			'classes'  => [ 'code' ],
			'tooltip'  => \__( 'The parameter for Initiating Party ID as mentioned in the payment provider dashboard.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
		];

		if ( null !== $this->merchant_options ) {
			$merchant_field['type']    = 'select';
			$merchant_field['options'] = $this->merchant_options;
		}

		$fields[] = $merchant_field;

		// Sub ID.
		$fields[] = [
			'section'  => 'advanced',
			'meta_key' => '_pronamic_gateway_ideal_sub_id',
			'id'       => 'pronamic_ideal_sub_id',
			'title'    => __( 'Sub ID', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			'type'     => 'text',
			'classes'  => [ 'small-text', 'code' ],
			'default'  => '',
			'tooltip'  => sprintf(
				'%s %s.',
				__( 'Sub ID', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				__( 'as mentioned in the payment provider dashboard', 'pronamic-pay-worldline-open-banking-ideal-2' )
			),
		];

		// Purchase ID.
		$fields[] = [
			'section'     => 'advanced',
			'meta_key'    => '_pronamic_gateway_ideal_purchase_id',
			'title'       => __( 'Purchase ID', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			'type'        => 'text',
			'classes'     => [ 'regular-text', 'code' ],
			'tooltip'     => sprintf(
				/* translators: %s: <code>purchaseID</code> */
				__( 'The iDEAL %s parameter.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				sprintf( '<code>%s</code>', 'purchaseID' )
			),
			'description' => sprintf(
				'%s %s<br />%s',
				__( 'Available tags:', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				sprintf(
					'<code>%s</code> <code>%s</code>',
					'{order_id}',
					'{payment_id}'
				),
				sprintf(
					/* translators: %s: default code */
					__( 'Default: <code>%s</code>', 'pronamic-pay-worldline-open-banking-ideal-2' ),
					'{payment_id}'
				)
			),
		];

		if ( PronamicGateway::MODE_TEST !== $this->get_mode() ) {
			/*
			 * Secret key and certificate
			 */

			// Secret key and certificate information.
			$fields[] = [
				'section'  => 'general',
				'title'    => __( 'Secret key and certificate', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'     => 'description',
				'callback' => [ $this, 'field_security' ],
			];

			// Organization.
			$fields[] = [
				'section'  => 'general',
				'group'    => 'pk-cert',
				'meta_key' => '_pronamic_gateway_organization',
				'title'    => __( 'Organization', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'     => 'text',
				'tooltip'  => __( 'Organization name, e.g. Pronamic', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			];

			// Organization Unit.
			$fields[] = [
				'section'  => 'general',
				'group'    => 'pk-cert',
				'meta_key' => '_pronamic_gateway_organization_unit',
				'title'    => __( 'Organization Unit', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'     => 'text',
				'tooltip'  => __( 'Organization unit, e.g. Administration', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			];

			// Locality.
			$fields[] = [
				'section'  => 'general',
				'group'    => 'pk-cert',
				'meta_key' => '_pronamic_gateway_locality',
				'title'    => __( 'City', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'     => 'text',
				'tooltip'  => __( 'City, e.g. Amsterdam', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			];

			// State or Province.
			$fields[] = [
				'section'  => 'general',
				'group'    => 'pk-cert',
				'meta_key' => '_pronamic_gateway_state_or_province',
				'title'    => __( 'State / province', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'     => 'text',
				'tooltip'  => __( 'State or province, e.g. Friesland', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			];

			// Country.
			$locale = \explode( '_', \get_locale() );

			$locale = count( $locale ) > 1 ? $locale[1] : $locale[0];

			$fields[] = [
				'section'     => 'general',
				'group'       => 'pk-cert',
				'meta_key'    => '_pronamic_gateway_country',
				'title'       => __( 'Country', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'        => 'text',
				'tooltip'     => sprintf(
					'%s %s (ISO-3166-1 alpha-2)',
					__( '2 letter country code, e.g.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
					strtoupper( $locale )
				),
				'size'        => 2,
				'description' => sprintf(
					'%s %s',
					__( '2 letter country code, e.g.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
					strtoupper( $locale )
				),
			];

			// Email Address.
			$fields[] = [
				'section'  => 'general',
				'group'    => 'pk-cert',
				'meta_key' => '_pronamic_gateway_email',
				'title'    => __( 'E-mail address', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'tooltip'  => sprintf(
					/* translators: %s: admin email */
					__( 'E-mail address, e.g. %s', 'pronamic-pay-worldline-open-banking-ideal-2' ),
					(string) get_option( 'admin_email' )
				),
				'type'     => 'text',
			];

			// Number Days Valid.
			$fields[] = [
				'section'  => 'general',
				'filter'   => \FILTER_SANITIZE_NUMBER_INT,
				'group'    => 'pk-cert',
				'meta_key' => '_pronamic_gateway_number_days_valid',
				'title'    => __( 'Number Days Valid', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'     => 'text',
				'default'  => 1825,
				'tooltip'  => __( 'Number of days the generated certificate will be valid for, e.g. 1825 days for the maximum duration of 5 years.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			];

			// Secret Key Password.
			$fields[] = [
				'section'  => 'general',
				'group'    => 'pk-cert',
				'meta_key' => '_pronamic_gateway_ideal_private_key_password',
				'title'    => __( 'Secret Key Password', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'type'     => 'text',
				'classes'  => [ 'regular-text', 'code' ],
				'default'  => wp_generate_password(),
				'tooltip'  => __( 'A random password which will be used for the generation of the secret key and certificate.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
				'input'    => function ( $name ) {
					// phpcs:disable WordPress.Security.NonceVerification.Missing

					if ( ! \array_key_exists( $name, $_POST ) ) {
						return '';
					}

					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password can contain whitespace, HTML tags and percent-encoded characters.
					return $_POST[ $name ];

					// phpcs:enable WordPress.Security.NonceVerification.Missing
				},
			];
		}

		// Private Key.
		$fields[] = [
			'section'  => 'general',
			'group'    => 'pk-cert',
			'title'    => __( 'Private Key', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			'meta_key' => PronamicGateway::MODE_TEST === $this->get_mode() ? null : '_pronamic_gateway_ideal_private_key',
			'value'    => PronamicGateway::MODE_TEST === $this->get_mode() ? trim( (string) \file_get_contents( __DIR__ . '/../certificates/TestCertificatesiDEAL.2.0.key', true ) ) : '',
			'readonly' => PronamicGateway::MODE_TEST === $this->get_mode(),
			'type'     => 'textarea',
			'callback' => [ $this, 'field_private_key' ],
			'classes'  => [ 'code' ],
			'tooltip'  => __( 'The secret key is used for secure communication with the payment provider. If left empty, the secret key will be generated using the given secret key password.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
		];

		// Certificate.
		$fields[] = [
			'section'  => 'general',
			'group'    => 'pk-cert',
			'title'    => __( 'Certificate', 'pronamic-pay-worldline-open-banking-ideal-2' ),
			'meta_key' => PronamicGateway::MODE_TEST === $this->get_mode() ? null : '_pronamic_gateway_ideal_private_certificate',
			'value'    => PronamicGateway::MODE_TEST === $this->get_mode() ? trim( (string) \file_get_contents( __DIR__ . '/../certificates/TestCertificatesiDEAL.2.0.pem', true ) ) : '',
			'readonly' => PronamicGateway::MODE_TEST === $this->get_mode(),
			'type'     => 'textarea',
			'callback' => [ $this, 'field_certificate' ],
			'classes'  => [ 'code' ],
			'tooltip'  => __( 'The certificate is used for secure communication with the payment provider. If left empty, the certificate will be generated using the secret key and given organization details.', 'pronamic-pay-worldline-open-banking-ideal-2' ),
		];

		// Return.
		return $fields;
	}

	/**
	 * Field security
	 *
	 * @param array<string, mixed> $field Field.
	 * @return void
	 */
	public function field_security( $field ) {
		$post_id = (int) \get_the_ID();

		$certificate = $this->get_meta( $post_id, 'ideal_private_certificate' );

		?>
		<p>
			<?php if ( empty( $certificate ) ) : ?>

				<span class="dashicons dashicons-no"></span> <?php esc_html_e( 'The secret key and certificate have not yet been configured.', 'pronamic-pay-worldline-open-banking-ideal-2' ); ?>
				<br/>

				<br/>

				<?php esc_html_e( 'A secret key and certificate are required for communication with the payment provider. Enter the organization details from the iDEAL account below to generate these required files.', 'pronamic-pay-worldline-open-banking-ideal-2' ); ?>

			<?php else : ?>

				<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'A secret key and certificate have been configured. The certificate must be uploaded to the payment provider dashboard to complete configuration.', 'pronamic-pay-worldline-open-banking-ideal-2' ); ?>
				<br/>

				<br/>

				<?php

				\wp_nonce_field( 'pronamic_pay_download_certificate', 'pronamic_pay_download_certificate_nonce' );

				\printf(
					'<input type="submit" id="%2$s" name="%2$s" value="%1$s" class="button" />',
					\esc_attr( \__( 'Download certificate', 'pronamic-pay-worldline-open-banking-ideal-2' ) ),
					\esc_attr( 'download_certificate' )
				);

				?>

			<?php endif; ?>
		</p>
		<?php
	}

	/**
	 * Field private key.
	 *
	 * @param array<string, mixed> $field Field.
	 * @return void
	 */
	public function field_private_key( $field ) {
		$post_id = (int) \get_the_ID();

		$private_key          = $this->get_meta( $post_id, 'ideal_private_key' );
		$private_key_password = $this->get_meta( $post_id, 'ideal_private_key_password' );
		$number_days_valid    = $this->get_meta( $post_id, 'number_days_valid' );

		if ( PronamicGateway::MODE_TEST === $this->get_mode() ) {
			$private_key = \file_get_contents( __DIR__ . '/../certificates/TestCertificatesiDEAL.2.0.key' );
		}

		if ( PronamicGateway::MODE_TEST !== $this->get_mode() ) {
			if ( ! empty( $private_key_password ) && ! empty( $number_days_valid ) ) {
				if ( \function_exists( '\escapeshellarg' ) ) {
					$filename = __( 'ideal.key', 'pronamic-pay-worldline-open-banking-ideal-2' );

					$command = sprintf(
						'openssl genrsa -aes128 -out %s -passout pass:%s 2048',
						\escapeshellarg( $filename ),
						\escapeshellarg( $private_key_password )
					);

					?>

					<p><?php esc_html_e( 'OpenSSL command', 'pronamic-pay-worldline-open-banking-ideal-2' ); ?></p>
					<input id="pronamic_ideal_openssl_command_key" name="pronamic_ideal_openssl_command_key" value="<?php echo esc_attr( $command ); ?>" type="text" class="large-text code" readonly="readonly"/>

					<?php
				}
			} else {
				printf(
					'<p class="pronamic-pay-description description">%s</p>',
					esc_html__( 'Leave empty and save the configuration to generate the secret key or view the OpenSSL command.', 'pronamic-pay-worldline-open-banking-ideal-2' )
				);
			}
		}

		?>
		<p>
			<?php

			if ( ! empty( $private_key ) ) {
				\wp_nonce_field( 'pronamic_pay_download_secret_key', 'pronamic_pay_download_secret_key_nonce' );

				\printf(
					'<input type="submit" id="%2$s" name="%2$s" value="%1$s" class="button" />',
					\esc_attr( \__( 'Download', 'pronamic-pay-worldline-open-banking-ideal-2' ) ),
					\esc_attr( 'download_secret_key' )
				);

				echo ' ';
			}

			if ( PronamicGateway::MODE_TEST !== $this->get_mode() ) {
				printf(
					'<label class="pronamic-pay-form-control-file-button button">%s <input type="file" name="%s" /></label>',
					esc_html__( 'Upload', 'pronamic-pay-worldline-open-banking-ideal-2' ),
					'_pronamic_gateway_ideal_private_key_file'
				);
			}

			?>
		</p>
		<?php
	}

	/**
	 * Field certificate.
	 *
	 * @param array<string, mixed> $field Field.
	 * @return void
	 */
	public function field_certificate( $field ) {
		$post_id = (int) \get_the_ID();

		$certificate = $this->get_meta( $post_id, 'ideal_private_certificate' );

		if ( PronamicGateway::MODE_TEST === $this->get_mode() ) {
			$certificate = \file_get_contents( __DIR__ . '/../certificates/TestCertificatesiDEAL.2.0.pem' );
		}

		if ( PronamicGateway::MODE_TEST !== $this->get_mode() ) {
			$private_key_password = $this->get_meta( $post_id, 'ideal_private_key_password' );
			$number_days_valid    = $this->get_meta( $post_id, 'number_days_valid' );

			$filename_key = __( 'ideal.key', 'pronamic-pay-worldline-open-banking-ideal-2' );
			$filename_cer = __( 'ideal.cer', 'pronamic-pay-worldline-open-banking-ideal-2' );

			// @link http://www.openssl.org/docs/apps/req.html
			$subj_args = [
				'C'            => $this->get_meta( $post_id, 'country' ),
				'ST'           => $this->get_meta( $post_id, 'state_or_province' ),
				'L'            => $this->get_meta( $post_id, 'locality' ),
				'O'            => $this->get_meta( $post_id, 'organization' ),
				'OU'           => $this->get_meta( $post_id, 'organization_unit' ),
				'CN'           => $this->get_meta( $post_id, 'organization' ),
				'emailAddress' => $this->get_meta( $post_id, 'email' ),
			];

			$subj_args = array_filter( $subj_args );

			$subj = '';

			foreach ( $subj_args as $type => $value ) {
				$subj .= '/' . $type . '=' . addslashes( $value );
			}

			if ( ! empty( $subj ) ) {
				if ( \function_exists( '\escapeshellarg' ) ) {
					$command = \trim(
						\sprintf(
							'openssl req -x509 -sha256 -new -key %s -passin pass:%s -days %s -out %s %s',
							\escapeshellarg( $filename_key ),
							\escapeshellarg( $private_key_password ),
							\escapeshellarg( $number_days_valid ),
							\escapeshellarg( $filename_cer ),
							\sprintf( '-subj %s', \escapeshellarg( $subj ) )
						)
					);

					?>

					<p><?php \esc_html_e( 'OpenSSL command', 'pronamic-pay-worldline-open-banking-ideal-2' ); ?></p>
					<input id="pronamic_ideal_openssl_command_certificate" name="pronamic_ideal_openssl_command_certificate" value="<?php echo \esc_attr( $command ); ?>" type="text" class="large-text code" readonly="readonly"/>

					<?php
				}
			} else {
				printf(
					'<p class="pronamic-pay-description description">%s</p>',
					esc_html__( 'Leave empty and save the configuration to generate the certificate or view the OpenSSL command.', 'pronamic-pay-worldline-open-banking-ideal-2' )
				);
			}
		}

		if ( ! empty( $certificate ) ) {
			$fingerprint = (string) \openssl_x509_fingerprint( $certificate );
			$fingerprint = \str_split( $fingerprint, 2 );
			$fingerprint = \implode( ':', $fingerprint );

			echo '<dl>';

			echo '<dt>', \esc_html__( 'SHA Fingerprint', 'pronamic-pay-worldline-open-banking-ideal-2' ), '</dt>';
			echo '<dd>', \esc_html( $fingerprint ), '</dd>';

			$info = \openssl_x509_parse( $certificate );

			if ( $info ) {
				$date_format = __( 'M j, Y @ G:i', 'pronamic-pay-worldline-open-banking-ideal-2' );

				if ( isset( $info['validFrom_time_t'] ) ) {
					echo '<dt>', \esc_html__( 'Valid From', 'pronamic-pay-worldline-open-banking-ideal-2' ), '</dt>';
					echo '<dd>', \esc_html( date_i18n( $date_format, $info['validFrom_time_t'] ) ), '</dd>';
				}

				if ( isset( $info['validTo_time_t'] ) ) {
					echo '<dt>', \esc_html__( 'Valid To', 'pronamic-pay-worldline-open-banking-ideal-2' ), '</dt>';
					echo '<dd>', \esc_html( date_i18n( $date_format, $info['validTo_time_t'] ) ), '</dd>';
				}
			}

			echo '</dl>';
		}

		?>
		<p>
			<?php

			if ( ! empty( $certificate ) ) {
				\wp_nonce_field( 'pronamic_pay_download_certificate', 'pronamic_pay_download_certificate_nonce' );


				\printf(
					'<input type="submit" id="%2$s" name="%2$s" value="%1$s" class="button" />',
					\esc_attr( \__( 'Download', 'pronamic-pay-worldline-open-banking-ideal-2' ) ),
					\esc_attr( 'download_certificate' )
				);

				echo ' ';
			}

			if ( PronamicGateway::MODE_TEST !== $this->get_mode() ) {
				printf(
					'<label class="pronamic-pay-form-control-file-button button">%s <input type="file" name="%s" /></label>',
					esc_html__( 'Upload', 'pronamic-pay-worldline-open-banking-ideal-2' ),
					'_pronamic_gateway_ideal_certificate_file'
				);
			}

			?>
		</p>
		<?php
	}

	/**
	 * Download certificate.
	 *
	 * @return void
	 */
	public function maybe_download_certificate() {
		if ( ! \array_key_exists( 'download_certificate', $_POST ) ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_pay_download_certificate_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_text_field( \wp_unslash( $_POST['pronamic_pay_download_certificate_nonce'] ) );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_pay_download_certificate' ) ) {
			return;
		}

		if ( ! \array_key_exists( 'post_ID', $_POST ) ) {
			return;
		}

		$post_id = \sanitize_text_field( \wp_unslash( $_POST['post_ID'] ) );

		$config = $this->get_config( (int) $post_id );

		$filename = sprintf( 'ideal-certificate-%s.cer', $post_id );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: application/x-x509-ca-cert; charset=' . get_option( 'blog_charset' ), true );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $config->certificate;

		exit;
	}

	/**
	 * Download secret key.
	 *
	 * @return void
	 */
	public function maybe_download_secret_key() {
		if ( ! \array_key_exists( 'download_secret_key', $_POST ) ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_pay_download_secret_key_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_text_field( \wp_unslash( $_POST['pronamic_pay_download_secret_key_nonce'] ) );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_pay_download_secret_key' ) ) {
			return;
		}

		if ( ! \array_key_exists( 'post_ID', $_POST ) ) {
			return;
		}

		$post_id = \sanitize_text_field( \wp_unslash( $_POST['post_ID'] ) );

		$config = $this->get_config( (int) $post_id );

		$filename = sprintf( 'ideal-secret-key-%s.key', $post_id );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: application/pgp-keys; charset=' . get_option( 'blog_charset' ), true );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $config->private_key;

		exit;
	}

	/**
	 * Save post.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_post( $post_id ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing

		// Files.
		$files = [
			'_pronamic_gateway_ideal_private_key_file' => '_pronamic_gateway_ideal_private_key',
			'_pronamic_gateway_ideal_certificate_file' => '_pronamic_gateway_ideal_private_certificate',
		];

		foreach ( $files as $name => $meta_key ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			if ( isset( $_FILES[ $name ] ) && UPLOAD_ERR_OK === $_FILES[ $name ]['error'] ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$value = file_get_contents( $_FILES[ $name ]['tmp_name'] );

				update_post_meta( $post_id, $meta_key, $value );
			}
		}

		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// Generate private key and certificate.
		$private_key          = get_post_meta( $post_id, '_pronamic_gateway_ideal_private_key', true );
		$private_key_password = get_post_meta( $post_id, '_pronamic_gateway_ideal_private_key_password', true );

		if ( empty( $private_key_password ) ) {
			// Without private key password we can't create private key and certificate.
			return;
		}

		if ( ! in_array( 'aes-128-cbc', openssl_get_cipher_methods(), true ) ) {
			// Without AES-128-CBC cipher method we can't create private key and certificate.
			return;
		}

		$args = [
			'digest_alg'             => 'SHA256',
			'private_key_bits'       => 2048,
			'private_key_type'       => \OPENSSL_KEYTYPE_RSA,
			'encrypt_key'            => true,
			'subjectKeyIdentifier'   => 'hash',
			'authorityKeyIdentifier' => 'keyid:always,issuer:always',
			'basicConstraints'       => 'CA:true',
		];

		// Private key.
		$pkey = \openssl_pkey_get_private( $private_key, $private_key_password );

		if ( false === $pkey ) {
			// If we can't open the private key we will create a new private key and certificate.
			if ( defined( 'OPENSSL_CIPHER_AES_128_CBC' ) ) {
				$args['encrypt_key_cipher'] = \OPENSSL_CIPHER_AES_128_CBC;
			} elseif ( defined( 'OPENSSL_CIPHER_3DES' ) ) {
				// @link https://www.pronamic.nl/wp-content/uploads/2011/12/iDEAL_Advanced_PHP_EN_V2.2.pdf
				$args['encrypt_key_cipher'] = \OPENSSL_CIPHER_3DES;
			} else {
				// Unable to create private key without cipher.
				return;
			}

			$pkey = openssl_pkey_new( $args );

			if ( false === $pkey ) {
				return;
			}

			// Export key.
			$result = openssl_pkey_export( $pkey, $private_key, $private_key_password, $args );

			if ( false === $result ) {
				return;
			}

			update_post_meta( $post_id, '_pronamic_gateway_ideal_private_key', $private_key );

			// Delete certificate since this is no longer valid.
			delete_post_meta( $post_id, '_pronamic_gateway_ideal_private_certificate' );
		}

		// Certificate.
		$certificate       = get_post_meta( $post_id, '_pronamic_gateway_ideal_private_certificate', true );
		$number_days_valid = get_post_meta( $post_id, '_pronamic_gateway_number_days_valid', true );

		if ( empty( $certificate ) ) {
			$required_keys = [
				'countryName',
				'stateOrProvinceName',
				'localityName',
				'organizationName',
				'commonName',
				'emailAddress',
			];

			$distinguished_name = [
				'countryName'            => get_post_meta( $post_id, '_pronamic_gateway_country', true ),
				'stateOrProvinceName'    => get_post_meta( $post_id, '_pronamic_gateway_state_or_province', true ),
				'localityName'           => get_post_meta( $post_id, '_pronamic_gateway_locality', true ),
				'organizationName'       => get_post_meta( $post_id, '_pronamic_gateway_organization', true ),
				'organizationalUnitName' => get_post_meta( $post_id, '_pronamic_gateway_organization_unit', true ),
				'commonName'             => get_post_meta( $post_id, '_pronamic_gateway_organization', true ),
				'emailAddress'           => get_post_meta( $post_id, '_pronamic_gateway_email', true ),
			];

			$distinguished_name = array_filter( $distinguished_name );

			/*
			 * Create certificate only if distinguished name contains all required elements
			 *
			 * @link http://stackoverflow.com/questions/13169588/how-to-check-if-multiple-array-keys-exists
			 */
			if ( count( array_intersect_key( array_flip( $required_keys ), $distinguished_name ) ) === count( $required_keys ) ) {
				// Determine cipher.
				if ( defined( 'OPENSSL_CIPHER_AES_128_CBC' ) ) {
					$args['encrypt_key_cipher'] = \OPENSSL_CIPHER_AES_128_CBC;
				} elseif ( defined( 'OPENSSL_CIPHER_3DES' ) ) {
					// @link https://www.pronamic.nl/wp-content/uploads/2011/12/iDEAL_Advanced_PHP_EN_V2.2.pdf
					$args['encrypt_key_cipher'] = \OPENSSL_CIPHER_3DES;
				} else {
					// Unable to create certificate without cipher.
					return;
				}

				$csr = openssl_csr_new( $distinguished_name, $pkey );

				if ( false !== $csr ) {
					$cert = openssl_csr_sign( $csr, null, $pkey, $number_days_valid, $args, time() );

					if ( false !== $cert ) {
						openssl_x509_export( $cert, $certificate );

						update_post_meta( $post_id, '_pronamic_gateway_ideal_private_certificate', $certificate );
					}
				}
			}
		}
	}

	/**
	 * Get config.
	 *
	 * @param int $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$mode = $this->get_mode();

		$initiating_party_id  = (string) $this->get_meta( $post_id, 'worldline_initiating_party_id' );
		$sub_id               = (string) $this->get_meta( $post_id, 'ideal_sub_id' );
		$private_key          = (string) $this->get_meta( $post_id, 'ideal_private_key' );
		$private_key_password = (string) $this->get_meta( $post_id, 'ideal_private_key_password' );
		$certificate          = (string) $this->get_meta( $post_id, 'ideal_private_certificate' );

		if ( PronamicGateway::MODE_TEST === $mode ) {
			$private_key          = (string) \file_get_contents( __DIR__ . '/../certificates/TestCertificatesiDEAL.2.0.key', true );
			$private_key_password = '';
			$certificate          = (string) \file_get_contents( __DIR__ . '/../certificates/TestCertificatesiDEAL.2.0.pem', true );
		}

		$id = ( '' === $sub_id || '0' === $sub_id ) ? $initiating_party_id : $initiating_party_id . ':' . $sub_id;

		$config = new Config(
			$this->service_base_domain,
			$this->service_app,
			$this->service_client,
			$id,
			$private_key,
			$certificate
		);

		$config->private_key_password = $private_key_password;

		$config->reference = $this->get_meta( $post_id, 'ideal_purchase_id' );

		return $config;
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		$gateway = new Gateway( $this->get_config( $post_id ) );

		$gateway->set_mode( $this->mode );

		return $gateway;
	}

	/**
	 * Maybe override iDEAL 2.0 MSP redirect URL for test.
	 *
	 * @param string  $redirect_url Redirect URL.
	 * @param Payment $payment     Payment.
	 * @return string
	 */
	public function maybe_override_worldline_open_banking_redirect_url_for_test( $redirect_url, $payment ) {
		if ( 'test' !== $payment->get_mode() ) {
			return $redirect_url;
		}

		if ( 'https://worldline.com' !== $redirect_url ) {
			return $redirect_url;
		}

		$redirect_url = \add_query_arg(
			[
				'amount'     => $payment->get_total_amount()->number_format( 2, '.', '' ),
				'return_url' => \rawurlencode( $payment->get_return_url() ),
			],
			'https://test-ideal-2.pronamicpay.com/'
		);

		return $redirect_url;
	}
}
