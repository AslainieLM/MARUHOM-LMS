<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController {
    public function index(){
        return view('index');
    }

    public function about(){
        return view('about');
    }

    public function contact(){
        return view('contact');
    }
}
