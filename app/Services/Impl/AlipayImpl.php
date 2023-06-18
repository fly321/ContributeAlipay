<?php

namespace App\Services\Impl;

use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;

class AlipayImpl implements \App\Services\Alipay
{
    private ?AlipayConfig $alipayConfig;
    public static ?self  $instance = null;
    private ?AlipayConfigUtil $alipayConfigUtil;

    private function __construct()
    {
        // 读取配置文件
        $alipay = config("alipayv3");
        $this->alipayConfig = new AlipayConfig();
        $this->alipayConfig->setAppId($alipay['appId']);

        $this->alipayConfig->setPrivateKey($alipay['rsaPrivateKey']);
        if ($alipay['isCertificate']){
            // 证书模式
            $this->alipayConfig->setAppCertPath($alipay['AppCertPath']);
            $this->alipayConfig->setAlipayPublicCertPath($alipay['AlipayPublicCertPath']);
            $this->alipayConfig->setRootCertPath($alipay['RootCertPath']);
        }else{
            $this->alipayConfig->setAlipayPublicKey($alipay['alipayrsaPublicKey']);
        }

        $this->alipayConfigUtil = new AlipayConfigUtil($this->alipayConfig);
    }


    public function getAlipayConfig(): AlipayConfig
    {
        return $this->alipayConfig;
    }


    public static function getInstance(): self
    {
        if (is_null(static::$instance) || !(static::$instance instanceof self)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * @param AlipayConfig|null $alipayConfig
     * @return AlipayImpl
     */
    public function setAlipayConfig(?AlipayConfig $alipayConfig): AlipayImpl
    {
        $this->alipayConfig = $alipayConfig;
        return $this;
    }

    /**
     * @return AlipayConfigUtil|null
     */
    public function getAlipayConfigUtil(): ?AlipayConfigUtil
    {
        return $this->alipayConfigUtil;
    }

    /**
     * @param AlipayConfigUtil|null $alipayConfigUtil
     */
    public function setAlipayConfigUtil(?AlipayConfigUtil $alipayConfigUtil): void
    {
        $this->alipayConfigUtil = $alipayConfigUtil;
    }

}
