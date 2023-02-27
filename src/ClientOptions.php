<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class ClientOptions {
    const SANDBOX_URI = 'SANDBOX_URI';

    private $options = [];

    public function build(): array {
        return $this->options;
    }

    public function withSandbox(string $baseUri) {
        $this->options[self::SANDBOX_URI] = $baseUri;
        return $this;
    }
}
