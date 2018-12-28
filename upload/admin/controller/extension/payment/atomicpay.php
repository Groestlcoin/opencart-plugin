<?php

// Used by OpenCart 2.1
class ControllerPaymentAtomicpay extends ControllerExtensionPaymentAtomicpay {}

class ControllerExtensionPaymentAtomicpay extends Controller
{
    protected $error = array();
    protected $registry;
    protected $token = 'user_token';
    protected $paymentBreadcrumbLink = 'marketplace/extension';
    protected $paymentExtensionLink = 'extension/payment';
    protected $code = 'payment_atomicpay';
    protected $viewPath = 'extension/payment/atomicpay';

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (!empty($missingRequirements = $this->missingRequirements())) {
            echo $missingRequirements;
            exit;
        }

        $this->registry = $registry;

        if (true === version_compare(VERSION, '2.3.0', '<')) {
            $this->token = 'token';
            $this->viewPath = 'payment/atomicpay.tpl';
            $this->paymentExtensionLink = 'payment';
            $this->paymentBreadcrumbLink = 'extension/payment';
            $this->code = 'atomicpay';
        } elseif (true === version_compare(VERSION, '3.0.0', '<')) {
            $this->token = 'token';
            $this->paymentBreadcrumbLink = 'extension/extension';
            $this->code = 'atomicpay';
        }

        $this->load->language($this->paymentExtensionLink.'/atomicpay');
    }

    public function __get($name)
    {
        return $this->registry->get($name);
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

    // Plugin installer
    public function install()
    {
        $this->log('Installing');
        $this->load->model('localisation/order_status');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_status','0','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_payment_api_accountID','','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_payment_api_privateKey','','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_payment_api_publicKey','','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_transaction_speed','medium','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_paid_status',2,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_underPaid_status',10,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_overPaid_status',2,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_confirmed_status',15,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_completed_status',5,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_invalid_status',10,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_expired_status',7,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_paidAfterExpiry_status',10,'0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_notification_url','".str_replace("admin/", "", $this->url->link($this->paymentExtensionLink.'/atomicpay/callback', $this->config->get('config_secure')))."','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_redirect_url','".str_replace("admin/", "", $this->url->link($this->paymentExtensionLink.'/atomicpay/success', $this->config->get('config_secure')))."','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_logging','1','0');");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`,`code`,`key`,`value`,`serialized`) VALUES ('0','".$this->code."','".$this->code."_geo_zone_id','0','0');");
    }

    // Plugin uninstaller
    public function uninstall()
    {
        $this->log('Uninstalling');
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting($this->code);
    }

    // Setting Handler
    public function index()
    {
        // Activate array that passes data to twig template
        $data = array();

        // Saving settings
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post['action'] === 'save') {
            $data = $this->validate();
            if (empty($data)) {
                $this->load->model('setting/setting');
                $this->model_setting_setting->editSetting($this->code, $this->request->post);
                $this->log('Settings Updated.');
                $this->session->data['success'] = $this->language->get('notification_success');
                $this->response->redirect($this->url->link($this->paymentExtensionLink.'/atomicpay', $this->token.'='.$this->session->data[$this->token], true));
            }
        }

        $this->document->setTitle($this->language->get('atomicpay'));

        // System template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        // Links
        $data['url_action'] = $this->url->link($this->paymentExtensionLink.'/atomicpay', $this->token.'='.$this->session->data[$this->token], 'SSL');
        $data['url_reset'] = $this->url->link($this->paymentExtensionLink.'/atomicpay/reset', $this->token.'='.$this->session->data[$this->token], 'SSL');
        $data['url_clear'] = $this->url->link($this->paymentExtensionLink.'/atomicpay/clear', $this->token.'='.$this->session->data[$this->token], 'SSL');
        $data['cancel'] = $this->url->link($this->paymentExtensionLink, $this->token.'='.$this->session->data[$this->token].'&type=payment', 'SSL');

        // Buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_clear'] = $this->language->get('button_clear');

        // Breadcrumbs
        $data['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', $this->token.'='.$this->session->data[$this->token], true)
            ),
            array(
                'text' => $this->language->get('text_payment'),
                'href' => $this->url->link($this->paymentBreadcrumbLink, $this->token.'='.$this->session->data[$this->token] . '&type=payment', true)
            ),
            array(
                'text' => $this->language->get('atomicpay'),
                'href' => $this->url->link($this->paymentExtensionLink.'/atomicpay', $this->token.'='.$this->session->data[$this->token], true)
            ),
        );

        // Tabs
        $data['tab_settings'] = $this->language->get('tab_settings');
        $data['tab_order_status'] = $this->language->get('tab_order_status');
        $data['tab_log'] = $this->language->get('tab_log');

        // Headings
        $data['heading_title'] = $this->language->get('atomicpay');

        // Labels
        $data['label_edit'] = $this->language->get('label_edit');
        $data['label_enabled'] = $this->language->get('label_enabled');
        $data['label_payment_api_accountID'] = $this->language->get('label_payment_api_accountID');
        $data['label_payment_api_privateKey'] = $this->language->get('label_payment_api_privateKey');
        $data['label_payment_api_publicKey'] = $this->language->get('label_payment_api_publicKey');
        $data['label_transaction_speed'] = $this->language->get('label_transaction_speed');
        $data['label_paid_status'] = $this->language->get('label_paid_status');
        $data['label_underPaid_status'] = $this->language->get('label_underPaid_status');
        $data['label_overPaid_status'] = $this->language->get('label_overPaid_status');
        $data['label_confirmed_status'] = $this->language->get('label_confirmed_status');
        $data['label_completed_status'] = $this->language->get('label_completed_status');
        $data['label_invalid_status'] = $this->language->get('label_invalid_status');
        $data['label_expired_status'] = $this->language->get('label_expired_status');
        $data['label_paidAfterExpiry_status'] = $this->language->get('label_paidAfterExpiry_status');
        $data['label_notification_url'] = $this->language->get('label_notification_url');
        $data['label_redirect_url'] = $this->language->get('label_redirect_url');
        $data['label_debugging'] = $this->language->get('label_debugging');

        // Text
        $data['text_payment'] = $this->language->get('text_payment');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_low'] = $this->language->get('text_low');
        $data['text_medium'] = $this->language->get('text_medium');
        $data['text_high'] = $this->language->get('text_high');

        // Validation
        $data['success'] = '';
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        // Load saved values
        $data['value_enabled'] = $this->config->get($this->code.'_status');
        $data['value_payment_api_accountID'] = $this->config->get($this->code.'_payment_api_accountID');
        $data['value_payment_api_privateKey'] = $this->config->get($this->code.'_payment_api_privateKey');
        $data['value_payment_api_publicKey'] = $this->config->get($this->code.'_payment_api_publicKey');
        $data['value_transaction_speed'] = $this->config->get($this->code.'_transaction_speed');
        $data['value_paid_status'] = $this->config->get($this->code.'_paid_status');
        $data['value_underPaid_status'] = $this->config->get($this->code.'_underPaid_status');
        $data['value_overPaid_status'] = $this->config->get($this->code.'_overPaid_status');
        $data['value_confirmed_status'] = $this->config->get($this->code.'_confirmed_status');
        $data['value_completed_status'] = $this->config->get($this->code.'_completed_status');
        $data['value_invalid_status'] = $this->config->get($this->code.'_invalid_status');
        $data['value_expired_status'] = $this->config->get($this->code.'_expired_status');
        $data['value_paidAfterExpiry_status'] = $this->config->get($this->code.'_paidAfterExpiry_status');
        $data['value_notification_url'] = $this->config->get($this->code.'_notification_url');
        $data['value_redirect_url'] = $this->config->get($this->code.'_redirect_url');
        $data['value_debugging'] = $this->config->get($this->code.'_logging');

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        // Log data
        $data['log'] = '';
        $file = DIR_LOGS.'atomicpay.log';
        if (file_exists($file)) {
            foreach (file($file, FILE_USE_INCLUDE_PATH, null) as $line) {
                $data['log'] .= $line."<br/>\n";
            }
        }
        if (empty($data['log'])) {
            $data['log'] = '<i>No log data available. Is Debugging switched on?</i>';
        }

        // Send output to browser
        $this->response->setOutput($this->load->view($this->viewPath, $data));
    }

    // Clear the AtomicPay Log
    public function clear()
    {
        fclose(fopen(DIR_LOGS.'atomicpay.log', 'w'));
        $this->session->data['success'] = $this->language->get('notification_log_success');
        $this->response->redirect($this->url->link($this->paymentExtensionLink.'/atomicpay', $this->token.'='.$this->session->data[$this->token], 'SSL'));
    }

    // Authorization and Validation
    private function validate()
    {
        $data = array();
        // Ensure the user has the permission to modify the plugin
        if (!$this->user->hasPermission('modify', $this->paymentExtensionLink.'/atomicpay')) {
            $data['error_warning'] = $this->language->get('warning_permission');
        }

        // Ensure the notification URL is set and a valid URL
        if (!empty($this->request->post[$this->code.'_notification_url']) && false === filter_var($this->request->post[$this->code.'_notification_url'], FILTER_VALIDATE_URL)) {
            $data['error_notification_url'] = $this->language->get('notification_error_notification_url');
        }

        // Ensure the redirect URL is set and a valid URL
        if (!empty($this->request->post[$this->code.'_redirect_url']) && false === filter_var($this->request->post[$this->code.'_redirect_url'], FILTER_VALIDATE_URL)) {
            $data['error_redirect_url'] = $this->language->get('notification_error_redirect_url');
        }

        // Ensure the plugin cannot be activated without authorization
        if ($this->request->post[$this->code.'_status'] == 1 && empty($this->request->post[$this->code.'_payment_api_accountID']) && empty($this->request->post[$this->code.'_payment_api_privateKey']) && empty($this->request->post[$this->code.'_payment_api_publicKey'])) {
            $data['error_enabled'] = $this->language->get('notification_error_payment_authorization');
            $data['error_payment_api_accountID'] = $this->language->get('notification_error_payment_api_accountID');
            $data['error_payment_api_privateKey'] = $this->language->get('notification_error_payment_api_privateKey');
            $data['error_payment_api_publicKey'] = $this->language->get('notification_error_payment_api_publicKey');
        }

        // Ensure the plugin cannot be activated without a notification URL
        if ($this->request->post[$this->code.'_status'] == 1 && empty($this->request->post[$this->code.'_notification_url'])) {
            $data['error_enabled'] = $this->language->get('notification_error_notification_url_enabled');
            $data['error_notification_url'] = $this->language->get('notification_error_notification_url');
        }

        // Ensure the plugin cannot be activated without a redirect URL
        if ($this->request->post[$this->code.'_status'] == 1 && empty($this->request->post[$this->code.'_redirect_url'])) {
            $data['error_enabled'] = $this->language->get('notification_error_redirect_url_enabled');
            $data['error_redirect_url'] = $this->language->get('notification_error_redirect_url');
        }

        // Request API Authorization
        if (!empty($this->request->post[$this->code.'_payment_api_accountID']) || !empty($this->request->post[$this->code.'_payment_api_privateKey']) || !empty($this->request->post[$this->code.'_payment_api_publicKey']))
        {
            try
            {
                $atm_accountID = trim($this->request->post[$this->code.'_payment_api_accountID']);
                $atm_privateKey = trim($this->request->post[$this->code.'_payment_api_privateKey']);
                $atm_publicKey = trim($this->request->post[$this->code.'_payment_api_publicKey']);

                $atm_accountID = trim($atm_accountID);
                $atm_privateKey = trim($atm_privateKey);
                $atm_publicKey = trim($atm_publicKey);

                // Validate API Connection
                $endpoint_url = 'https://merchant.atomicpay.io/api/v1/authorization';

                $data_to_post = [
                  'account_id' => $atm_accountID,
                  'account_privateKey' => $atm_privateKey,
                  'account_publicKey' => $atm_publicKey
                ];

                $options = [
                  CURLOPT_URL        => $endpoint_url,
                  CURLOPT_POST       => true,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_POSTFIELDS => $data_to_post,
                ];

                $curl = curl_init();
                curl_setopt_array($curl, $options);
                $response = curl_exec($curl);
                curl_close($curl);

                $atm_data = json_decode($response);
                $code = $atm_data->code;

                if($code == "200")
                {}
                else {
                  throw new AuthenticationException("Failed");
                }

            }
            catch (AuthenticationException $exception) {
                $data['error_warning'] = $this->language->get('notification_error_authorization_failed');
                $data['error_payment_api_accountID'] = $this->language->get('notification_error_payment_api_accountID');
                $data['error_payment_api_privateKey'] = $this->language->get('notification_error_payment_api_privateKey');
                $data['error_payment_api_publicKey'] = $this->language->get('notification_error_payment_api_publicKey');
            }
        }

        return $data;
    }

    // Check that the system meets the minimum requirements
    private function missingRequirements()
    {
        $errors = [];
        $contactYourWebAdmin = " in order to function. Please contact your web server administrator for assistance.";

        # PHP
        if (true === version_compare(PHP_VERSION, '5.5.0', '<'))
        {
            $errors[] = 'Your PHP version is too old. The AtomicPay plugin requires PHP 5.4 or higher'
                .$contactYourWebAdmin;
        }

        # OpenSSL
        if (extension_loaded('openssl') === false)
        {
            $errors[] = 'The AtomicPay plugin requires the OpenSSL extension for PHP'.$contactYourWebAdmin;
        }

        # Json
        if (extension_loaded('json') === false)
        {
            $errors[] = 'The AtomicPay plugin requires the JSON extension for PHP'.$contactYourWebAdmin;
        }

        # Curl required
        if (false === extension_loaded('curl'))
        {
            $errors[] = 'The AtomicPay plugin requires the Curl extension for PHP'.$contactYourWebAdmin;
        }

        if (!empty($errors))
        {
            return implode("<br>\n", $errors);
        }
    }
}
