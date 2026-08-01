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
        // preg_quote() would escape the curly braces of {param} placeholders,
        // so literal segments and placeholders must be quoted/built separately.
        $parts = preg_split('/(\{\w+\})/', $path, -1, PREG_SPLIT_DELIM_CAPTURE);

        $regex = '';
        foreach ($parts as $part) {
            if (preg_match('/^\{(\w+)\}$/', $part, $matches)) {
                $regex .= '(?P<' . $matches[1] . '>[^/]+)';
            } else {
                $regex .= preg_quote($part, '#');
            }
        }

        return $regex;
    }

    public function dispatch(string $method, string $path, array $params = []): mixed
    {
        $path = parse_url($path, PHP_URL_PATH);
        $path = '/' . trim($path, '/');

        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $token = $_POST['csrf_token'] ?? null;

            if (!Csrf::verify($token)) {
                $this->logger->warning("CSRF token invalid or missing: $method $path");
                return $this->csrfError();
            }
        }

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

    private function csrfError(): string
    {
        http_response_code(419);

        $title = 'Session expirée - Alumni CNDA';
        $content = '
            <div class="bg-white rounded-xl shadow-md p-12 text-center max-w-lg mx-auto">
                <p class="text-6xl font-bold text-indigo-600 mb-4">419</p>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Session expirée</h1>
                <p class="text-gray-600 mb-6">Votre session a expiré ou la requête n\'a pas pu être vérifiée. Merci de réessayer.</p>
                <a href="javascript:history.back()" class="inline-block bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Retour
                </a>
            </div>
        ';

        ob_start();
        include dirname(__DIR__, 2) . '/views/layouts/base.php';
        return ob_get_clean();
    }

    private function notFound(): string
    {
        http_response_code(404);

        $title = 'Page introuvable - Alumni CNDA';
        $content = '
            <div class="bg-white rounded-xl shadow-md p-12 text-center max-w-lg mx-auto">
                <p class="text-6xl font-bold text-indigo-600 mb-4">404</p>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Page introuvable</h1>
                <p class="text-gray-600 mb-6">La page que vous recherchez n\'existe pas ou a été déplacée.</p>
                <a href="/" class="inline-block bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Retour à l\'accueil
                </a>
            </div>
        ';

        ob_start();
        include dirname(__DIR__, 2) . '/views/layouts/base.php';
        return ob_get_clean();
    }
}
