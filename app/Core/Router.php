<?php
/**
 * Simple Router - maps URI to Controller::action
 */

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = defined('BASE_URL') ? BASE_URL : '';
    }

    public function get(string $uri, string $controllerAction): void
    {
        $this->routes['GET'][$this->normalizeUri($uri)] = $controllerAction;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($requestUri, PHP_URL_PATH);

        if ($this->baseUrl && strpos($path, $this->baseUrl) === 0) {
            $path = substr($path, strlen($this->baseUrl)) ?: '/';
        }
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $routes = $this->routes[$method] ?? [];
        foreach ($routes as $pattern => $handler) {
            $params = $this->match($pattern, $path);
            if ($params !== null) {
                $this->call($handler, $params);
                return;
            }
        }

        $this->renderError(404);
    }

    protected function normalizeUri(string $uri): string
    {
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }
        return $uri;
    }

    protected function match(string $pattern, string $path): ?array
    {
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace(['\{slug\}', '\{year\}', '\{id\}'], '([^/]+)', $pattern);
        if (preg_match('#^' . $pattern . '$#', $path, $m)) {
            array_shift($m);
            return $m;
        }
        return null;
    }

    protected function call(string $handler, array $params): void
    {
        [$class, $method] = explode('@', $handler);
        if (strpos($class, '\\') === false) {
            $class = 'App\\Controllers\\' . $class;
        }
        if (!class_exists($class)) {
            $this->renderError(500);
            return;
        }
        $controller = new $class();
        if (!method_exists($controller, $method)) {
            $this->renderError(500);
            return;
        }
        $controller->$method(...$params);
    }

    protected function renderError(int $status): void
    {
        $errorController = 'App\\Controllers\\ErrorController';
        if (class_exists($errorController)) {
            $controller = new $errorController();
            if ($status === 404 && method_exists($controller, 'notFound')) {
                $controller->notFound();
                return;
            }
            if ($status === 500 && method_exists($controller, 'serverError')) {
                $controller->serverError();
                return;
            }
        }

        http_response_code($status);
        echo $status === 404 ? '404 Not Found' : '500 Internal Server Error';
    }
}
