<?php

namespace AurelLungu\Framework\Http;

class Response
{
    const METHOD_NOT_ALLOWED = 405;
    const ROUTE_NOT_FOUND = 404;
    const BAD_REQUEST = 400;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    public function __construct(
        private ?string $content = '',
        private int $status = 200,
        private array $headers = []
    ) {
        // Must be set before sending content

        // So best to create an instantiation like here
        http_response_code($this->status);
    }

    public function send(): void
    {
        echo $this->content;
    }
}