@extends('admin.layouts.app')

@section('content')

    <el-main>

        <el-form label-position="top"  label-width="80px" :model="formdata" :rules="rules" ref="formdata">
            <el-form-item label="角色名称" prop="show_name" :error="errormsg.show_name" >
                <el-input v-model="formdata.show_name" placeholder="角色名称"></el-input>
            </el-form-item>

            <el-form-item label="角色操作" prop="name" :error="errormsg.name" >
                <el-input v-model="formdata.name" placeholder="角色操作名称"></el-input>
            </el-form-item>

            <el-form-item label="角色权限" prop="perms" :error="errormsg.permissions">
                <el-tree
                        :data="perms"
                        show-checkbox
                        default-expand-all
                        node-key="id"
                        ref="tree"
                        :default-checked-keys="formdata.permissions"
                        highlight-current
                        :props="defaultProps"
                >
                </el-tree>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" @click="submitForm('formdata')">立即修改</el-button>
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
                formdata:eval(<?php echo json_encode($info) ?>),
                errormsg:{
                    show_name : "",
                    name: "",
                    permissions: "",
                },
                rules: {
                    show_name: [
                        { required: true, message: '角色名称不能为空', trigger: 'change' }
                    ],
                    name: [
                        { required: true, message: '角色操作不能为空', trigger: 'change' }
                    ],
                },
                perms:eval(<?php echo json_encode($perms) ?>),
                defaultProps:{
                    children: 'children',
                    label: 'label',
                }
            },
            mounted:function(){
                console.log(this.formdata);
                this.showerr();
            },
            methods: {
                submitForm: function(formName){
                    var vm = this;
                    vm.$message.close();
                    this.$refs[formName].validate(function(valid) {
                        if (valid) {
                            //判断权限树是否选中
                            vm.formdata.permissions = vm.$refs.tree.getCheckedKeys();
                            if(!vm.formdata.permissions.length){
                                vm.errormsg.permissions = "请至少选择一个权限";
                                return false;
                            }
                            const loading = vm.$loading({
                                lock: true,
                                text: 'Loading',
                                spinner: 'el-icon-loading',
                                background: 'rgba(0, 0, 0, 0.7)'
                            });
                            vm.errormsg = {};
                            setTimeout(function() {
                                axios.put("/admin/role/{{$id}}",vm.formdata).then(function (reason) {
                                    if(reason.data.code == 0){
                                        vm.$message({
                                            type: 'success',
                                            message: '修改成功'
                                        });
                                        setTimeout(function(){
                                            window.location.href = "{{  route('admin.role.index') }}";
                                        },1000);
                                    }else{
                                        vm.$message({
                                            type: 'error',
                                            message: '修改失败'
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
                                            message: '修改失败'
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
