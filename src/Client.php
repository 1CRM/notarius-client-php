<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Client {
    /**
     * The API base URI
     */
    public const BASE_URI = 'https://cloud.consigno.com/api/v1/';

    public const AUTH_ENDPOINT = 'auth/login';
    public const DOCUMENTS_ENDPOINT = 'documents';
    public const DELETED_WORKFLOWS_ENDPOINT = 'workflows/deleted';
    public const WORKFLOWS_ENDPOINT = 'workflows';
    public const WORKFLOW_ENDPOINT = 'workflows/%s';
    public const DOCUMENTS_TEMPLATE_ENDPOINT = 'documents';
    public const TEMPLATES_ENDPOINT = 'templates';
    public const TEMPLATE_ENDPOINT = 'templates/%s';
    public const BULK_ENDPOINT = 'bulks';
    public const AUDIT_ENDPOINT = 'workflows/%s/audit';
    public const SIGNED_DOCUMENTS_ENDPOINT = 'workflows/%s/documents';
    public const CERTIFIED_DOCUMENTS_ENDPOINT = 'workflows/%s/certifiedcopy';
    public const SINGLE_DOCUMENT_ENDPOINT = 'workflows/%s/documents/%s';

    /** 
     * @var string
     */
    private $token = "";

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var array
     */
    private $options;

    private const LIST_PARAMS = [
        'scope',
        'offset',
        'nbItems',
        'filterName',
        'filterStatus',
        'sortField',
        'sortDir',
    ];

    public function __construct(array $options = []) {
        $this->options = $options;
        $this->client = new HttpClient(['base_uri' => $this->baseUri()]);
    }

    /**
     * @throws GuzzleException
     * @throws RequestException
     */
    public function authenticate() {
        $key = $this->getAuthOption(ClientOptions::NOTARIUS_AUTH_KEY);
        $secret = $this->getAuthOption(ClientOptions::NOTARIUS_AUTH_SECRET);
        $force = $this->getAuthOption(ClientOptions::NOTARIUS_PLATFORM_LOGIN, true);
        $username = $this->getAuthOption(ClientOptions::NOTARIUS_AUTH_USER);
        $password = $this->getAuthOption(ClientOptions::NOTARIUS_AUTH_PASSWORD);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
        ];
        if (!$force && strlen($username) > 0) {
            $options['body'] = \json_encode([
                'username' => $username,
                'password' => $password
            ]);
            $options['headers']['X-Client-Id'] = $key;
            $options['headers']['X-Client-Secret'] = $secret;
        } else {
            $options['body'] = \json_encode([
                'username' => $key,
                'password' => $secret
            ]);
        }
        $res = $this->expectStatusCode(
            $this->client->post(self::AUTH_ENDPOINT, $options),
            200
        );
        $this->token = $res->getHeaderLine('X-Auth-Token');
        return $this->token;
    }

    public function uploadDocument(string $filename, $contents, string $label = null): DocumentMetadata {
        $opts = $this->requestOptions();
        $uploadData = ['pdfaPolicy' => 1];
        if (!is_null($label)) {
            $uploadData['documentLabel'] = $label;
        }
        $opts['multipart'] = [
            ['name' => 'uploadData', 'contents' => \json_encode($uploadData)],
            ['name' => 'file', 'filename' => $filename, 'contents' => $contents],
        ];
        $res = $this->expectStatusCode(
            $this->client->post(self::DOCUMENTS_ENDPOINT, $opts),
            201
        );
        $meta = \json_decode((string)$res->getBody(), true);
        return new DocumentMetadata($meta['response']['metadata']);
    }

    public function uploadTemplateDocument(string $filename, $contents): DocumentMetadata {
        $opts = $this->requestOptions();
        $opts['multipart'] = [
            ['name' => 'uploadData', 'contents' => '{"pdfaPolicy":1}'],
            ['name' => 'file', 'filename' => $filename, 'contents' => $contents],
        ];
        $res = $this->expectStatusCode(
            $this->client->post(self::DOCUMENTS_TEMPLATE_ENDPOINT, $opts),
            201
        );
        $meta = \json_decode((string)$res->getBody(), true);
        return new DocumentMetadata($meta['response']['metadata']);
    }

    public function createTemplate(Template $template): Template {
        $opts = $this->requestOptions();
        $opts['headers']['Content-Type'] = 'application/json';
        $opts['body'] = \json_encode($template->toArray());
        $res = $this->expectStatusCode(
            $this->client->post(self::TEMPLATES_ENDPOINT, $opts),
            201
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new Template($reply['response']);
    }

    public function getTemplates(array $params = []): ListResult {
        $opts = $this->requestOptions();
        $opts['query'] = array_filter($params, [$this, 'restrictListParam'], ARRAY_FILTER_USE_KEY);
        $res = $this->expectStatusCode(
            $this->client->get(self::TEMPLATES_ENDPOINT, $opts),
            200
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new ListResult('\OneCRM\NotariusClient\Template', 'collection', $reply['response']);
    }

    public function getTemplate(string $id): Template {
        $opts = $this->requestOptions();
        $res = $this->expectStatusCode(
            $this->client->get(sprintf(self::TEMPLATE_ENDPOINT, urlencode($id)), $opts),
            200
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new Template($reply['response']);
    }

    public function updateTemplate(string $id, Template $template): Template {
        $opts = $this->requestOptions();
        $opts['headers']['Content-Type'] = 'application/json';
        $opts['body'] = \json_encode($template->toArray());
        $res = $this->expectStatusCode(
            $this->client->put(sprintf(self::TEMPLATE_ENDPOINT, urlencode($id)), $opts),
            200
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new Template($reply['response']);
    }

    public function createBulk(BulkWorkflow $bulk): BulkWorkflow {
        $opts = $this->requestOptions();
        $opts['headers']['Content-Type'] = 'application/json';
        $opts['body'] = \json_encode($bulk->toArray());
        $res = $this->expectStatusCode(
            $this->client->post(self::BULK_ENDPOINT, $opts),
            201
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new BulkWorkflow($reply['response']);
    }



    public function createWorkflow(Workflow $workflow): Workflow {
        $opts = $this->requestOptions();
        $opts['headers']['Content-Type'] = 'application/json';
        $opts['body'] = \json_encode($workflow->toArray());
        $res = $this->expectStatusCode(
            $this->client->post(self::WORKFLOWS_ENDPOINT, $opts),
            201
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new Workflow($reply['response']);
    }

    public function updateWorkflowStatus(string $id, Workflow $workflow): Workflow {
        $opts = $this->requestOptions();
        $opts['headers']['Content-Type'] = 'application/json';
        $opts['body'] = \json_encode($workflow->toArray());
        $res = $this->expectStatusCode(
            $this->client->put(sprintf(self::WORKFLOW_ENDPOINT, urlencode($id)), $opts),
            200
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new Workflow($reply['response']);
    }

    public function updateWorkflowFields(string $id, Workflow $workflow): Workflow {
        $opts = $this->requestOptions();
        $opts['headers']['Content-Type'] = 'application/json';
        $opts['body'] = \json_encode($workflow->toArray());
        $res = $this->expectStatusCode(
            $this->client->patch(sprintf(self::WORKFLOW_ENDPOINT, urlencode($id)), $opts),
            200
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new Workflow($reply['response']);
    }

    public function getWorkflow(string $id): Workflow {
        $opts = $this->requestOptions();
        $res = $this->expectStatusCode(
            $this->client->get(sprintf(self::WORKFLOW_ENDPOINT, urlencode($id)), $opts),
            200
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new Workflow($reply['response']);
    }

    public function getWorkflows(array $params = [], bool $trash = false): ListResult {
        $opts = $this->requestOptions();
        $opts['query'] = array_filter($params, [$this, 'restrictListParam'], ARRAY_FILTER_USE_KEY);
        $endpoint = $trash ? self::DELETED_WORKFLOWS_ENDPOINT : self::WORKFLOWS_ENDPOINT;
        $res = $this->expectStatusCode(
            $this->client->get($endpoint, $opts),
            200
        );
        $reply = \json_decode((string)$res->getBody(), true);
        return new ListResult('\OneCRM\NotariusClient\workflow', 'workflows', $reply['response']);
    }

    public function downloadAuditTrail(string $workflowId): StreamInterface {
        $options = $this->requestOptions(true);
        $res = $this->expectStatusCode(
            $this->client->post(sprintf(self::AUDIT_ENDPOINT, $workflowId), $options),
            200
        );
        return $res->getBody();
    }

    public function downloadFinalDocuments(string $workflowId): StreamInterface {
        $options = $this->requestOptions(true);
        $res = $this->expectStatusCode(
            $this->client->post(sprintf(self::SIGNED_DOCUMENTS_ENDPOINT, $workflowId), $options),
            200
        );
        return $res->getBody();
    }

    public function downloadCertifiedCopy(string $workflowId): StreamInterface {
        $options = $this->requestOptions(true);
        $res = $this->expectStatusCode(
            $this->client->post(sprintf(self::CERTIFIED_DOCUMENTS_ENDPOINT, $workflowId), $options),
            200
        );
        return $res->getBody();
    }

    public function downloadSingleDocument(string $workflowId, string $documentId): StreamInterface {
        $options = $this->requestOptions(true);
        $res = $this->expectStatusCode(
            $this->client->post(sprintf(self::SINGLE_DOCUMENT_ENDPOINT, $workflowId, $documentId), $options),
            200
        );
        return $res->getBody();
    }

    private function restrictListParam($param): bool {
        return in_array($param, self::LIST_PARAMS);
    }

    private function requestOptions(bool $authInBody = false): array {
        $authData = [
            'X-Auth-Token' => $this->token,
        ];
        $options = [
            'http_errors' => false,
        ];
        if ($authInBody) {
            $options['form_params'] = $authData;
        } else {
            $options['headers'] = $authData;
        }
        return $options;
    }

    private function baseUri(): string {
        return $this->options[ClientOptions::SANDBOX_URI] ?? self::BASE_URI;
    }

    private function getAuthOption(string $name, $asBool = false): string {
        $value = $this->options[$name] ?? getenv($envVar);
        return $asBool ? (bool)$value : (is_string($value) ? $value : "");
    }

    private function expectStatusCode(ResponseInterface $res, $expect = 200): ResponseInterface {
        $newToken = $res->getHeaderLine('X-Auth-Token');
        if ($newToken !== "") {
            $this->token = $newToken;
        }
        $code = $res->getStatusCode();
        $valid =
            is_array($expect)
            ? in_array($code, $expect, true)
            : $code === $expect;
        if (!$valid) {
            throw RequestException::fromApiResponse($res);
        }
        return $res;
    }
}
