<?php

namespace App\Services;

use Alipay\OpenAPISDK\Util\Model\AlipayConfig;

interface Alipay
{
    # getAlipayConfig
    public function getAlipayConfig() :AlipayConfig;
}
