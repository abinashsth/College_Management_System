<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        return view('account.index');
    }

    public function fees()
    {
        return view('account.fees');
    }

    public function payments()
    {
        return view('account.payments');
    }
}
