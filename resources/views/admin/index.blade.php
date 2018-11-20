@extends('admin.layouts.app')

@section('content')

    <el-main>
        <br>
    </el-main>

@endsection

@section('script')
    <script>
        new Vue({
            delimiters: ['<%', '%>'],
            el: '#app',
            data:{
                main:123456
            },
            mounted:function(){
                this.showerr();
            },
        })
    </script>
@endsection
