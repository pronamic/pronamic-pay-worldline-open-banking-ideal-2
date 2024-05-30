<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking
 */

namespace Pronamic\WordPress\Pay\Gateways\WorldlineOpenBanking;

use Pronamic\IDealIssuers\IDealIssuerService;
use Pronamic\WordPress\Pay\Banks\BankAccountDetails;
use Pronamic\WordPress\Pay\Core\Gateway as PronamicGateway;
use Pronamic\WordPress\Pay\Core\ModeTrait;
use Pronamic\WordPress\Pay\Core\PaymentMethod;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Fields\IDealIssuerSelectField;
use Pronamic\WordPress\Pay\Fields\SelectFieldOption;
use Pronamic\WordPress\Pay\Fields\SelectFieldOptionGroup;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Gateway class
 */
final class Gateway extends PronamicGateway {
	use ModeTrait;

	/**
	 * Config.
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Constructs and initializes an Wordline Open Banking gateway
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct();

		$this->config = $config;

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		$this->supports = [
			'payment_status_request',
		];

		$ideal_payment_method = new PaymentMethod( PaymentMethods::IDEAL );
		$ideal_payment_method->set_status( 'active' );

		$field_ideal_issuer = new IDealIssuerSelectField( 'pronamic_pay_worldline_open_banking_ideal_issuer' );
		$field_ideal_issuer->set_options( $this->get_ideal_issuers() );

		$ideal_payment_method->add_field( $field_ideal_issuer );

		$this->register_payment_method( $ideal_payment_method );
	}

	/**
	 * Get iDEAL issuers.
	 *
	 * @return iterable<SelectFieldOption|SelectFieldOptionGroup>
	 */
	private function get_ideal_issuers() {
		$ideal_issuer_service = new IDealIssuerService();

		$issuers = $ideal_issuer_service->get_issuers();

		$items = [];

		foreach ( $issuers as $issuer ) {
			$items[] = new SelectFieldOption( $issuer->code, $issuer->name );
		}

		return $items;
	}

	/**
	 * Start
	 *
	 * @see PronamicGateway::start()
	 *
	 * @param Payment $payment Payment.
	 */
	public function start( Payment $payment ) {
		$client = new Client( $this->config );

		$common_payment_data = new PaymentInitiationRequestBasic( $payment->get_total_amount()->number_format( 2, '.', '' ) );

		$common_payment_data->remittance_information = $payment->get_description();

		$reference = $payment->format_string( $this->config->reference );

		if ( '' === $reference ) {
			$reference = (string) $payment->get_id();
		}

		if ( '' !== $reference ) {
			$common_payment_data->remittance_information_structured = new RemittanceInformationStructured( $reference );
		}

		$issuer = $payment->get_meta( 'issuer' );

		if ( null !== $issuer && '' !== $issuer ) {
			$debtor_information = new DebtorInformation();

			$debtor_information->agent = $issuer;

			$common_payment_data->debtor_information = $debtor_information;
		}

		$payment_initiation_request = new PaymentInitiationRequest( $common_payment_data );

		$payment_initiation_request->initiating_party_return_url = $payment->get_return_url();

		$payment_initiation_response = $client->create_payment( $payment_initiation_request );

		$payment->set_meta( 'worldline_open_banking_payment_id', $payment_initiation_response->common_payment_data->payment_id );
		$payment->set_meta( 'worldline_open_banking_aspsp_payment_id', $payment_initiation_response->common_payment_data->aspsp_payment_id );

		$payment->set_transaction_id( $payment_initiation_response->common_payment_data->aspsp_payment_id );

		if ( null !== $payment_initiation_response->links->redirect_url ) {
			$redirect_url = $payment_initiation_response->links->redirect_url;

			/**
			 * Filter for the Wordline Open Banking redirect URL.
			 *
			 * @param string  $redirect_url The Wordline Open Banking redirect URL.
			 * @param Payment $payment      Pronamic Pay payment object.
			 */
			$redirect_url = \apply_filters( 'pronamic_pay_worldline_open_banking_redirect_url', $redirect_url, $payment );

			$payment->set_action_url( $redirect_url );
		}
	}

	/**
	 * Update status of the specified payment
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 * @throws \Exception Throws an execution if private key cannot be read.
	 */
	public function update_status( Payment $payment ) {
		$client = new Client( $this->config );

		$worldline_payment_id = $payment->get_meta( 'worldline_open_banking_payment_id' );

		if ( null === $worldline_payment_id ) {
			return;
		}

		$payment_status_request = new PaymentStatusRequest( $worldline_payment_id );

		$payment_status_response = $client->get_payment_status( $payment_status_request );

		$status = Status::transform_to_pronamic( $payment_status_response->common_payment_data->payment_status );

		if ( null !== $status ) {
			$payment->set_status( $status );
		}

		/**
		 * Consumer bank details.
		 */
		$debtor_information = $payment_status_response->common_payment_data->debtor_information;

		if ( null !== $debtor_information ) {
			$consumer_bank_details = $payment->get_consumer_bank_details();

			if ( null === $consumer_bank_details ) {
				$consumer_bank_details = new BankAccountDetails();

				$payment->set_consumer_bank_details( $consumer_bank_details );
			}

			$consumer_bank_details->set_name( $debtor_information->name );

			$debtor_account = $debtor_information->account;

			if ( null !== $debtor_account ) {
				switch ( $debtor_account->scheme_name ) {
					case 'IBAN':
						$consumer_bank_details->set_iban( $debtor_account->identification );

						break;
					case 'SortCodeAccountNumber':
					default:
						$consumer_bank_details->set_account_number( $debtor_account->identification );
				}
			}
		}
	}
}
