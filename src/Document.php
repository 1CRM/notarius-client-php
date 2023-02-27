<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class Document extends ApiObject {

    public $documentId;
    public $documentLabel;
    public $name;
    public $data;

    public $fields;
    public $anchors;
    public $annotations;

    protected function listProps(): array {
        return [
            "fields" => '\OneCRM\NotariusClient\FormField',
            "anchors" => '\OneCRM\NotariusClient\FormAnchor',
            "annotations" => '\OneCRM\NotariusClient\Annotation',
        ];
    }

    public static function withContent($content, array $values = []): Document {
        $valid = false;
        if (is_resource($content)) {
            if (get_resource_type($content) == 'file' || get_resource_type($content) == 'stream') {
                $valid = true;
            }
            $fileData = stream_get_contents($content);
        } elseif (is_string($content)) {
            $valid = true;
            $fileData = $content;
        } elseif (is_object($content) && method_exists($content, '__toString')) {
            // Stringable did not exists before PHP 8 
            $valid = true;
            $fileData = (string)$content;
        }
        if (!$valid) {
            $type = gettype($content);
            throw new \InvalidArgumentException("Invalid content (expected stream, string or Stringable, got {$type})");
        }
        $data = array_merge($values, ['data' => base64_encode($fileData)]);
        return new Document($data);
    }

    public function cloneForUpdate() {
        $copy = new Document([]);
        $copy->documentId = $this->documentId;
        foreach ($this->fields as $f) {
            $copy->fields[] = clone $f;
        }
        return $copy;
    }
}
