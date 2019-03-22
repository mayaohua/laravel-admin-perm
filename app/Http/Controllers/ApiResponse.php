<?php
namespace App\Http\Controllers;



trait ApiResponse
{

    private $codeArr = [
        0 => '请求成功',
        10001 => '不能删除管理员角色',
        10002 => '文件大小不能超过600k',
        10003 => '密码修改成功，请重新登录',
        10004 => '邮箱验证码已发送，请勿频繁操作',
        10005 => '邮箱验证发送失败，请稍后重试',
        10006 => '今日邮箱验证发送次数已达上线，请明天再试',
        10007 => '邮箱验证码已失效',
        10008 => '邮箱验证码不正确',
        10009 => '邮箱验证token不正确',

        10010 => '文件上传失败',
        10011 => '图片大小不能超过2M',
        10012 => '文件验证失败',
        10013 => '清除缓存失败'
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
        //        '402' => "请求错误，参数无效报错",
        '403' => "请求错误，无权限访问",
        '404' => "请求错误，访问资源不存在",
        '405' => "请求错误，请求方式不正确",
        '500' => "服务器错误"
    ];




    public function success($data = [])
    {
        $data = $this->getJson(0, $data);
        return $this->send($data, 200);
    }





    public function error($code, $data = [], $status = 200)
    {
        $data = $this->getJson($code, $data);
        if ($data === false) {
            return $this->fail(500, $data);
        }
        return $this->send($data, $status);
    }





    public function fail($status = 400, $data = [])
    {
        if (isset($this->statusArr[$status])) {
            return $this->send($data ? $data : $this->statusArr[$status], $status);
        }
        throw new \Exception('status ' . $status . ' 不在合法的列中');
    }





    private function getJson(int $code = 0, array $data = [])
    {
        if (isset($this->codeArr[$code])) {
            if ($code == 0) {
                return ['data' => $data, 'code' => $code];
            } else {
                $message = $this->codeArr[$code];
                return ['data' => $data, 'code' => $code, 'error' => $message];
            }
        } else {
            return false;
        }
    }





    private function send($data, $status)
    {
        return response($data, $status);
    }


    public function back($code = null)
    {
        return !$code ? $this->success() : $this->error($code);
    }
}

