<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class Workflow extends WorkflowOrTemplate {

    public const NOT_STARTED = 0;
    public const IN_PROCESS = 1;
    public const COMPLETED = 2;
    public const EXPIRED = 3;
    public const FROM_TEMPLATE = 4;
    public const DECLINED = 5;

    public $expiresOn;
    public $pdfaPolicy;
    public $editUrl;
    public $totalActions;
    public $remainingActions;
}
