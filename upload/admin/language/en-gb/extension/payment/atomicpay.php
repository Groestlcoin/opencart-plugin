<?php

$_['heading_title'] = 'AtomicPay';
$_['text_atomicpay'] = '<a href="http://atomicpay.io" target="_blank"><img src="view/image/payment/atomicpay.png"></a>';
$_['atomicpay'] = 'AtomicPay';

$_['notification_success'] = 'Authorization Successful. AtomicPay plugin settings updated';
$_['notification_log_success'] = 'AtomicPay log has been cleared successfully';
$_['notification_error_notification_url'] = 'The notification URL is required and must be a valid URL';
$_['notification_error_redirect_url'] = 'The redirect URL is required and must be a valid URL';
$_['notification_error_authorization'] = 'The plugin cannot be activated without valid API credentials';
$_['notification_error_notification_url_enabled'] = 'The plugin cannot be activated without a Notification URL';
$_['notification_error_redirect_url_key_enabled'] = 'The plugin cannot be activated without a Redirect URL';
$_['notification_error_payment_api_accountID'] = 'Please check and ensure Account ID is valid';
$_['notification_error_payment_api_privateKey'] = 'Please check and ensure Private Key is valid';
$_['notification_error_payment_api_publicKey'] = 'Please check and ensure Public Key is valid';
$_['notification_error_authorization_failed'] = 'Authorization Failed. AtomicPay plugin settings not updated';

$_['button_save'] = 'Save';
$_['button_cancel'] = 'Cancel';
$_['button_clear'] = 'Clear Logs';

$_['tab_settings'] = 'Settings';
$_['tab_order_status'] = 'Order Status';
$_['tab_log'] = 'Log';

$_['label_edit'] = 'Edit AtomicPay';
$_['label_enabled'] = 'Status';
$_['label_network'] = 'Network';
$_['label_payment_api_accountID'] = 'Account ID';
$_['label_payment_api_privateKey'] = 'API Private Key';
$_['label_payment_api_publicKey'] = 'API Public Key';
$_['label_transaction_speed'] = 'Transaction Speed';
$_['label_paid_status'] = 'Paid Status';
$_['label_underPaid_status'] = 'Underpaid Status';
$_['label_overPaid_status'] = 'Overpaid Status';
$_['label_confirmed_status'] = 'Confirmed Status';
$_['label_completed_status'] = 'Completed Status';
$_['label_invalid_status'] = 'Invalid Status';
$_['label_expired_status'] = 'Expired Status';
$_['label_paidAfterExpiry_status'] = 'Paid After Expiry Status';
$_['label_notification_url'] = 'Notification URL (IPN)';
$_['label_redirect_url'] = 'Redirect URL';
$_['label_debugging'] = 'Debugging';

$_['help_enabled'] = 'Use this option to enable or disable AtomicPay plugin';
$_['help_payment_api_accountID'] = 'This is your unique Merchant Account ID that can be obtained at your AtomicPay Merchant Account under API Integration. Sign up at https://atomicpay.io';
$_['help_payment_api_privateKey'] = 'This is your unique API Private Key that can be obtained at your AtomicPay Merchant Account under API Integration. Sign up at https://atomicpay.io';
$_['help_payment_api_publicKey'] = 'This is your unique API Public Key that can be obtained at your AtomicPay Merchant Account under API Integration. Sign up at https://atomicpay.io';
$_['help_transaction_speed'] = 'The transaction speed determines how quickly an invoice payment is considered to be confirmed, at which you would fulfill and complete the order. Note: 1 confirmation may take up to 10 mins';
$_['help_notification_url'] = 'AtomicPay will send IPNs to this notification URL, along with the invoice data';
$_['help_redirect_url'] = 'Customers will be redirected back to this URL after successful, expired or failed payment';
$_['help_debugging'] = 'Log AtomicPay plugin events for debugging and troubleshooting';

$_['help_paid_status'] = 'The invoice has been paid. Awaiting network confirmation status';
$_['help_underPaid_status'] = 'The invoice is underpaid and has expired. Please kindly contact your customer';
$_['help_overPaid_status'] = 'The invoice is overpaid. Awaiting network confirmation status. Please contact customer on refund matters';
$_['help_confirmed_status'] = 'The invoice has been confirmed. Awaiting payment completion status';
$_['help_completed_status'] = 'The invoice payment has completed';
$_['help_invalid_status'] = 'The invoice is invalid for this order. The payment was not confirmed by the network. Do not process this order';
$_['help_expired_status'] = 'The invoice has expired for this order. Do not process this order';
$_['help_paidAfterExpiry_status'] = 'The payment has been received after the invoice has expired. Please contact customer on refund matters';

$_['text_payment'] = 'Extensions';
$_['text_enabled'] = 'Enabled';
$_['text_disabled'] = 'Disabled';
$_['text_low'] = 'Low Risk (6 Confirmations)';
$_['text_medium'] = 'Medium Risk (2 Confirmations)';
$_['text_high'] = 'High Risk (1 Confirmation)';
