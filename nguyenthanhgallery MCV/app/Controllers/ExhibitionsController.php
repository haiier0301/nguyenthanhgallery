<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Exhibition;

class ExhibitionsController extends Controller
{
    public function index(): void
    {
        $exhibitions = Exhibition::all();
        $awards = array_filter($exhibitions, fn($e) => ($e['type'] ?? '') === 'award');
        $history = array_filter($exhibitions, fn($e) => ($e['type'] ?? '') === 'group' || ($e['type'] ?? '') === 'solo');
        $this->view('exhibitions', [
            'pageTitle' => 'Exhibitions - Nguyen Thanh Gallery',
            'bodyClass' => 'page-exhibitions',
            'currentPage' => 'exhibitions',
            'exhibitions' => $exhibitions,
            'awards' => $awards,
            'history' => $history,
        ]);
    }
}
