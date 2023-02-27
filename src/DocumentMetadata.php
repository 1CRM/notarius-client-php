<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class DocumentMetadata extends ApiObject {
    public $originalDocumentId;
    public $documentId;
    public $fileName;
    public $fileSize;
    public $createdBy;
    public $uploadDate;
    public $contentType;
    public $originalFileType;
    public $pageCount;
    public $hasCorruptedSignatures;
    public $pdfaLevel;
    public $certificationLevel;
    public $pdfaPolicy;
    public $onS3;
}
