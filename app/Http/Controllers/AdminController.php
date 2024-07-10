<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    function index()
    {
        $data = [
            'title' => 'Admin Index Page',
            'name' => auth()->user()->name,
        ];

        return view('admin.index', $data);
    }
}
