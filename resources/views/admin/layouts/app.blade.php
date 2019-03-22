<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">

    <!-- 引入样式 -->
    <link rel="stylesheet" href="{{ asset('css/element-ui.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
        }

        html {
            height: 100%;
            box-sizing: border-box;
            transition: background-color .3s cubic-bezier(.25, .8, .25, 1)
        }

        html *,
        html :after,
        html :before {
            box-sizing: inherit
        }

        body {
            min-height: 100%;
            margin: 0;
            position: relative;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            font-family: Roboto, Noto Sans, -apple-system, BlinkMacSystemFont, sans-serif
        }

        ::selection {
            color: #ffffff;
            background: #ff0000;
        }

        ::-moz-selection {
            color: #ffffff;
            background: #ff0000;
        }

        .el-header,
        .el-footer {
            background-color: #B3C0D1;
            color: #333;
            text-align: center;
            line-height: 60px;
        }

        .el-aside {
            background-color: white;
            color: #333;
            text-align: center;
            line-height: 200px;
        }

        body>.el-container {
            margin-bottom: 40px;
        }

        .el-container:nth-child(5) .el-aside,
        .el-container:nth-child(6) .el-aside {
            line-height: 260px;
        }

        .el-container:nth-child(7) .el-aside {
            line-height: 320px;
        }

        .header-operations {
            display: inline-block;
            float: right;
            padding-right: 30px;
            height: 100%;
            margin: 0;
        }

        .header-operations li {
            color: #fff;
            display: inline-block;
            vertical-align: middle;
            padding: 0 10px;
            margin: 0 10px;
            line-height: 80px;
            cursor: pointer;
        }

        .header-operations li a {
            color: #fff;
        }

        .main-container {
            flex: 1;
        }

        .app-container {
            display: flex;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="el-loading-mask is-fullscreen" style="z-index: 2000;    background-color: rgba(255,255,255,1);" ref="appcontainershade">
            <div class="el-loading-spinner">
                <svg viewBox="25 25 50 50" class="circular">
                    <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                </svg>
            </div>
        </div>
        <el-container class="app-container" ref="appcontainer" style="diplay:none">
            <el-header height="80px" style="background-color: rgb(64, 158, 255);display: flex;justify-content:space-between;line-height: 80px;">
                <img style="vertical-align: middle; width: 200px; margin-top: -10px;" src="https://www.baidu.com/img/superlogo_c4d7df0a003d3db9b65e9ef0fe6da1ec.png" alt="element-logo" class="header-logo">
                <ul class="header-operations">
                    {{--<li>切换主题色</li>--}}
                    {{--<li class="header-download is-available">--}}
                    {{--下载主题--}}
                    {{--</li> <li>帮助</li>--}}
                    <li><a href="/{{config('webset.web_indexname')}}/password" style="text-decoration: none;display: inline-block;">修改密码</a></li>
                    <li onclick="event.preventDefault();document.getElementById('logout-form').submit();">退出</li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

            </el-header>
            <el-container class="main-container">
                <el-aside width="300px">
                    <el-menu default-active="{{Route::currentRouteName()}}" unique-opened="true" style="text-align: left;height: 100%;">
                        @foreach(Auth::user()->getDirectPermissions() as $value)
                        @if($value->level == 1)
                        <el-menu-item style="padding-left:20px;" index="{{$value->name}}" onclick="window.location.href='{{route($value->name)}}'">
                            <i class="{{$value->icon}}" style="vertical-align: middle; margin-right: 5px; width: 24px; text-align: center; font-size: 18px;"></i>
                            <span slot="title">{{$value->show_name}}</span>
                        </el-menu-item>
                        @endif
                        @endforeach
                        @foreach(Auth::user()->getPermissionsViaRoles() as $value)
                        @if($value->level == 1)
                        <el-submenu index="{{$value->name}}">
                            <template slot="title"><i class="{{$value->icon}}" style="vertical-align: middle;margin-right: 5px;width: 24px;text-align: center;font-size: 18px;"></i>{{$value->show_name}}</template>
                            <el-menu-item-group>
                                @foreach(Auth::user()->getPermissionsViaRoles() as $val)
                                @if($val->level == 2 && $val->pid == $value->id)
                                <el-menu-item index="{{$val->name}}" onclick="window.location.href='{{route($val->name)}}'">{{$val->show_name}}</el-menu-item>
                                @endif
                                @endforeach
                            </el-menu-item-group>
                        </el-submenu>
                        @endif
                        @endforeach

                    </el-menu>
                </el-aside>
                <el-container>
                    <el-breadcrumb separator-class="el-icon-arrow-right" style="padding: 24px;border-bottom: 1px solid #ebeef5;">
                        @if($perm['parent'])
                        <el-breadcrumb-item><b>{{$perm['parent']['show_name']}}</b></el-breadcrumb-item>
                        @endif
                        <el-breadcrumb-item>{{$perm['show_name']}}</el-breadcrumb-item>
                    </el-breadcrumb>
                    @yield('content')
                    <el-footer style="line-height: 60px;">
                        <p>Copyright ©深圳市伍壹叁教育科技有限公司 版权所有 粤ICP备18044373号-1</p>
                    </el-footer>
                </el-container>
            </el-container>
        </el-container>
    </div>
    <!-- import Vue before Element -->
    <script src="{{ asset('js/vue.min.js') }}"></script>
    <!-- 引入组件库 -->
    <script src="{{ asset('js/element-ui.js') }}"></script>
    <!-- 网络请求 -->
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <!-- vue配置 -->

    <script>
        // 显示操作时出现的错误
        (function() {
            if ('{{$errors -> first() }}') {
                Vue.prototype.$notify.info({
                    title: '提醒',
                    message: '{{$errors -> first() }}',
                    position: 'bottom-right'
                });
            }
            Vue.prototype.appInit = function() {
                this.$refs.appcontainershade.style.display = 'none';
                this.$refs.appcontainer.$el.display = 'block'
            }
        }())
    </script>

    @yield('script')


</body>

</html> 