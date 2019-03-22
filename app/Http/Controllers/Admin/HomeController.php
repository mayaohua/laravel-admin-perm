<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class HomeController extends Controller
{
    protected $redirectTo = '/admin/index';    //邮箱验证后的跳转路径

    public function __construct()
    {
        $this->redirectTo = '/'.config('webset.web_indexname').'/index';
    }

    public function index()
    {
        return view('admin.home');
    }

    public function show_password_view(Request $request){
        $type = Input::get('action',0);
         //            return $request->user()->hasVerifiedEmail()
        //                ? view('admin.auth.passwords.email')
        //                : view('admin.auth.passwords.form');
        return $type == 1 ? view('admin.auth.passwords.email') : view('admin.auth.passwords.form');
    }

    /**
     * form方式修改密码操作
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form_password(Request $request){

        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $old_password = $request->get('old_password');
        $validator->after(function ($validator) use($old_password){
            if(!Hash::check($old_password,auth()->user()->getAuthPassword()))
                $validator->errors()->add('old_password', '原始密码不正确');
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $request->user()->password = Hash::make($request->password);
        $request->user()->setRememberToken(Str::random(60));
        $request->user()->save();
        event(new PasswordReset($request->user()));
        Auth::guard()->logout($request->user());
        $request->session()->invalidate();
        return $this->error(10003);

    }

    /**
     * email方式修改密码操作
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function email_password(Request $request){
        $uid = auth()->user()->id;
            //发送验证码
            if($request -> has('send_code')){
                if(Redis::exists($uid.'_auth_time')){
                    return $this->error(10004);
                }

                if(Redis::get($uid.'_auth_count')>3){
                    return $this->error(10006);
                }

                //生成验证码
                $code = $this->makVaildateCode();

                //保存数据到redis中
                Redis::setex($uid.'_auth', 60*60*2, $code);   //验证码
                Redis::incr($uid.'_auth_time');               //验证码发送凭证
                Redis::incr($uid.'_auth_count');              //发送次数
                Redis::expire($uid.'_auth_time',60);          //验证码有效时间
                Redis::expire($uid.'_auth_count',60*60*24);    //下次验证时间
                $name =auth()->user()->name;
                $time = '2小时';
                $date = Carbon::now()->toDateString();
                $flag = Mail::send('admin.auth.emailtpl.resetpwd',['name'=>$name,'time'=>$time,'code'=>$code,'date'=>$date],function($message){
                    $to = auth()->user()->email;
                    $message ->to($to)->subject('513学院修改密码验证');
                });
                if(!$flag){
                    return $this->success();
                }else{
                    return $this->error(10005);
                }
            }elseif ($request -> has('code')){
                //验证验证码
                $code = $request->get('code');

                if(Redis::exists($uid.'_auth')){
                    if($code === Redis::get($uid.'_auth')){
                        $token = $uid. date('Ymd'). rand(10000000,99999999);
                        $entoken = encrypt($token);
                        Redis::incr($token.'_auth_token');
                        Redis::expire($token.'_auth_token',60*60*2);
                        return $this->success(['token' => $entoken]);
                    }else{
                        return $this->error(10008);
                    }

                }else{
                    return $this->error(10007);
                }
            }elseif ($request -> has('token')){
                //修改新密码
                $entoken = $request->get('token');
                try {
                    $token = decrypt($entoken);
                    if(!Redis::exists($token.'_auth_token')){
                        return $this->error(10009);   //邮箱验证token不正确
                    }
                } catch (DecryptException $e) {
                    return $this->error(10009);       //邮箱验证token不正确
                }
                $vadata = $this->validate($request,['password' => 'required|min:6']);
                $request->user()->password = Hash::make($vadata['password']);
                $request->user()->setRememberToken(Str::random(60));
                $request->user()->save();
                Redis::del($uid.'_auth');
                Redis::del($uid.'_auth_time');
                Redis::del($uid.'_auth_count');
                Redis::del($token.'_auth_token');
                event(new PasswordReset($request->user()));
                Auth::guard()->logout($request->user());
                $request->session()->invalidate();
                return $this->error(10003);
            }
    }


    /**
     * email身份验证
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function email_validate(Request $request){
        if($request->isMethod('get')){
            return view('admin.auth.passwords.validate_email');
        }else{
            $uid = auth()->user()->id;
            //发送验证码
            if($request -> has('send_code')){
                if(Redis::exists($uid.'_auth_time')){
                    return $this->error(10004);
                }

                if(Redis::get($uid.'_auth_count')>3){
                    return $this->error(10006);
                }

                //生成验证码
                $code = $this->makVaildateCode();

                //保存数据到redis中
                Redis::setex($uid.'_auth', 60*60*2, $code);   //验证码
                Redis::incr($uid.'_auth_time');               //验证码发送凭证
                Redis::incr($uid.'_auth_count');              //发送次数
                Redis::expire($uid.'_auth_time',60);          //验证码有效时间
                Redis::expire($uid.'_auth_count',60*60*24);    //下次验证时间
                $name =auth()->user()->name;
                $time = '2小时';
                $date = Carbon::now()->toDateString();
                $flag = Mail::send('admin.auth.emailtpl.validate',['name'=>$name,'time'=>$time,'code'=>$code,'date'=>$date],function($message){
                    $to = auth()->user()->email;
                    $message ->to($to)->subject('513学院身份验证');
                });
                if(!$flag){
                    return $this->success();
                }else{
                    return $this->error(10005);
                }
            }elseif ($request -> has('code')){
                //验证验证码
                $code = $request->get('code');

                if(Redis::exists($uid.'_auth')){
                    if($code === Redis::get($uid.'_auth')){
                        $token = $uid. date('Ymd'). rand(10000000,99999999);
                        $entoken = encrypt($token);
                        Redis::incr($token.'_auth_token');
                        Redis::expire($token.'_auth_token',60*60*2);
                        return $this->success(['token' => $entoken]);
                    }else{
                        return $this->error(10008);
                    }

                }else{
                    return $this->error(10007);
                }
            }elseif ($request -> has('token')){
                //修改新密码
                $entoken = $request->get('token');
                try {
                    $token = decrypt($entoken);
                    if(!Redis::exists($token.'_auth_token')){
                        return $this->error(10009);   //邮箱验证token不正确
                    }
                } catch (DecryptException $e) {
                    return $this->error(10009);       //邮箱验证token不正确
                }
                $vadata = $this->validate($request,['password' => 'required|min:6']);
                $request->user()->email_verified_at = Carbon::now();
                $request->user()->password = Hash::make($vadata['password']);
                $request->user()->setRememberToken(Str::random(60));
                $request->user()->save();
                Redis::del($uid.'_auth');
                Redis::del($uid.'_auth_time');
                Redis::del($uid.'_auth_count');
                Redis::del($token.'_auth_token');
                event(new PasswordReset($request->user()));
                Auth::guard()->logout($request->user());
                $request->session()->invalidate();
                return $this->error(10003);
            }
        }
    }


    private function makVaildateCode($len = 6){
        $min = pow(10 , ($len - 1));
        $max = pow(10, $len) - 1;
        return rand($min, $max);
    }


}
