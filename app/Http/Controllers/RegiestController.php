<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegiestController extends Controller
{

    public function createPage () {
        return view('regiest');
    }


    public function store (Request $request) {

        [
            'username' => $username,
        ] = $request->input();

        $username = $request->username;
        $email = $request->email;
        $password = $request->password;

        return $email;
    }

}
