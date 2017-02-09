<?php
/**
 * @package   Afterpay_Afterpay
 * @author    Afterpay <steven.gunarso@touchcorp.com>
 * @copyright Copyright (c) 2016 Afterpay (http://www.afterpay.com.au)
 */

/* @var $installer Mage_Customer_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'registration_http_request', array(
    'label' => 'Registration HTTP Request',
    'type' => 'varchar',
    'input' => 'label',
    'backend' => 'customerregip/entity_attribute_backend_request',
    'required' => 0,
    'visible' => 0,
));

$installer->endSetup();
