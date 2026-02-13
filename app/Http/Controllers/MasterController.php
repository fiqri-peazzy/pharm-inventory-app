<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function users()
    {
        return view('pages.master.users.index');
    }

    public function roles()
    {
        return view('pages.master.roles.index');
    }
}
