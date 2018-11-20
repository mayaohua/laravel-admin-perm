<?php
namespace App\Http\Controllers;



trait ApiResponse{

    private $codeArr = [
        0 => '请求成功',
        10001 => '不能删除管理员角色',
    ];

    /**
     *  '200' => "请求正确，请求返回正确的结果",
        '201' => "请求正确，资源被正确的创建",
        '202' => "请求正确，结果正在处理中",
        '204' => "请求正确，无返回的内容",
     */

    private $statusArr = [
        '400' => "请求错误，参数无效报错",
        '401' => "请求错误，请求未授权",
        '402' => "请求错误，参数无效报错",
        '403' => "请求错误，无权限访问",
        '404' => "请求错误，访问资源不存在",
        '405' => "请求错误，请求方式不正确",
        '500' => "服务器错误"
    ];




    public function success($data = []){
        $data = $this->getJson(0,$data);
        return $this->send($data,200);
    }





    public function error($code,$data = []){
        $data = $this->getJson($code,$data);
        if($data === false){
            return $this -> fail(500);
        }
        return $this->send($data,200);
    }





    public function fail($status = 400){
        if(isset( $this->statusArr[$status])){
            return $this->send($this->statusArr[$status],$status);
        }
        throw new \Exception('status '.$status.' 不在合法的列中');
    }





    private function getJson(int $code = 0,array $data = []){
        if(isset($this->codeArr[$code])){
            if($code == 0){
                return ['data' => $data,'code' => $code];
            }else{
                $message = $this->codeArr[$code];
                return ['data' => $data, 'code' => $code, 'error'=>$message];
            }
        }else{
            return false;
        }
    }





    private function send($data,$status){
        return response($data,$status);
    }


    public function back($code =null){
        return !$code ? $this -> success() : $this -> error($code);
    }

}