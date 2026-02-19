<?php

declare(strict_types=1);

namespace App\Shipping\Dto;

class ErrorResponse
{
    public function __construct(
        private readonly string $error,
        private readonly string $message
    ) {
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'error' => $this->error,
            'message' => $this->message,
        ];
    }
}
