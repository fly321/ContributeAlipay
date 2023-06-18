<?php

namespace App\Services\Impl;

use Alipay\OpenAPISDK\Api\AlipayDataBillAccountlogApi;
use Alipay\OpenAPISDK\Util\AlipayLogger;
use App\Services\AlipayDataBillAccountingApi;
use Exception;
use GuzzleHttp\Client;

class AlipayDataBillAccountingApiImpl implements AlipayDataBillAccountingApi
{

    const SUCCESS = "SUCCESS";
    const FAIL = "FAIL";
    const  ALIPAY_ORDER_DOES_NOT_EXIST= "支付宝订单不存在";


    /** @noinspection PhpDynamicAsStaticMethodCallInspection */
    public function query(array $data = []): ?array
    {
        $apiInstance = new AlipayDataBillAccountlogApi(
            new Client()
        );
        # 取消日志输出
        AlipayLogger::setNeedEnableLogger(false);
        $apiInstance->setAlipayConfigUtil(alipayConfigUtil: AlipayImpl::getInstance()->getAlipayConfigUtil());

        $startTime = $data['start_time']; // string | 账务流水创建时间的起始范围
        $endTime = $data['end_time']; // string | 账务流水创建时间的结束范围。与起始时间间隔不超过31天。查询结果为起始时间至结束时间的左闭右开区间
        $alipayOrderNo = $data['alipay_order_no'] ?? null; // string | 支付宝订单号，通过支付宝订单号精确查询相关的流水明细，商户订单号与支付宝订单号互斥
        $pageNo = $data['pageNo']  ?? 1; // string | 分页号，从1开始
        $pageSize = $data['pageSize'] ?? 2000; // string | 分页大小1000-2000，默认2000
        $trans_memo = $data["trans_memo"] ?? null;
        $trans_amount = $data["trans_amount"] ?? 0;

        try {
            $result = $apiInstance->query(
                startTime: $startTime,
                endTime: $endTime,
                alipayOrderNo: $alipayOrderNo,
                pageNo: $pageNo,
                pageSize: $pageSize,
            );
            return $this->checkPay($alipayOrderNo, $trans_memo, $trans_amount, $result);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    public function toArray($obj){
        return json_decode(json_encode($obj), true);
    }

    /**
     * 检测是否支付
     * @param $alipayOrderNo
     * @param $trans_memo
     * @param $trans_amount
     * @param \Alipay\OpenAPISDK\Model\AlipayDataBillAccountlogQueryResponseModel|\Alipay\OpenAPISDK\Model\AlipayDataBillAccountlogQueryDefaultResponse $result
     * @return array
     */
    public function checkPay($alipayOrderNo, $trans_memo, $trans_amount, $result): array
    {
        if (is_null($result->getDetailList())){
            throw new \Exception(static::ALIPAY_ORDER_DOES_NOT_EXIST);
        }
        if (!is_null($alipayOrderNo) && $alipayOrderNo != "") {
            if (count($result->getDetailList()) == 0){
                throw new \Exception(static::ALIPAY_ORDER_DOES_NOT_EXIST);
            } else {
                return $this->toArray($result->getDetailList()[0]);
            }
        }

        $bool = false;
        $object = null;

        foreach ($result->getDetailList() as $key => $value) {
            if ($value["type"] != "转账") {
                continue;
            }
            if ($value->getTransMemo() == $trans_memo && $value->getTransAmount() == $trans_amount) {
                $bool = true;
                $object = $value;
                break;
            }
        }

        if ($bool) {
            return $this->toArray($object);
        } else {
            throw new Exception(static::ALIPAY_ORDER_DOES_NOT_EXIST);
        }
    }

}
