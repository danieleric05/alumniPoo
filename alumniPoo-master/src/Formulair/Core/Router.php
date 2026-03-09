<?php

namespace Formulair\Core;

class Router
{
    private array $routes = [];
    private Logger $logger;

    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger ?? new Logger();
    }

    public function get(string $path, callable $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, callable $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function put(string $path, callable $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    public function delete(string $path, callable $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    private function addRoute(string $method, string $path, callable $callback): void
    {
        $pattern = $this->pathToRegex($path);
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'path' => $path,
            'callback' => $callback,
        ];
    }

    private function pathToRegex(string $path): string
    {
        return preg_replace_callback(
            '/{(\w+)}/',
            fn($matches) => '(?P<' . $matches[1] . '>[^/]+)',
            preg_quote($path, '#')
        );
    }

    public function dispatch(string $method, string $path, array $params = []): mixed
    {
        $path = parse_url($path, PHP_URL_PATH);
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match('#^' . $route['pattern'] . '$#', $path, $matches)) {
                $params = array_merge($params, array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

                $this->logger->debug("Route matched: {$route['path']}", [
                    'method' => $method,
                    'path' => $path,
                ]);

                return call_user_func($route['callback'], $params);
            }
        }

        $this->logger->warning("No route found: $method $path");
        return $this->notFound();
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
