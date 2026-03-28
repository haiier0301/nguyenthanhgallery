<?php
namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', [
            'pageTitle' => '404 - Page Not Found',
            'bodyClass' => 'page-light',
            'currentPage' => '',
        ]);
    }

    public function serverError(): void
    {
        http_response_code(500);
        $this->view('errors/500', [
            'pageTitle' => '500 - Internal Server Error',
            'bodyClass' => 'page-light',
            'currentPage' => '',
        ]);
    }
}

