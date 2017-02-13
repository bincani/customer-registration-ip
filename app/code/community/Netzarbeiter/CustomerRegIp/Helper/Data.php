<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * package    Netzarbeiter_CustomerRegIp
 * copyright  Copyright (c) 2014 Vinai Kopp http://netzarbeiter.com/
 * license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Netzarbeiter_CustomerRegIp_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool|string
     */
    public function getRemoteAddr($ipToLong = false)
    {
        $remoteAddr = $this->_getIpAddress();
        if (!$remoteAddr) {
            return false;
        }
        return $ipToLong ? inet_pton($remoteAddr) : $remoteAddr;
    }

    /**
     * @return string
     */
    private function _getIpAddress()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    // FILTER_FLAG_NO_PRIV_RANGE will filter your local dev
                    //if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    /**
     * @param $httpRequest
     * @return mixed
     */
    public function parseRequest($httpRequest) {
        $request = array();
        $parts = preg_split("/\->/", $httpRequest);
        if ($parts[0] && filter_var($parts[0], FILTER_VALIDATE_URL)) {
            $httpReferer = parse_url($parts[0]);
            $request[0] = array(
                'scheme' => $httpReferer['scheme'],
                'url' => $this->_stripBase($parts[0])
            );
        }
        else {
            $request[0] = "";
        }
        if ($parts[1] && filter_var($parts[1], FILTER_VALIDATE_URL)) {
            $httpRequest = parse_url($parts[1]);
            $request[1] = array(
                'scheme' => $httpRequest['scheme'],
                'url' => $this->_stripBase($parts[1])
            );
        }
        else {
            $request[1] = "";
        }
        return $request;
    }

    /**
     * @param $url
     * @return mixed
     */
    private function _stripBase($url) {
        $storeId = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();
        $baseUrl = Mage::getBaseUrl(); // will get admin url
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $pattern = sprintf("/%s/", str_replace("/", "\/", $baseUrl));
        $home = "";
        if (preg_match($pattern, $url)) {
            if ($url == $baseUrl) {
                $home = "HOME";
            }
            $url = preg_replace($pattern, $home, $url);
        }
        else {
            $secureBaseUrl = Mage::app()->getStore($storeId)->getConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);
            $pattern = sprintf("/%s/", str_replace("/", "\/", $secureBaseUrl));
            if (preg_match($pattern, $url)) {
                if ($url == $secureBaseUrl) {
                    $home = "HOME";
                }
                $url = preg_replace($pattern, $home, $url);
            }
        }
        return $url;
    }

}
