<?php
namespace App\Controllers;
use App\Core\Controller;

class ExhibitionController extends Controller
{
    public function show(): void
    {
        $this->view('exhibition-8-hearts', [
            'pageTitle' => '8 HEARTS – TRUC-ANH | Nguyen Thanh Gallery',
            'bodyClass' => 'page-artist-detail page-exhibition-detail',
            'currentPage' => 'exhibitions',
        ]);
    }
}
