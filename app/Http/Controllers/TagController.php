<?php

namespace App\Http\Controllers;

class TagController extends Controller
{
    public function index()
    {
        return view('placeholder', ['title' => 'Tags — Index']);
    }

    public function store()
    {
        return view('placeholder', ['title' => 'Tags — Store']);
    }
}
