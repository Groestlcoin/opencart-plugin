<?php

/** Used by OC 2.1 */
class ModelPaymentAtomicpay extends ModelExtensionPaymentAtomicpay {}

/**
 * Class ModelExtensionPaymentAtomicpay
 */
class ModelExtensionPaymentAtomicpay extends Model
{
    /** @var string  */
    protected $languagePath = 'extension/payment/atomicpay';

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (true === version_compare(VERSION, '2.3.0', '<')) {
            $this->languagePath = 'payment/atomicpay';
        }

        $this->load->language($this->languagePath);
    }

    public function getMethod()
    {
        $this->load->language('extension/payment/atomicpay');

        return array(
            'code' => 'atomicpay',
            'title' => $this->language->get('text_title'),
            'terms' => '',
            'sort_order' => '1'
        );
    }
}
