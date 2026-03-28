<?php
namespace App\Controllers;
use App\Core\Controller;

class AboutController extends Controller
{
    public function index(): void
    {
        $this->view('about', [
            'pageTitle' => 'About Us - Nguyen Thanh Gallery',
            'bodyClass' => 'page-about',
            'currentPage' => 'about',
        ]);
    }
}
