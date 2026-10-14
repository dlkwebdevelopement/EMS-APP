<?php
// app/Http/Controllers/LandingPageController.php

namespace App\Http\Controllers;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function features()
    {
        return view('features');
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    public function terms()
    {
        return view('terms-and-conditions');
    }

    public function deleteAccount()
    {
        return view('delete-account');
    }
}