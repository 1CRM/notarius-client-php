<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class FormField extends ApiObject {
    public $x;
    public $y;
    public $width;
    public $height;
    public $page;
    public $assignedTo;
    public $visible;
    public $name;
    public $locked;
    public $assignable;
    public $merged;
    public $signed;
    public $declined;
}
