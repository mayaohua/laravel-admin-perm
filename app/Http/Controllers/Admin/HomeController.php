<?php

namespace App\Http\Controllers\Admin;


class HomeController extends Controller
{

    public function index()
    {
        return view('admin.index');
    }


}
