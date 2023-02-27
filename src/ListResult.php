<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class ListResult extends ApiObject {

    public $count;
    public $total;
    public $offset;

    public $list;

    private $_className;
    private $_fieldName;

    public function __construct(string $class, string $fieldName, array $data = []) {
        $this->_className = $class;
        $this->_fieldName = $fieldName;
        parent::__construct($data);
    }

    protected function listProps(): array {
        return [
            'list' => $this->_className,
        ];
    }

    protected function mapField(string $fieldName, bool $encode) {
        if ($encode && $fieldName === 'list') return $this->_fieldName;
        elseif (!$encode && $fieldName === $this->_fieldName) return 'list';
        return parent::mapField($fieldName, $encode);
    }
}
