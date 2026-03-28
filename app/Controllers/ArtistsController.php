<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Artist;

class ArtistsController extends Controller
{
    public function index(): void
    {
        $artists = Artist::all();
        $this->view('artists', [
            'pageTitle' => 'Artists - Nguyen Thanh Gallery',
            'bodyClass' => 'page-artists',
            'currentPage' => 'artists',
            'artists' => $artists,
        ]);
    }
}
