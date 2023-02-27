<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

use Psr\Http\Message\ResponseInterface;

/**
 * RequestException is an exception thrown when a request
 * to the API returns an undexpected HTTP status code
 */
class RequestException extends \Exception {
    /**
     * @var array
     */
    private $params = [];

    private function __construct(int $code = 0, array $params = []) {
        parent::__construct($params['message'] ?? $params['msg'] ?? sprintf("Unnnown error, code is %d", $code));
        $this->params = $params;
    }

    public static function fromApiResponse(ResponseInterface $res): RequestException {
        $code = $res->getStatusCode();
        $body = (string)$res->getBody();
        $params = @\json_decode($body, true);
        return new self($code, is_array($params) ? $params : []);
    }

    public function error() {
        return $this->params['error'] ?? null;
    }

    public function timestamp() {
        return $this->params['timestamp'] ?? 0;
    }
}
