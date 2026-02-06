<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceivingController extends Controller
{
    public function index()
    {
        return view('pages.procurement.receivings-index');
    }

    public function create()
    {
        return view('pages.procurement.receivings-create');
    }

    public function edit($id)
    {
        return view('pages.procurement.receivings-edit', compact('id'));
    }
}
