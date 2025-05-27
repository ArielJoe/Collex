<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MemberController extends Controller
{
    public function index()
    {
        return view('member.index');
    }
}
