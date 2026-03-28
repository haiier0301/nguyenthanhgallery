<?php
/**
 * Front Controller - MVC Nguyen Thanh Gallery
 * Point all requests here (except existing files) via .htaccess or PHP built-in server
 */

declare(strict_types=1);

require __DIR__ . '/app/config.php';

spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;

$router = new Router();

// Public pages
$router->get('/', 'HomeController@index');
$router->get('/about', 'AboutController@index');
$router->get('/artists', 'ArtistsController@index');
$router->get('/artists/{slug}', 'ArtistController@show');
$router->get('/artists/{slug}/{year}', 'ArtistController@series');
$router->get('/exhibitions', 'ExhibitionsController@index');
$router->get('/exhibitions/8-hearts', 'ExhibitionController@show');
$router->get('/art-fairs', 'ArtFairsController@index');
$router->get('/contact', 'ContactController@index');

$router->dispatch();
