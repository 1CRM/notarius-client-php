<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class ClientOptions {
    const SANDBOX_URI = 'SANDBOX_URI';
    const NOTARIUS_AUTH_KEY = 'NOTARIUS_AUTH_KEY';
    const NOTARIUS_AUTH_SECRET = 'NOTARIUS_AUTH_SECRET';
    const NOTARIUS_PLATFORM_LOGIN = 'NOTARIUS_PLATFORM_LOGIN';
    const NOTARIUS_AUTH_USER = 'NOTARIUS_AUTH_USER';
    const NOTARIUS_AUTH_PASSWORD = 'NOTARIUS_AUTH_PASSWORD';

    private $options = [];

    public function build(): array {
        return $this->options;
    }

    public function withSandbox(string $baseUri) {
        $this->options[self::SANDBOX_URI] = $baseUri;
        return $this;
    }

    public function withAuthKey(string $key) {
        $this->options[self::NOTARIUS_AUTH_KEY] = $key;
        return $this;
    }

    public function withSecret(string $secret) {
        $this->options[self::NOTARIUS_AUTH_SECRET] = $secret;
        return $this;
    }

    public function withUser(string $user) {
        $this->options[self::NOTARIUS_AUTH_USER] = $user;
        return $this;
    }

    public function withPassword(string $password) {
        $this->options[self::NOTARIUS_AUTH_PASSWORD] = $password;
        return $this;
    }
}
