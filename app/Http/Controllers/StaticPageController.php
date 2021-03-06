<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Cookie;

class StaticPageController extends Controller
{
    public function __construct () {

        $this->middleware('guest', [
            'only' => ['regiest', 'login']
        ]);
        
    }

    
    public function regiest (Request $request) {
        return view('regiest', [
            'type' => 'regiest'
        ]);
    }


    public function tPassword (Request $request) {
        return view('password.reset');
    }


    public function login (Request $request) {

        $key = config('custom.session.user');
        $data = session()->get($key);
        $data['prevServe'] = $request->serve == null 
            ? null
            : urldecode($request->serve);

        session([
            $key => $data
        ]);

        return view('login');
    }


    public function indexPage (Request $request) {
        
        $msg = session('msg');

        return view('index', compact('msg'));

    }
}
