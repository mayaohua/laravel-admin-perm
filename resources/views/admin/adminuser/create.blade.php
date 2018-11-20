@extends('admin.layouts.app')

@section('content')

    <el-main>

        <el-form label-position="top"  label-width="80px" :model="formdata" :rules="rules" ref="formdata">
            <el-form-item label="用户名" prop="name" :error="errormsg.show_name" >
                <el-input v-model="formdata.name" placeholder="用户名"></el-input>
            </el-form-item>

            <el-form-item label="登录邮箱" prop="email" :error="errormsg.email" >
                <el-input v-model="formdata.email" placeholder="登录邮箱"></el-input>
            </el-form-item>

            <el-form-item label="分配角色" prop="role" :error="errormsg.role" >
                <el-select v-model="formdata.role" placeholder="请选择角色">
                    <el-option
                            v-for="item in roles"
                            :key="item.id"
                            :label="item.show_name"
                            :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" @click="submitForm('formdata')">立即创建</el-button>
            </el-form-item>

        </el-form>

    </el-main>

@endsection



@section('script')
    <script>
        new Vue({
            delimiters: ['<%', '%>'],
            el: '#app',
            data:{
                formdata:{
                    email : "",
                    name: "",
                    role:"",
                },
                errormsg:{
                    email : "",
                    name: "",
                    role: "",
                },
                rules: {
                    email: [
                        { required: true, message: '邮箱不能为空', trigger: 'change' }
                    ],
                    name: [
                        { required: true, message: '用户名不能为空', trigger: 'change' }
                    ],
                    role : [
                        { required: true, message: '角色不能为空', trigger: 'change' }
                    ],
                },
                roles:eval(<?php echo json_encode($roles) ?>),
                defaultProps:{
                    children: 'children',
                    label: 'label',
                }
            },
            mounted:function(){
                this.showerr();
            },
            methods: {
                submitForm: function(formName){
                    var vm = this;
                    vm.$message.close();
                    this.$refs[formName].validate(function(valid) {
                        if (valid) {
                            const loading = vm.$loading({
                                lock: true,
                                text: 'Loading',
                                spinner: 'el-icon-loading',
                                background: 'rgba(0, 0, 0, 0.7)'
                            });
                            vm.errormsg = {};
                            setTimeout(function() {
                                axios.post("{{route('admin.adminuser.store')}}",vm.formdata).then(function (reason) {
                                    if(reason.data.code == 0){
                                        vm.$message({
                                            type: 'success',
                                            message: '添加成功'
                                        });
                                        setTimeout(function(){
                                            window.location.reload();
                                        },1000);
                                    }else{
                                        vm.$message({
                                            type: 'error',
                                            message: '添加失败'
                                        });
                                    }
                                    loading.close();
                                }).catch(function (err) {
                                    console.log(err.response)
                                    if(err.response.status == 422){
                                        vm.errormsg = err.response.data.errors;
                                    }else{
                                        vm.$message({
                                            type: 'error',
                                            message: '添加失败'
                                        });
                                    }
                                    loading.close();
                                })
                            }, 300);
                        }
                    });
                },

            }
        })
    </script>
@endsection
