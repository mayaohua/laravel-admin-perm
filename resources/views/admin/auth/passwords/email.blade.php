@extends('admin.layouts.app')

@section('content')

<el-main>


    <el-steps :active="active" finish-status="success" style="width: 40%;margin: 20px auto 50px;">
        <el-step title="身份验证"></el-step>
        <el-step title="密码输入"></el-step>
        <el-step title="完成修改"></el-step>
    </el-steps>

    <el-form label-position="top" label-width="80px" :model="formdata" ref="formdata" style="width: 40%;margin: 0 auto;position: relative;">

        <el-form-item class="transition-box" v-show="active == 1" label="将会向您的邮箱账号xiyizia@163.com发送验证码" :error="errormsg.old_pwd">
            <el-input placeholder="请输入验证码" v-model="formdata.code" prop="code" maxlength="6">
                <el-button slot="append" @click="sendCode" style="width: 120px;" :disabled="code_max_time != code_now_time" :class="{'my-code':code_max_time == code_now_time}"><% code_msg %></el-button>
            </el-input>
        </el-form-item>
        <transition name="el-zoom-in-center">
            <div v-show="active == 2">
                <el-form-item label="新密码" :error="errormsg.password">
                    <el-input v-model="formdata.password" placeholder="新密码"></el-input>
                </el-form-item>

                <el-form-item label="确认密码" prop="password_re" :error="errormsg.password_re">
                    <el-input v-model="formdata.password_re" placeholder="确认密码"></el-input>
                </el-form-item>
            </div>

        </transition>

        <transition name="el-zoom-in-center">
            <div v-show="active == 3" style="text-align: center;margin-top: 150px;">
                <i class="el-icon-success" style="color:#67c23a;font-size: 100px;"></i>
                <p style="margin-top:20px;font-size: 20px;">修改成功即将进入登录页面</p>
            </div>
        </transition>


        <el-form-item style="margin-bottom: 50px;text-align: center;position: absolute; top:300px;" v-show="active != 3">
            <el-button type="primary" @click="next('formdata')" :disabled="formdata.code == ''">下一步</el-button>
        </el-form-item>

    </el-form>

</el-main>


@endsection

@section('script')
<script>
    new Vue({
        delimiters: ['<%', '%>'],
        el: '#app',
        data: {
            formdata: {
                code: "",
                password: "",
                password_re: "",
                pid: "",
                brief: "",
            },
            active: 1,
            errormsg: {
                code: "",
                password: "",
                password_re: "",
                pid: "",
                brief: ""
            },
            code_msg: '发送验证码',
            code_max_time: 60,
            code_now_time: 60,
            interTime: 0,
            rules: {
                code: [{
                    required: true,
                    message: '验证码不能为空',
                    trigger: 'change'
                }],

            },
            csrftoken: '{{csrf_token()}}',

        },
        mounted: function() {
            this.appInit();
        },
        methods: {
            next: function(formName) {
                if (this.active == 1) {
                    //验证码验证
                    this.vailCode();
                } else if (this.active == 2) {
                    this.setPwd();
                }
                //this.active++;
            },
            vailCode: function() {
                var vm = this;
                this.myhttp('password/email', {
                    'code': this.formdata.code
                }, function(reason) {
                    vm.$message({
                        type: 'success',
                        message: '邮箱验证通过'
                    });
                    vm.formdata.token = reason.data.token;
                    vm.active++;
                }, function(reason) {
                    vm.$message({
                        type: 'error',
                        message: reason.error
                    });
                }, function(reason) {
                    vm.$message({
                        type: 'error',
                        message: '验证码token已失效'
                    });
                });
            },
            setPwd: function() {
                var vm = this;

                if (!this.formdata.token) {
                    this.active--;
                    return;
                }

                if (!this.formdata.password.length) {
                    vm.$message({
                        type: 'error',
                        message: '密码不能为空'
                    });
                    return;
                }
                if (!this.formdata.password_re.length) {
                    vm.$message({
                        type: 'error',
                        message: '确认密码不能为空'
                    });
                    return;
                }
                if (this.formdata.password_re !== this.formdata.password) {
                    vm.$message({
                        type: 'error',
                        message: '两次密码不一致'
                    });
                    return;
                }
                var data = {
                    token: this.formdata.token,
                    password: this.formdata.password,
                }

                this.myhttp('password/email', data, function(reason) {

                }, function(reason) {
                    if (reason.code == 10003) {
                        vm.$message({
                            type: 'success',
                            message: reason.error
                        });
                        vm.active++;
                        setTimeout(function() {
                            window.location.href = "/{{config('webset.web_indexname')}}"
                        }, 2000);
                    } else {
                        vm.$message({
                            type: 'error',
                            message: reason.error
                        });
                        vm.active--;
                    }
                }, function(response) {
                    if (response.status == 422) {
                        vm.$message({
                            type: 'error',
                            message: response.data.errors.password
                        });
                    } else {
                        vm.$message({
                            type: 'error',
                            message: '验证码token已失效'
                        });
                        this.active--;
                    }

                });
            },
            sendCode: function() {
                if (this.code_now_time != this.code_max_time) {
                    return;
                }
                var vm = this;
                this.myhttp("password/email", {
                    'send_code': 1
                }, function(reason) {
                    vm.$message({
                        type: 'success',
                        message: '发送成功，请耐心等待邮件'
                    });
                    vm.code_msg = vm.code_max_time + "s后再试";
                    vm.settime();
                });
            },
            settime: function() {
                var vm = this;
                if (vm.code_now_time == 0) {
                    vm.code_now_time = vm.code_max_time;
                    vm.code_msg = "发送验证码";
                } else {
                    vm.code_msg = vm.code_now_time + "s后再试";
                    vm.code_now_time--;
                    setTimeout(function() {
                        vm.settime()
                    }, 1000)
                }
            },
            myhttp: function(url, data, successback, errorback, failback) {
                var vm = this;
                vm.$message.close();
                const loading = vm.$loading({
                    lock: true,
                    text: 'Loading',
                    spinner: 'el-icon-loading',
                    background: 'rgba(0, 0, 0, 0.7)'
                });
                vm.errormsg = {};
                axios.post(url, data).then(function(reason) {
                    if (reason.data.code == 0) {
                        successback && successback(reason.data);
                    } else {
                        if (errorback) {
                            errorback(reason.data);
                        } else {
                            vm.$message({
                                type: 'error',
                                message: reason.data.error
                            });
                        }
                    }
                    loading.close();
                }).catch(function(err) {
                    if (failback) {
                        failback(err.response);
                    } else {
                        if (err.response.status == 422) {
                            vm.errormsg = err.response.data.errors;
                        } else {
                            vm.$message({
                                type: 'error',
                                message: '发送失败'
                            });
                        }
                    }
                    loading.close();

                })
            }
        },
        computed: {
            btnDis: function() {
                var code = Number(this.formdata.code);
                return isNaN(code) || String(code).length !== 6;
            }
        }
    })
</script>

@endsection


@section('css')
.my-code{
background: #409EFF!important;
color:white!important;
}

@endsection 