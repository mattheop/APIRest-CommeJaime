<?php

namespace App\Application\Middleware;

use Slim\Exception\HttpSpecializedException;
use Slim\Interfaces\ErrorRendererInterface;

class ErrorRenderer implements ErrorRendererInterface
{

    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        $payload = ['success' => false, 'error' => [
            'class' => (new \ReflectionClass($exception))->getShortName(),
            'status' => $exception->getCode(),
            'title' => 'Internal Server Error',
            'description' => $exception->getMessage()
        ]];

        if($exception instanceof HttpSpecializedException){
            $payload['error']['status'] = $exception->getCode();
            $payload['error']['title'] = $exception->getTitle();
            $payload['error']['description'] = $exception->getDescription();
            $payload['error']['message'] = $exception->getMessage();
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

}