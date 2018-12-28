<?= $header; ?><?= $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-atomicpay" data-toggle="tooltip" title="<?= $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?= $url_cancel; ?>" data-toggle="tooltip" title="<?= $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?= $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if (isset($error_warning)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if (isset($success) && ! empty($success)) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $label_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?= $url_action; ?>" method="post" enctype="multipart/form-data" id="form-atomicpay" class="form-horizontal">
                    <input type="hidden" name="action" value="save">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-settings" data-toggle="tab"><?= $tab_settings; ?></a></li>
                        <li><a href="#tab-status" data-toggle="tab"><?= $tab_order_status; ?></a></li>
                        <li><a href="#tab-log" data-toggle="tab"><?= $tab_log; ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-settings">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">
                                <span data-toggle="tooltip" title="Use this option to enable or disable AtomicPay plugin"><?= $label_enabled; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_status" id="input-status" class="form-control">
                                        <option value="1" <?php if ($value_enabled == 1) { ?> selected="selected" <?php } ?>><?= $text_enabled; ?></option>
                                        <option value="0" <?php if ($value_enabled == 0) { ?> selected="selected" <?php } ?>><?= $text_disabled; ?></option>
                                    </select>
                                    <?php if (isset($error_enabled)) { ?>
                                    <div class="text-danger"><?= $error_enabled; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-payment-api-accountID">
                                <span data-toggle="tooltip" title="This is your unique Merchant Account ID that can be obtained at your AtomicPay Merchant Account under API Integration. Sign up at https://atomicpay.io"><?= $label_payment_api_accountID; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="atomicpay_payment_api_accountID" id="input-payment-api-accountID" value="<?= $value_payment_api_accountID; ?>" class="form-control" />
                                    <?php if (isset($error_payment_api_accountID)) { ?>
                                    <div class="text-danger"><?= $error_payment_api_accountID; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-payment-api-privateKey">
                                <span data-toggle="tooltip" title="This is your unique API Private Key that can be obtained at your AtomicPay Merchant Account under API Integration. Sign up at https://atomicpay.io"><?= $label_payment_api_privateKey; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="password" name="atomicpay_payment_api_privateKey" id="input-payment-api-privateKey" value="<?= $value_payment_api_privateKey; ?>" class="form-control" />
                                    <?php if (isset($error_payment_api_privateKey)) { ?>
                                    <div class="text-danger"><?= $error_payment_api_privateKey; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-payment-api-publicKey">
                                <span data-toggle="tooltip" title="This is your unique API Public Key that can be obtained at your AtomicPay Merchant Account under API Integration. Sign up at https://atomicpay.io"><?= $label_payment_api_publicKey; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="password" name="atomicpay_payment_api_publicKey" id="input-payment-api-publicKey" value="<?= $value_payment_api_publicKey; ?>" class="form-control" />
                                    <?php if (isset($error_payment_api_publicKey)) { ?>
                                    <div class="text-danger"><?= $error_payment_api_publicKey; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-transaction-speed">
                                <span data-toggle="tooltip" title="The transaction speed determines how quickly an invoice payment is considered to be confirmed, at which you would fulfill and complete the order. Note: 1 confirmation may take up to 10 mins"><?= $label_transaction_speed; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-bolt fa-fw"></i></span>
                                        <select name="atomicpay_transaction_speed" id="input-risk-speed" class="form-control">
                                            <option value="high" <?php if ($value_transaction_speed == "high") { ?> selected="selected" <?php } ?>><?= $text_high; ?></option>
                                            <option value="medium" <?php if ($value_transaction_speed == "medium") { ?> selected="selected" <?php } ?>><?= $text_medium; ?></option>
                                            <option value="low" <?php if ($value_transaction_speed == "low") { ?> selected="selected" <?php } ?>><?= $text_low; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-notification-url">
                                <span data-toggle="tooltip" title="AtomicPay will send IPNs to this notification URL, along with the invoice data"><?= $label_notification_url; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                                        <input type="url" name="atomicpay_notification_url" id="input-notify-url" value="<?= $value_notification_url; ?>" class="form-control" />
                                    </div>
                                    <?php if (isset($error_notification_url)) { ?>
                                    <div class="text-danger"><?= $error_notification_url; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-redirect-url">
                                <span data-toggle="tooltip" title="Customers will be redirected back to this URL after successful, expired or failed payment"><?= $label_redirect_url; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                                        <input type="url" name="atomicpay_redirect_url" id="input-redirect-url" value="<?= $value_redirect_url; ?>" class="form-control" />
                                    </div>
                                    <?php if (isset($error_redirect_url)) { ?>
                                    <div class="text-danger"><?= $error_redirect_url; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-debug">
                                <span data-toggle="tooltip" title="Log AtomicPay plugin events for debugging and troubleshooting"><?= $label_debugging; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_logging" id="input-debugging" class="form-control">
                                        <option value="1" <?php if ($value_debugging == 1) { ?> selected="selected" <?php } ?>><?= $text_enabled; ?></option>
                                        <option value="0" <?php if ($value_debugging == 0) { ?> selected="selected" <?php } ?>><?= $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="tab-status">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The invoice has been paid. Awaiting network confirmation status"><?= $label_paid_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_paid_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_paid_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The invoice is underpaid and has expired. Please kindly contact your customer"><?= $label_underPaid_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_underPaid_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_underPaid_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The invoice is overpaid. Awaiting network confirmation status. Please contact customer on refund matters"><?= $label_overPaid_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_overPaid_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_overPaid_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The invoice has been confirmed. Awaiting payment completion status"><?= $label_confirmed_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_confirmed_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_confirmed_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The invoice payment has completed"><?= $label_completed_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_completed_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_completed_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The invoice is invalid for this order. The payment was not confirmed by the network. Do not process this order"><?= $label_invalid_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_invalid_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_invalid_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The invoice has expired for this order. Do not process this order"><?= $label_expired_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_expired_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_expired_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>     
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="The payment has been received after the invoice has expired. Please contact customer on refund matters"><?= $label_paidAfterExpiry_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="atomicpay_paidAfterExpiry_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status): ?>
                                        <?php $selected = ($order_status['order_status_id'] == $value_paidAfterExpiry_status) ? 'selected' : ''; ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $order_status['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>                            
                        </div>
                        
                        <div class="tab-pane" id="tab-log">
                            <pre><?= $log; ?></pre>
                            <div class="text-right">
                                <a href="<?= $url_clear; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> <?= $button_clear; ?></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $footer; ?>
