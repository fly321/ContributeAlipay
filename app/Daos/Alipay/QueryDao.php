<?php

namespace App\Daos\Alipay;

use App\Models\AlipayOrder;
use App\Services\Impl\AlipayDataBillAccountingApiImpl;
use Exception;

class QueryDao
{
    private AlipayDataBillAccountingApiImpl $alipay;
    const ALIPAY_ORDER_NO_OR_TRANS_MEMO_NOT_EMPTY = "alipay_order_no or trans_memo not empty!";
    const ORDER_NOT_EXITES = "order not exits!";
    public function __construct()
    {
        $this->alipay = new AlipayDataBillAccountingApiImpl();
    }


    public function query(array $params){
        $data = [
            "start_time" => $params['start_time'] ?? date("Y-m-d H:i:s", strtotime("-15 minute")),
            "end_time" => $params['end_time'] ?? date("Y-m-d H:i:s"),
            "page_no" => $params['page_no'] ?? 1,
            "page_size" => $params['page_size'] ?? 2000,
            "alipay_order_no" => $params['alipay_order_no'] ?? "",
            "trans_memo" => $params['trans_memo'] ?? "",
            "trans_amount" => $params['trans_amount'] ?? 0
        ];

        if ($data['alipay_order_no'] == "" && $data['trans_memo'] == "") {
            throw new \Exception(static::ALIPAY_ORDER_NO_OR_TRANS_MEMO_NOT_EMPTY);
        }

        if ($data['trans_memo'] != "") {
            $alipayOrder = AlipayOrder::where("sn", $params['trans_memo'])->first();
            if (!$alipayOrder){
                throw new \Exception(static::ORDER_NOT_EXITES);
            }

        }


        try {
            return $this->alipay->query($data);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return AlipayDataBillAccountingApiImpl
     */
    public function getAlipay(): AlipayDataBillAccountingApiImpl
    {
        return $this->alipay;
    }

    /**
     * @param AlipayDataBillAccountingApiImpl $alipay
     */
    public function setAlipay(AlipayDataBillAccountingApiImpl $alipay): void
    {
        $this->alipay = $alipay;
    }

    public static function getMaxId(){
        return AlipayOrder::max("id") ?? 0;
    }

}
