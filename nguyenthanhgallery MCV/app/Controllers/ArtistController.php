<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Artist;
use App\Models\Artwork;

class ArtistController extends Controller
{
    public function show(string $slug): void
    {
        $slug = preg_replace('/\.html$/i', '', $slug);
        $artist = Artist::findById($slug) ?? Artist::findBySlug($slug);
        if (!$artist) {
            http_response_code(404);
            echo '404 Artist not found';
            return;
        }
        $seriesYears = Artist::getSeriesYears($artist['id']);
        $artworks = Artwork::byArtist($artist['id']);
        $this->view('artist', [
            'pageTitle' => $artist['nameDisplay'] . ' - Nguyen Thanh Gallery',
            'bodyClass' => 'page-artist-detail',
            'currentPage' => 'artists',
            'artist' => $artist,
            'seriesYears' => $seriesYears,
            'artworks' => $artworks,
        ]);
    }

    public function series(string $slug, string $year): void
    {
        $slug = preg_replace('/\.html$/i', '', $slug);
        $year = preg_replace('/\.html$/i', '', $year);
        $artist = Artist::findById($slug) ?? Artist::findBySlug($slug);
        if (!$artist) {
            http_response_code(404);
            echo '404 Artist not found';
            return;
        }
        $artworks = Artwork::byArtistAndYear($artist['id'], $year);
        $seriesYears = Artist::getSeriesYears($artist['id']);
        $this->view('artist-series', [
            'pageTitle' => $year . ' - ' . $artist['nameDisplay'] . ' - Nguyen Thanh Gallery',
            'bodyClass' => 'page-artist-detail',
            'currentPage' => 'artists',
            'artist' => $artist,
            'year' => $year,
            'artworks' => $artworks,
            'seriesYears' => $seriesYears,
        ]);
    }
}
