<?php

namespace Formulair\Controller;

use Formulair\Core\Logger;
use Formulair\Core\Validator;

abstract class BaseController
{
    protected Logger $logger;

    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger ?? new Logger();
    }

    protected function view(string $viewName, array $data = []): string
    {
        $viewPath = dirname(__DIR__, 2) . '/views/' . $viewName . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: $viewName");
        }

        ob_start();
        extract($data);
        include $viewPath;
        return ob_get_clean();
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function getInput(string $key = null, mixed $default = null): mixed
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST' || $method === 'PUT') {
            $data = $_POST;
            if (empty($data) && in_array($method, ['PUT', 'DELETE'])) {
                parse_str(file_get_contents('php://input'), $data);
            }
        } else {
            $data = $_GET;
        }

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    protected function validate(array $data, array $rules): Validator
    {
        $validator = new Validator($data);
        $validator->validate($rules);
        return $validator;
    }

    protected function error(string $message, int $statusCode = 400): void
    {
        $this->logger->error($message);
        $this->json(['error' => $message], $statusCode);
    }

    protected function getClientIp(): string
    {
        $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;

        if ($forwardedFor) {
            return trim(explode(',', $forwardedFor)[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
