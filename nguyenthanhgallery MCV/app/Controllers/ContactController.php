<?php
namespace App\Controllers;
use App\Core\Controller;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('contact', [
            'pageTitle' => 'Contact - Nguyen Thanh Gallery',
            'bodyClass' => 'page-contact',
            'currentPage' => 'contact',
        ]);
    }
}
