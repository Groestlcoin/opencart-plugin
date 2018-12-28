<?php

// Used by OpenCart 2.1
class ControllerPaymentAtomicpay extends ControllerExtensionPaymentAtomicPay {}

class ControllerExtensionPaymentAtomicPay extends Controller
{
    protected $code = 'payment_atomicpay';
    protected $confirmPath = 'extension/payment';
    protected $viewPath = 'extension/payment/atomicpay';
    protected $languagePath = 'extension/payment/atomicpay';

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (true === version_compare(VERSION, '3.0.0', '<')) {
            $this->code = 'atomicpay';
        }
        if (true === version_compare(VERSION, '2.3.0', '<')) {
            $this->viewPath = 'payment/atomicpay.tpl';
            $this->confirmPath = 'payment';
            $this->languagePath = 'payment/atomicpay';
        }
        if (true === version_compare(VERSION, '2.2.0', '<')) {
            $this->viewPath = 'default/template/payment/atomicpay.tpl';
        }

        $this->load->language($this->languagePath);
    }

    public function index()
    {
        $data['text_title'] = $this->language->get('text_title');
        $data['url_redirect'] = $this->url->link($this->confirmPath.'/atomicpay/confirm', $this->config->get('config_secure'));
        $data['button_confirm'] = $this->language->get('button_confirm');

        if (isset($this->session->data['error_atomicpay'])) {
            $data['error_atomicpay'] = $this->session->data['error_atomicpay'];
            unset($this->session->data['error_atomicpay']);
        }

        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/payment/atomicpay')) {
            return $this->load->view($this->config->get('config_template').'/template/'.$this->viewPath, $data);
        }
        return $this->load->view($this->viewPath, $data);
    }

    // Create payment invoice and redirect to AtomicPay
    public function confirm()
    {
        $this->load->model('checkout/order');

        if (!isset($this->session->data['order_id'])) {
            $this->response->redirect($this->url->link('checkout/cart'));
            return;
        }

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        if (false === $order_info) {
            $this->response->redirect($this->url->link('checkout/cart'));
            return;
        }

        $orderID = $this->session->data['order_id'];
        $order_total = $order_info['total'];
        $order_total = round($order_total, 2);
        $currency_code = $order_info['currency_code'];
        $transaction_speed = $this->config->get($this->code.'_transaction_speed');
        $notification_email = $order_info['email'];
        $notification_url = $this->config->get($this->code.'_notification_url');
        $redirect_url = $this->config->get($this->code.'_redirect_url');

        $AccountID = $this->config->get($this->code.'_payment_api_accountID');
        $AccountPrivateKey = $this->config->get($this->code.'_payment_api_privateKey');

        $endpoint_url = "https://merchant.atomicpay.io/api/v1/invoices";
        $encoded_auth = base64_encode("$AccountID:$AccountPrivateKey");
        $authorization = "Authorization: BASIC $encoded_auth";

        $data_to_post = [
          'order_id' => $orderID,
          'order_price' => $order_total,
          'order_currency' => $currency_code,
          'notification_email' => $notification_email,
          'notification_url' => $notification_url,
          'redirect_url' => $redirect_url,
          'transaction_speed' => $transaction_speed
        ];

        $data_to_post = json_encode($data_to_post);

        $options = [
        CURLOPT_URL        => $endpoint_url,
        CURLOPT_HTTPHEADER => array('Content-Type:application/json', $authorization),
        CURLOPT_POST       => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $data_to_post
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response);
        $code = $data->code;

        if($code == "200")
        {
          $invoice_id = $data->invoice_id;
          $invoice_url = $data->invoice_url;
        }
        else
        {
          $errormessage = $data->message;

          $this->log('Error creating invoice: '.$errormessage);
          $this->session->data['error_atomicpay'] = 'Apologies. AtomicPay is unable to generate the payment invoice:</br>'.$errormessage;
          $this->response->redirect($this->url->link('checkout/checkout'));

          return;
        }

        $order = $this->model_checkout_order->getOrder($orderID);
        $this->model_checkout_order->addOrderHistory($orderID, '1');
        $this->response->redirect($invoice_url);
    }

    // Redirect Handler
    public function success()
    {
        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];

        if (is_null($order_id)) {
            $this->response->redirect($this->url->link('checkout/success'));
            return;
        }

        $this->response->redirect($this->url->link('checkout/success'));
    }

    // The IPN Handler
    public function callback()
    {
        $this->load->model('checkout/order');

        $post = file_get_contents("php://input");
        if (true === empty($post)) {
            $this->log('AtomicPay plugin received empty POST data for an IPN message.');
            return;
        }

        $json = json_decode($post);
        if (true === empty($json)) {
            $this->log('AtomicPay plugin received an invalid JSON payload sent to IPN handler: '.$post);
            return;
        }

        if (false === array_key_exists('invoice_id', $json)) {
            $this->log('AtomicPay plugin did not receive a Payment ID present in JSON payload: '.var_export($json, true));
            return;
        }

        $invoice_id = $json->invoice_id;
        $AccountID = $this->config->get($this->code.'_payment_api_accountID');
        $AccountPrivateKey = $this->config->get($this->code.'_payment_api_privateKey');

		$endpoint_url = "https://merchant.atomicpay.io/api/v1/invoices/$invoice_id";
		$encoded_auth = base64_encode("$AccountID:$AccountPrivateKey");
		$authorization = "Authorization: BASIC $encoded_auth";

        $options = [
			CURLOPT_URL        => $endpoint_url,
			CURLOPT_HTTPHEADER => array('Content-Type:application/json', $authorization),
			CURLOPT_RETURNTRANSFER => true
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

		$data = json_decode($response);
		$code = $data->code;

        if($code == "200")
        {
			$result = $data->result;
			$result_array = $result["0"];

			$atm_invoice_timestamp = $result_array->invoice_timestamp;
			$atm_invoice_id = $result_array->invoice_id;
			$atm_order_id = $result_array->order_id;
			$atm_order_description = $result_array->order_description;
			$atm_order_price = $result_array->order_price;
			$atm_order_currency = $result_array->order_currency;
			$atm_transaction_speed = $result_array->transaction_speed;
			$atm_payment_currency = $result_array->payment_currency;
			$atm_payment_rate = $result_array->payment_rate;
			$atm_payment_address = $result_array->payment_address;
			$atm_payment_paid = $result_array->payment_paid;
			$atm_payment_due = $result_array->payment_due;
			$atm_payment_total = $result_array->payment_total;
			$atm_payment_txid = $result_array->payment_txid;
			$atm_payment_confirmation = $result_array->payment_confirmation;
			$atm_notification_email = $result_array->notification_email;
			$atm_notification_url = $result_array->notification_url;
			$atm_redirect_url = $result_array->redirect_url;
			$atm_status = $result_array->status;
			$atm_statusException = $result_array->statusException;
			if($atm_payment_rate != ""){ $atm_payment_rate = "$atm_payment_rate $atm_order_currency"; }

            $this->log('[Info] The Invoice ID is valid.');

            if(false === isset($atm_order_id) && true === empty($atm_order_id))
            {
            	$this->log('[Error] Could not fetch the order ID from the invoice API. Validation failed. Unable to proceed.');
            }
            else
            {
                $this->log('[Info] Found Order ID: ' . $atm_order_id);
            }

			//Check if invoice status is available
			$checkStatus = $atm_status;
			if (false === isset($checkStatus) && true === empty($checkStatus))
			{
				$this->log('[Error] Unable to get the current status from the invoice');
			}
			else
			{
				$this->log('[Info] The current order status for this invoice is ' . $checkStatus);
			}

			switch ($checkStatus)
			{
				case 'paid':
				if($atm_statusException != "")
				{
					if($atm_statusException == "Overpaid")
					{
						$this->log('[Info] The invoice #'.$invoice_id.' status is Overpaid. Order status has been set as Overpaid');
                  		$order_status_id = $this->config->get($this->code.'_overPaid_status');
					}
				}
				else
				{
					$this->log('[Info] The invoice #'.$invoice_id.' status is paid. Order status has been set as paid');
					$order_status_id = $this->config->get($this->code.'_paid_status');
				}

				break;

				case 'confirmed':
				$this->log('[Info] The invoice #'.$invoice_id.' status is confirmed. Order status has been set as confirmed');
             	$order_status_id = $this->config->get($this->code.'_confirmed_status');
				break;

				case 'complete':
				$this->log('[Info] The invoice #'.$invoice_id.' status is completed. Order status has been set as completed');
             	$order_status_id = $this->config->get($this->code.'_completed_status');
				break;

				case 'invalid':
				$this->log('[Info] The invoice #'.$invoice_id.' status is invalid. Order status has been set as invalid');
             	$order_status_id = $this->config->get($this->code.'_invalid_status');
				break;

				case 'expired':
				$this->log('[Info] The invoice #'.$invoice_id.' status is expired. Order status has been set as expired');
				if($atm_statusException != "")
				{
					if($atm_statusException == "Paid After Expiry")
					{
						$this->log('[Info] The invoice has been paid after expiry...');
						$order_status_id = $this->config->get($this->code.'_paidAfterExpiry_status');
					}

					if($atm_statusException == "Underpaid")
					{
						$this->log('[Info] The invoice is underpaid...');
						$order_status_id = $this->config->get($this->code.'_underPaid_status');
					}
				}
				else
				{
					$this->log('[Info] The invoice is underpaid...');
					$order_status_id = $this->config->get($this->code.'_expired_status');
				}

				break;

				default:
				$this->log('[Info] IPN response is an unknown message type. See error message below:');
				$error_string = 'Unhandled invoice status: ' . $atm_status;
				$this->log("[Warning] $error_string");
			}

          	if($order_status_id != "")
          	{
            	$this->model_checkout_order->addOrderHistory($atm_order_id, $order_status_id);
          	}

			$this->log('[Info] Exiting ipn_callback()...');
		}
        else
        {
			$this->log('[Error] The Invoice ID is invalid');
			$this->log('[Info] Exiting ipn_callback()...');
			wp_die('Invalid IPN');
        }
    }

    // Logger function for debugging
    public function log($message)
    {
        if ($this->config->get($this->code.'_logging') != true) {
            return;
        }
        $log = new Log('atomicpay.log');
        $log->write($message);
    }

}
