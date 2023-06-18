<?php

namespace App\Services;

interface AlipayDataBillAccountingApi
{
    function query(array $data = []) :?array;

    function checkPay($alipayOrderNo, $trans_memo, $trans_amount, $array): array;
}
