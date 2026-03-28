<?php
namespace App\Controllers;
use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home', [
            'pageTitle' => 'Nguyen Thanh Gallery',
            'bodyClass' => '',
            'currentPage' => '',
        ]);
    }
}
