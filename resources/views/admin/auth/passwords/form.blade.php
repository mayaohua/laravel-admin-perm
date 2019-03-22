@extends('admin.layouts.app')

@section('content')

<el-main>

    <el-form label-position="top" label-width="80px" :model="formdata" :rules="rules" ref="formdata">
        <el-form-item label="原密码" prop="old_password" :error="errormsg.old_password">
            <el-input type="password" v-model="formdata.old_password" placeholder="原密码"></el-input>
        </el-form-item>

        <el-form-item label="新密码" prop="password" :error="errormsg.password">
            <el-input type="password" v-model="formdata.password" placeholder="新密码"></el-input>
        </el-form-item>

        <el-form-item label="确认密码" prop="password_confirmation" :error="errormsg.password_confirmation">
            <el-input type="password" v-model="formdata.password_confirmation" placeholder="确认密码"></el-input>
        </el-form-item>

        <el-form-item>
            <el-button type="primary" @click="submitForm('formdata')">立即修改</el-button>
        </el-form-item>

        <el-form-item style="color:#666">
            忘记密码？进入<a href="?action=1">邮箱验证</a>
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
                old_password: "",
                password: "",
                password_confirmation: "",
            },
            errormsg: {
                old_password: "",
                password: "",
                password_confirmation: "",
            },
            rules: {
                old_password: [{
                    required: true,
                    message: '原密码不能为空',
                    trigger: 'change'
                }],
                password: [{
                    required: true,
                    message: '新密码不能为空',
                    trigger: 'change'
                }],
                password_confirmation: [{
                    required: true,
                    message: '确认密码不能为空',
                    trigger: 'change'
                }],
            },
        },
        mounted: function() {
            this.appInit();
        },
        methods: {
            submitForm: function(formName) {
                this.errormsg.password_confirmation = this.errormsg.password = "";
                var vm = this;
                vm.$message.close();
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        if (vm.formdata.password !== vm.formdata.password_confirmation) {
                            vm.$nextTick(function() {
                                vm.errormsg.password_confirmation = vm.errormsg.password = "两次密码不一致";
                            })
                            //return false;
                        }
                        const loading = vm.$loading({
                            lock: true,
                            text: 'Loading',
                            spinner: 'el-icon-loading',
                            background: 'rgba(0, 0, 0, 0.7)'
                        });
                        vm.errormsg = {};
                        setTimeout(function() {
                            axios.post("password/form", vm.formdata).then(function(reason) {
                                if (reason.data.code == 10003) {
                                    vm.$message({
                                        type: 'warning',
                                        message: reason.data.error
                                    });
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    vm.$message({
                                        type: 'error',
                                        message: reason.data.error
                                    });
                                }
                                loading.close();
                            }).catch(function(err) {
                                console.log(err.response)
                                if (err.response.status == 422) {
                                    vm.errormsg = err.response.data.errors;
                                } else {
                                    vm.$message({
                                        type: 'error',
                                        message: '修改失败'
                                    });
                                }
                                loading.close();
                            })
                        }, 300);
                    }
                });
            }

        }
    })
</script>

@endsection


@section('css')


@endsection 