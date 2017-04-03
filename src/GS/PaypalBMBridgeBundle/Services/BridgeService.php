<?php

namespace GS\PaypalBMBridgeBundle\Services;

use PayPal\Service\ButtonManagerService;

if (!defined('PP_CONFIG_PATH')) {
    define('PP_CONFIG_PATH', sys_get_temp_dir());
}

class BridgeService
{

    protected $mode;
    protected $username;
    protected $password;
    protected $signature;
    protected $config;

    const PAYPAL_SANDBOX_MODE = "sandbox";
    const PAYPAL_PRODUCTION_MODE = "live";

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        if ($parameters['environment'] == "production") {
            $this->setUsername($parameters['production']['username'])
                    ->setPassword($parameters['production']['password'])
                    ->setSignature($parameters['production']['signature']);
            $this->setMode(self::PAYPAL_PRODUCTION_MODE);
        } else {
            $this->setUsername($parameters['sandbox']['username'])
                    ->setPassword($parameters['sandbox']['password'])
                    ->setSignature($parameters['sandbox']['signature']);
            $this->setMode(self::PAYPAL_SANDBOX_MODE);
        }

        $config = array(
            "mode" => $this->getMode(),
            'http.ConnectionTimeOut' => $parameters['http']['timeout'],
            'http.Retry' => $parameters['http']['retry'],
            'log.FileName' => $parameters['logs']['filename'],
            'log.LogEnabled' => $parameters['logs']['enabled'],
            'log.LogLevel' => $parameters['logs']['level'],
        );

        $this->setConfig($config);
    }

    // Creates a configuration array containing credentials and other required configuration parameters.
    private function getAcctAndConfig()
    {
        $config = array(
            // Signature Credential
            "acct1.UserName" => $this->getUsername(),
            "acct1.Password" => $this->getPassword(),
            "acct1.Signature" => $this->getSignature(),
                // Subject is optional and is required only in case of third party authorization
                //"acct1.Subject" => "",
                // Sample Certificate Credential
                // "acct1.UserName" => "certuser_biz_api1.paypal.com",
                // "acct1.Password" => "D6JNKKULHN3G5B8A",
                // Certificate path relative to config folder or absolute path in file system
                // "acct1.CertPath" => "cert_key.pem",
                // Subject is optional and is required only in case of third party authorization
                //"acct1.Subject" => "",
        );

        return array_merge($config, $this->getConfig());
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    private function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    private function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $signature
     *
     * @return $this
     */
    private function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @param string $mode
     *
     * @return $this
     */
    private function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param array $config
     */
    private function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns ready to use ButtonManagerService from PayPal sdk
     *
     * @return ButtonManagerService
     */
    public function getButtonManagerService()
    {
        $paypalBMService = new ButtonManagerService($this->getAcctAndConfig());
        return $paypalBMService;
    }
}
