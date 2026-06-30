<?php

namespace App\Controllers;

class Settings extends BaseController
{
    public function index()
    {

        $data = [
            'name'  => session('name'),
            'email' => session('email'),
            'role'  => session('role'),
        ];
 
        $content = view('settings/index', $data);
        return view('layouts/Layout', ['title' => 'Settings', 'content' => $content]);
    }
}
