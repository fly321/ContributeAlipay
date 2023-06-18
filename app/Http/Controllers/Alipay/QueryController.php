<?php

namespace App\Http\Controllers\Alipay;

use App\Annotation\RequestMapping;
use App\Daos\Alipay\QueryDao;
use App\Http\Controllers\Controller;
use App\Models\AlipayOrder;
use App\Scan\Scanning;
use App\Services\Impl\AlipayDataBillAccountingApiImpl;
use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Nette\Utils\Random;
use ReflectionClass;
use ReflectionMethod;

#[RequestMapping(prefix: "alipay")]
class QueryController extends Controller
{
    #[RequestMapping(path: "checkPay", name: "", method: ["POST"], middleware: "", domain: "", prefix: "")]
    public function checkPay(Request $request)
    {
        // 设置中国时区
        date_default_timezone_set("PRC");
        try {
            $array = (new QueryDao())->query($request->all());
        } catch (\Exception $e) {
            return response()->json(data: [
                "status" => AlipayDataBillAccountingApiImpl::FAIL,
                "error_msg" => $e->getMessage(),
                "data" => null,
                "code" => 0
            ]);
        }

        // 支付成功修改
        $aliPayOrder = AlipayOrder::where("sn", $array["trans_memo"])->where("price", $array['balance'])->first();
        if ($aliPayOrder && $aliPayOrder->status == 0) {
            $aliPayOrder->status = 1;
            $aliPayOrder->pay_time = time();
            $aliPayOrder->alipay_sn = $array["alipay_order_no"];
            $aliPayOrder->save();
        }

        return response()->json([
            "status" => AlipayDataBillAccountingApiImpl::SUCCESS,
            "error_msg" => "",
            "data" => $array,
            "code" => 1,
            "message" => "success"
        ]);
    }

    #[RequestMapping(path: "createPay", name: "", method: ["POST","GET"], middleware: "", domain: "", prefix: "")]
    public function createPay(Request $request){
        $price = $request->input("price", 0.01);
        $orderSn = $request->input("orderSn", md5(Random::generate(32).time()));
        $remark = $request->input("remark", "");
        if ($remark == "") $remark = "给你一拳！";
        $uid = config("alipayv3.uid");
        $url = config("alipayv3.urlScheme");
        $url = sprintf($url, $uid, $price, $orderSn);
        $arr = [
            "price" => $price,
            "sn" => $orderSn,
            "urlScheme" => $url,
            "remark" => $remark,
        ];

        try {
            $aliPayOrder = new AlipayOrder;
            foreach ($arr as $key => $value){
                $aliPayOrder->$key = $value;
            }
            unset($aliPayOrder->urlScheme);
            $aliPayOrder->save();
        } catch (\Exception $e) {
            return response()->json(data: [
                "status" => AlipayDataBillAccountingApiImpl::FAIL,
                "error_msg" => $e->getMessage(),
                "data" => null,
                "code" => 0
            ]);
        }

        return response()->json([
            "status" => AlipayDataBillAccountingApiImpl::SUCCESS,
            "error_msg" => "",
            "data" => array_merge($arr, ["timestamp" => time()]),
            "code" => 1,
            "message" => "创建订单成功！"
        ]);
    }

    public function index(){
        // 模板传参
        return view('alipay.index');
    }


    #[RequestMapping(path: "list", name: "", method: ["POST","GET"], middleware: "", domain: "", prefix: "")]
    public function list(Request $request){
        $aliPayOrder = new AlipayOrder();
        // 游标翻页
        $cursor = $request->input("cursor", 0);
        $limit = $request->input("limit", 10);
        $list = $aliPayOrder->where("id", "<=", $cursor)->orderBy("id", "desc")->limit($limit)->get();
        // 打印sql
        $list = $list->toArray();
        try {
//            $cursor = $list[count($list) - 1]["id"];
            if (count($list) === 1 || count($list)< $limit) {
                $cursor = false;
            }else{
                $cursor = end($list)["id"];
            }

        } catch (\Exception $e) {
            $cursor = false;
        }
        return response()->json([
            "status" => AlipayDataBillAccountingApiImpl::SUCCESS,
            "error_msg" => "",
            "data" => [
                "list" => $list,
                "cursor" => $cursor
            ]
        ]);
    }

}
