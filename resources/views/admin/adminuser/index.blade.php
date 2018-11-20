@extends('admin.layouts.app')

@section('content')

    <el-main>

        <el-row>
            <el-button :disabled="!multipleSelection.length" type="text" @click="handleDelSelection">删除选中</el-button>
            <div style="float: right">
                <el-input  v-model="search" style="width:200px;" clearable  placeholder="输入用户名查找"></el-input>
                <el-button type="primary" plain>搜索</el-button>
            </div>

        </el-row>

        <el-table
                :data="tableData"
                stripe
                max-height="800"
                tooltip-effect="dark"
                @selection-change="handleSelectionChange"
                style="width: 100%">
            <el-table-column
                    type="selection"
                    width="55">
            </el-table-column>

            <el-table-column
                    prop="name"
                    label="用户名">

            </el-table-column>

            <el-table-column
                    prop="email"
                    label="登录邮箱">
            </el-table-column>

            <el-table-column
                    label="所在角色">
                <template slot-scope="scope">
                    <el-popover trigger="hover" placement="top" style="display: inline-block;">
                        <p>拥有权限: <% scope.row.role.prems %></p>
                        <div slot="reference" class="name-wrapper" style="margin:0 0 4px 4px;">
                            <el-tag size="medium"><% scope.row.role.show_name %></el-tag>
                        </div>
                    </el-popover>
                </template>
            </el-table-column>

            <el-table-column
                    fixed="right"
                    label="操作"
                    width="150">
                <template slot-scope="scope">
                    <el-button @click="handleClick(scope.row)" type="text" size="small">编辑</el-button>
                    <el-button type="text" style="color:orangered" @click="handleDelOne(scope.row)" size="small">删除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="currentPage"
                :page-sizes="pageSizes"
                :page-size="pageSize"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total"
                style="margin-top:20px;">
        </el-pagination>

    </el-main>

@endsection



@section('script')
    <script>
        new Vue({
            delimiters: ['<%', '%>'],
            el: '#app',
            data:{
                tableData: eval(<?php echo json_encode($list->items()) ?>),
                pageSize: eval(<?php echo json_encode($limit) ?>),
                pageSizes: eval(<?php echo json_encode($limits) ?>),
                total: eval(<?php echo json_encode($list->total()) ?>),
                currentPage: eval(<?php echo json_encode($list->currentPage()) ?>),
                search:'',
                multipleSelection: [],

            },
            mounted:function(){
                this.showerr();
            },
            methods: {
                handleClick:function (row) {
                    window.location.href = "/admin/adminuser/"+row.id+"/edit";
                },
                handleSizeChange:function(val) {
                    window.location.href="?limit="+val+"&page=1"
                },
                handleCurrentChange:function(val) {
                    if(val!= this.currentPage){
                        window.location.href="?limit=100&page="+val
                    }
                },
                handleSelectionChange:function(val) {
                    this.multipleSelection = val;
                },
                handleDelSelection:function(){
                    var vm = this;
                    if(this.multipleSelection.length){
                        this.showDelView(function(){
                            vm.actionDel(vm.multipleSelection);
                        })
                    }
                },
                handleDelOne:function(row){
                    var vm = this;
                    this.showDelView(function(){
                        vm.actionDel(row);
                    })
                },
                showDelView:function(confirm){
                    this.$confirm('删除不可恢复, 是否继续?', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function() {
                        confirm && confirm();
                    }).catch(function(error){
                        console.log(error)
                        console.log('cancel')
                    })
                },
                actionDel:function(data){
                    var ids = [];
                    if(Array.isArray(data)){
                        for(var i = 0;i<data.length;i++){
                            ids.push(data[i].id)
                        }
                    }else{
                        ids.push(data.id)
                    }

                    var vm = this;
                    ids = ids.join(',');
                    const loading = vm.$loading({
                        lock: true,
                        text: 'Loading',
                        spinner: 'el-icon-loading',
                        background: 'rgba(0, 0, 0, 0.7)'
                    });
                    axios.delete('/admin/adminuser/'+ids)
                        .then(function (response) {
                            if(response.data.code == 0){
                                vm.$message({
                                    type: 'success',
                                    message: '删除成功!'
                                });
                                setTimeout(function () {
                                    window.location.reload();
                                },1000);
                            }else{
                                vm.$message({
                                    type: 'error',
                                    message: '删除失败'
                                });
                                loading.close();
                            }

                        })
                        .catch(function (error) {
                            loading.close();
                            vm.$message({
                                type: 'error',
                                message: '删除失败'
                            });
                        });
                },
            }
        })
    </script>
@endsection
