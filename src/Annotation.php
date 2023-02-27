<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class Annotation extends ApiObject {
    public $id;
    public $x;
    public $y;
    public $width;
    public $height;
    public $page;
    public $assignedTo;
    public $number;
    public $content;
    public $status;
    public $groupId;
    public $userFullName;
}
