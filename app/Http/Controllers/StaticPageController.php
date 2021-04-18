<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $key = env('USER_SESSION_KEY');
        $data = session()->get($key);
        $data['prevServe'] = \urldecode($request->serve);
        // var_dump($data);
        session([
            $key => $data
        ]);

        return view('login');
    }


    public function indexPage (Request $request) {
        return view('after');
    }
}
