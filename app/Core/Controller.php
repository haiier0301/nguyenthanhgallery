<?php
/**
 * Base Controller - common helpers for views
 */

namespace App\Core;

abstract class Controller
{
    protected string $baseUrl = '';
    protected string $viewsPath = '';

    public function __construct()
    {
        $this->baseUrl = defined('BASE_URL') ? BASE_URL : '';
        $this->viewsPath = defined('VIEWS_PATH') ? VIEWS_PATH : (dirname(__DIR__) . '/Views/');
    }

    protected function view(string $name, array $data = []): void
    {
        extract($data);
        $content = '';
        ob_start();
        $viewFile = $this->viewsPath . str_replace('.', '/', $name) . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        }
        $content = ob_get_clean();

        $layoutFile = $this->viewsPath . 'layout.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function render(string $viewName, array $data = []): string
    {
        extract($data);
        ob_start();
        $viewFile = $this->viewsPath . str_replace('.', '/', $viewName) . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        }
        return ob_get_clean();
    }
}
