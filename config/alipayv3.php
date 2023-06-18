<?php
return [
    "gatewayUrl" => "https://openapi.alipay.com/gateway.do",
    "appId" => env("ALIPAY_APP_ID"),
    "rsaPrivateKey" => env("ALIPAY_RSA_PRIVATE_KEY"),
    "alipayrsaPublicKey" => env("ALIPAYRSA_PUBLIC_KEY"),
    "AppCertPath" => public_path().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cert".DIRECTORY_SEPARATOR."appCertPublicKey_".env("ALIPAY_APP_ID").".crt",
    "AlipayPublicCertPath" => public_path().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cert".DIRECTORY_SEPARATOR."alipayCertPublicKey_RSA2.crt",
    "RootCertPath" => public_path().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cert".DIRECTORY_SEPARATOR."alipayRootCert.crt",
    "isCertificate" => true, // true证书模式 false秘钥模式
    "uid" => env("ALIPAY_UID"), // 支付宝uid
    "urlScheme" => 'alipays://platformapi/startapp?appId=20000123&actionType=scan&biz_data={"s": "money","u": "%s","a": "%s","m":"%s"}', // 支付宝urlScheme
];
