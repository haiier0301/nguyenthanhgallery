<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\ArtFair;
use App\Models\Media;

class ArtFairsController extends Controller
{
    public function index(): void
    {
        $artFairs = ArtFair::sortedByYearDesc();
        $mediaImages = Media::byFolder('art-fair');
        $mediaImages = array_slice($mediaImages, 0, 12);

        $this->view('art-fairs', [
            'pageTitle' => 'Art Fairs - Nguyen Thanh Gallery',
            'bodyClass' => 'page-art-fairs',
            'currentPage' => 'art-fairs',
            'artFairs' => $artFairs,
            'mediaImages' => $mediaImages,
        ]);
    }
}
