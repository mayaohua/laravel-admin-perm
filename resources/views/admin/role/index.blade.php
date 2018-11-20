@extends('admin.layouts.app')

@section('content')

    <el-main>

            <el-row>
                <el-button :disabled="!multipleSelection.length" type="text" @click="handleDelSelection">删除选中</el-button>
                <div style="float: right">
                    <el-input  v-model="search" style="width:200px;" clearable  placeholder="输入角色名称查找"></el-input>
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
                        prop="show_name"
                        label="角色名称"
                        >
                </el-table-column>
                <el-table-column
                        prop="name"
                        label="角色操作名称"
                        >
                </el-table-column>
                <el-table-column
                        prop="guard_name"
                        label="角色分组">
                </el-table-column>

                <el-table-column
                        label="角色权限">
                    <template slot-scope="scope">
                        <el-popover trigger="hover" placement="top" style="display: inline-block;" v-for="perm in scope.row.permissions">
                            <p>权限: <% perm.show_name %></p>
                            <div slot="reference" class="name-wrapper" style="margin:0 0 4px 4px;">
                                <el-tag size="medium"><% perm.show_name %></el-tag>
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
                    window.location.href = "/admin/role/"+row.id+"/edit";
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
                    axios.delete('/admin/role/'+ids)
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
