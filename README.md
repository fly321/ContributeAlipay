# ContributeAlipay
### 环境需求
* php >= 8.1

### 安装
```shell
git clone https://github.com/fly321/ContributeAlipay.git
composer update
php artisan key:generate
php artisan migrate
```
### 需要配置的地方
> .env

```dotenv
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:AHDpnMJ23ZlGhNzWhmsU3q9P7WAsTT6Lwxhj0bQjyzA=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alipay
DB_USERNAME=root
DB_PASSWORD=123456

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

ALIPAY_APP_ID=
ALIPAY_RSA_PRIVATE_KEY=
ALIPAYRSA_PUBLIC_KEY=
ALIPAY_UID=
```

> config/alipayv3.php
```php
<?php
return [
    "gatewayUrl" => "https://openapi.alipay.com/gateway.do",
    "appId" => env("ALIPAY_APP_ID"),
    "rsaPrivateKey" => env("ALIPAY_RSA_PRIVATE_KEY"),
    "alipayrsaPublicKey" => env("ALIPAYRSA_PUBLIC_KEY"),
    "AppCertPath" => public_path().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cert".DIRECTORY_SEPARATOR."appCertPublicKey_2021001194686370.crt",
    "AlipayPublicCertPath" => public_path().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cert".DIRECTORY_SEPARATOR."alipayCertPublicKey_RSA2.crt",
    "RootCertPath" => public_path().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cert".DIRECTORY_SEPARATOR."alipayRootCert.crt",
    "isCertificate" => true, // true证书模式 false秘钥模式
    "uid" => env("ALIPAY_UID"), // 支付宝uid
    "urlScheme" => 'alipays://platformapi/startapp?appId=20000123&actionType=scan&biz_data={"s": "money","u": "%s","a": "%s","m":"%s"}', // 支付宝urlScheme
];
```

> 如果是证书模式需要配置证书
* 根目录下新建cert文件夹
* 将支付宝下载的证书放入cert文件夹下
* 修改config/alipayv3.php中的AppCertPath、AlipayPublicCertPath、RootCertPath
* 修改.env中的isCertificate为true
* 修改.env中的uid为支付宝uid
