<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>捐赠Website</title>

{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.1/vue.js"></script>--}}
    <script src="https://lf6-cdn-tos.bytecdntp.com/cdn/vue/2.6.10/vue.js"></script>
    <!-- 1. 导入css -->
{{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/element-ui/2.8.2/theme-chalk/index.css" rel="stylesheet">--}}
    <link href="https://lf6-cdn-tos.bytecdntp.com/cdn/element-ui/2.8.2/theme-chalk/index.css" rel="stylesheet">
    <!-- 2. 引入vue和vue-router-->

    <!-- 3. 引入ElementUI组件 -->
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/element-ui/2.8.2/index.js"></script>--}}
    <script src="https://lf6-cdn-tos.bytecdntp.com/cdn/element-ui/2.8.2/index.js"></script>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0-beta.1/axios.min.js"></script>--}}
    <script src="https://lf6-cdn-tos.bytecdntp.com/cdn/axios/0.19.0-beta.1/axios.min.js"></script>



</head>
<body>
<div id="app">

    <el-dialog
        title="PayInfo"
        :visible.sync="dialogVisible"
        width="30%"
        :before-close="handleClose"
        >

        <div>订单号：@{{payObj.sn}}</div>
        <div>支付剩余时间：<span style="color:red">@{{timeText}}</span></div>
        <el-image style="text-align: center" :src="payObj.fullUrlScheme"></el-image>

        <a :href="payObj.urlScheme" style="color: #cccccc;font-weight: bold">点我跳转</a>

        <span slot="footer" class="dialog-footer">
        </span>
    </el-dialog>

    <div id="container">

        <div class="he1">
        <div id="header">
            <div class="title bgc">@{{webTitle}}</div>
        </div>

        <el-row>
            <el-form ref="form" :model="form" label-width="80px">
                <el-row class="flex">
                    <div>
                        <el-form-item class="text-bold" :label="form.price.title">
                            <div class="grid-content bg-purple-dark">
                                <el-input-number :step="form.price.step" :placeholder="form.price.placeholder" v-model="form.price.val"></el-input-number>
                            </div>
                        </el-form-item>

                        <el-form-item class="text-bold" :label="form.remark.title">
                            <div class="grid-content bg-purple-dark">
                                <el-input :placeholder="form.remark.placeholder" v-model="form.remark.val"></el-input>
                            </div>
                        </el-form-item>

                        <el-button type="primary" style="color: white" @click="createPay">立即捐赠</el-button>
                    </div>
                </el-row>
            </el-form>
        </el-row>
        </div>

        <el-table
            class="dataList"
            :data="tableData"
            style="width: 100%"
        >
            <el-table-column
                prop="sn"
                label="订单号"
            >
            </el-table-column>
            <el-table-column
                prop="price"
                label="金额"
            >
            </el-table-column>
            <el-table-column
                prop="remark"
                label="备注">
            </el-table-column>
            <el-table-column
                prop="StatusText"
                label="状态">
            </el-table-column>
            <el-table-column
                prop="created_at"
                label="创建时间"
            >
                <template slot-scope="scope">
                    <span>@{{ scope.row.created_at | formatDate }}</span>
                </template>
            </el-table-column>

            <el-table-column
                fixed="right"
                label="操作"
                width="120">
                <template slot-scope="scope">
                    <el-button v-if="scope.row.status === 0"
                        @click.native.prevent="checkPay(scope.$index, tableData)"
                        type="text"
                        size="small">
                        自助查询
                    </el-button>

                    <el-button v-if="scope.row.status === 1"
                        @click.native.prevent="checkPay2(scope.$index, tableData)"
                        type="text"
                        size="small">
                        查询原始数据
                    </el-button>

                </template>
            </el-table-column>
        </el-table>

{{--        居中--}}
        <div style="justify-content: space-between;display: flex">
        <el-button type="primary" style="color: white;flex: 1;" v-if="cursor" @click="loadMore">加载更多</el-button>
        </div>

        <footer>
            <div class="footer" style="color: white;font-weight: bold;text-align: center;font-size: 30px">
                <div>@{{webTitle}} ©{{date("Y")}} Created by <a style="text-decoration: none;color: rgb(64,158,255)" href="https://github.com/fly321"><i class="el-icon-s-promotion"></i>Fly</a></div>
                <div>github: <a style="text-decoration: none;color: rgb(64,158,255)" href="https://github.com/fly321/ContributeAlipay"><i class="el-icon-s-promotion"></i>Fly</a></div>
            </div>
        </footer>

    </div>
</div>

<script type="module">
    const maxId = {{\App\Daos\Alipay\QueryDao::getMaxId()}};
    Vue.filter('formatDate', function (time) {
        // 时间戳转时间
        time = time * 1000;
        // 直接转成时间格式
        let date = new Date(time);
        let Y = date.getFullYear() + '-';
        let M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        let D = (date.getDate() < 10 ? '0'+date.getDate() : date.getDate()) + ' ';
        let h = (date.getHours() < 10 ? '0'+date.getHours() : date.getHours()) + ':';
        let m = (date.getMinutes() < 10 ? '0'+date.getMinutes() : date.getMinutes()) + ':';
        let s = (date.getSeconds() < 10 ? '0'+date.getSeconds() : date.getSeconds());
        return Y+M+D+h+m+s;
    })
    new Vue({
        el: '#app',
        mounted(){
            this.init();
            this.cursor = this.maxId;
        },
        data: ()=>{
            return {
                contain:true,
                maxId: maxId,
                payObj: {
                    price: 0.01,
                    remark: "给你一拳！",
                    sn: "",
                    urlScheme: "",
                    fullUrlScheme: "",
                    time: 300,
                    timeText: "300秒",
                },
                qrcodeApi:"https://qun.qq.com/qrcode/index?data=",//二维码api
                dialogVisible: false,
                webTitle: 'fly-捐赠Website',
                form: {
                    price:{
                        title: '捐赠金额',
                        placeholder: '请输入捐赠金额',
                        val: 0.01,
                        step:0.01
                    },
                    remark:{
                        title: '备注',
                        placeholder: '请输入备注',
                        val: "给你一拳！"
                    }
                },
                tableData:[

                ],
                cursor:maxId,
                timeText: "300秒",
                timeInterval: null,
            }
        },
        methods:{
            init(){
                this.getList(this.cursor);
            },
            gotoUrl(){
                window.location.href = this.payObj.urlScheme;
            },
            handleClose(done) {
                this.dialogVisible = false;
                try {
                    clearInterval(this.timeInterval);
                } catch (e) {
                    console.log(e)
                } finally {
                    this.tableData = [];
                    this.maxId+=100;
                    this.cursor = this.maxId;
                    this.init();
                }
            },
            createPay(){
                // 自动补0 两位
                this.form.price.val = parseFloat(this.form.price.val).toFixed(2);
                axios.post('/alipay/api/createPay', {
                    price: this.form.price.val,
                    remark: this.form.remark.val,
                }).then(res=>{
                    if (res.data.code === 1){
                        this.$message({
                            message: res.data.message,
                            type: 'success'
                        });
                        this.dialogVisible = true;
                        this.payObj = res.data.data;
                        this.payObj.fullUrlScheme = this.qrcodeApi + encodeURIComponent(this.payObj.urlScheme);
                        this.payObj.time = 300;

                        // 创建一个定时器
                        this.timeInterval = setInterval(()=>{
                            this.payObj.time--
                            this.timeText = this.payObj.time + "秒"
                            this.checkPay1()
                            if (this.payObj.time <= 0){
                                clearInterval(this.timeInterval);
                            }
                        },1000);

                    }else{
                        this.$message({
                            message: res.data.error_msg,
                            type: 'error'
                        });
                    }
                }).catch(err=>{

                })
            },
            loadMore(){
                this.getList(this.cursor);
            },
            getList(cursor,limit=3){
                axios.post('/alipay/api/list', {
                    cursor: this.cursor,
                    limit: limit,
                    contain: this.contain
                }).then(res=>{
                    this.cursor = res.data.data.cursor;
                    this.contain = false;
                    // push
                    res.data.data.list.forEach(item=>{
                        this.tableData.push(item);
                    })
                }).catch(err=>{

                })
            },
            getDateTime(time = null){
                /*if (time === null){
                    //获取当前时间戳
                    time = Date.parse(new Date());
                }else {
                    time = time * 1000;
                }*/
                // 直接转成时间格式
                let Y,M,D,h,m,s;
                let date = new Date(time);
                Y = date.getFullYear() + '-';
                M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
                // 补0操作
                D = (date.getDate() < 10 ? '0'+(date.getDate()) : date.getDate()) + ' ';
                h = (date.getHours() < 10 ? '0'+(date.getHours()) : date.getHours()) + ':';
                m = (date.getMinutes() < 10 ? '0'+(date.getMinutes()) : date.getMinutes()) + ':';
                s = (date.getSeconds() < 10 ? '0'+(date.getSeconds()) : date.getSeconds());
                return Y+M+D+h+m+s;
            },
            _checkPay(sn, price, startTime = null, endTime=null){
                return axios.post('/alipay/api/checkPay', {
                    trans_memo: sn,
                    trans_amount: price,
                    start_time: this.getDateTime(startTime),
                    end_time: this.getDateTime(endTime),
                })
            },
            __checkPay(r1){
                return r1.then(res=>{
                    if (res.data.code === 1){
                        this.$message({
                            message: res.data.message,
                            type: 'success'
                        });
                        // 清除定时器
                        try {
                            clearInterval(this.timeInterval);
                            this.dialogVisible = false;
                        } catch (e) {
                        } finally {
                            // 刷新列表
                            this.tableData = [];
                            this.cursor = this.maxId;
                            this.getList()
                        }
                    }else{
                        this.$message({
                            message: res.data.error_msg,
                            type: 'error'
                        });
                    }
                }).catch(err=>{

                })
            },
            checkPay1(){
                let cx = this._checkPay(this.payObj.sn, this.payObj.price, parseInt(this.payObj.timestamp) * 1000, parseInt(this.payObj.timestamp) * 1000 + 15*60*1000);
                this.__checkPay(cx);
            },
            checkPay2(index, rows){
                const row = this.tableData[index];
                let cx = this._checkPay(row.sn, row.price, parseInt(row.created_at) * 1000, parseInt(row.created_at) * 1000 + 15*60*1000);
                cx.then(res =>{
                    if (res.data.code === 1){
                        let _html = "";
                        // 循环对象
                        for (let key in res.data.data){
                            _html += "<p><b>" + key + "：" + res.data.data[key] + "</b></p>";
                        }

                        this.$alert(_html, '支付回执', {
                            dangerouslyUseHTMLString: true
                        });
                    }else{
                        this.$message({
                            message: res.data.error_msg,
                            type: 'error'
                        });
                    }
                }).catch(err=>{
                    console.log(err)
                })
            },
            checkPay(index, rows){
                const row = rows[index];
                let cx = this._checkPay(row.sn, row.price, parseInt(row.created_at) * 1000, parseInt(row.created_at) * 1000 + 15*60*1000);
                this.__checkPay(cx)
            }
        },
        watch:{
            // 表格数据变化
            /*tableData:{
                handler(val,oldVal){
                    const tableBody = document.querySelector('.dataList');
                    console.log(tableBody)
                    if (tableBody.scrollHeight - tableBody.scrollTop <= tableBody.clientHeight) {
                        if (this.cursor){
                            this.getList(this.cursor);
                        }
                    }
                },
                deep:true
            }*/
        }
    })
</script>

<style>

    :root{
        --color1: #ffffff;
        --contentColor: #00000030;
        --backgroundColor: linear-gradient(45deg, #e8ccff,#ffc);
    }

    .title{
        font-size: 30px;
        font-weight: bold;
        text-align: center;
        padding: 20px;
        color: var(--color1);
    }

    /*媒体查询*/
    @media screen and (max-width: 1200px) {
        #container{
            width: 100%!important;
        }
    }



    .flex{
        display: flex;
        justify-content: space-between;
    }
    .text-bold{
        font-weight: bold;
    }
    .el-form-item>label{
        font-weight: bold;
        color: white;
    }
    *{
        margin: 0;
        padding: 0;
    }
    html{
        width: 100%;
        height: 100%;
    }
    #container {
        width: 100vh;
        background-color: var(--contentColor);
        background-size: cover;
        margin: 18px auto;
        border-radius: 40px;
        background-clip: border-box;
        background-repeat: no-repeat;
    }
    html{
        background: var(--backgroundColor);
    }
    #margin-top30{
        margin-top: 30px;
    }
    .he1{

    }
    .he1::after{
        content: "";
        display: block;
        width: 100%;
        height: 1px;
        margin-top: 10px;
    }
</style>

</body>
</html>
