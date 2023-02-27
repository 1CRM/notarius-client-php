<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

abstract class ApiObject implements ArrayConvertable {

    private $_listProps = [];
    private $_objectProps = [];
    protected $_data = [];

    public function __construct(array $data) {
        $this->_listProps = $this->listProps();
        $this->_objectProps = $this->objectProps();
        $reflect = new \ReflectionObject($this);
        foreach ($data as $k => $v) {
            $newKey = $this->mapField($k, false);
            try {
                $prop = $reflect->getProperty($newKey);
                $exists = $prop->isPublic();
            } catch (\ReflectionException $e) {
                $exists = false;
            }
            if (isset($this->_listProps[$newKey]) && is_array($v) && $exists) {
                $cls = $this->_listProps[$newKey];
                $val = new ObjectsList($cls);
                foreach ($v as $member) {
                    $val[] = new $cls($member);
                }
                $this->$newKey = $val;
            } elseif (isset($this->_objectProps[$newKey]) && is_array($v) && $exists) {
                $cls = $this->_objectProps[$newKey];
                $this->$newKey = new $cls($v);
            } elseif ($exists) {
                $this->$newKey = $this->decodeField($k, $v);
            } else {
                $this->$k = $this->decodeField($k, $v);
            }
        }
        foreach ($this->_listProps as $k => $cls) {
            $newKey = $this->mapField($k, false);
            try {
                $prop = $reflect->getProperty($newKey);
                $exists = $prop->isPublic();
            } catch (\ReflectionException $e) {
                $exists = false;
            }
            if ($exists && !($this->$newKey instanceof ObjectsList)) {
                $this->$newKey = new ObjectsList($cls);
            }
        }
    }

    public function __isset($name) {
        return array_key_exists($name, $this->_data);
    }

    public function __get($name) {
        return $this->_data[$name];
    }

    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }

    public function toArray() {
        $data = $this->_data;
        $reflect = new \ReflectionObject($this);
        foreach ($this->_listProps as $k => $_unused) {
            try {
                $prop = $reflect->getProperty($k);
                $exists = $prop->isPublic();
            } catch (\ReflectionException $e) {
                $exists = false;
            }
            if ($exists && $this->$k instanceof ObjectsList) {
                $val = $this->$k->toArray();
                if (!is_null($val)) {
                    $newKey = $this->mapField($k, true);
                    $data[$newKey] = $val;
                }
            }
        }
        foreach ($this->_objectProps as $k => $_unused) {
            try {
                $prop = $reflect->getProperty($k);
                $exists = $prop->isPublic();
            } catch (\ReflectionException $e) {
                $exists = false;
            }
            if ($exists && $this->$k instanceof ArrayConvertable) {
                $val = $this->$k->toArray();
                if (!is_null($val)) {
                    $newKey = $this->mapField($k, true);
                    $data[$newKey] = $val;
                }
            }
        }
        foreach ($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $k = $prop->getName();
            if (isset($this->_objectProps[$k]) || isset($this->_listProps[$k]))
                continue;
            $val = $this->encodeField($k, $this->$k);
            if (!is_null($val)) {
                $newKey = $this->mapField($k, false);
                $data[$newKey] = $val;
            }
        }
        return array_filter($data, function ($value) {
            return !is_null($value);
        });
    }

    public function __clone() {
        return new static($this->toArray());
    }

    protected function decodeField($fieldName, $value) {
        return $value;
    }

    protected function encodeField($fieldName, $value) {
        return $value;
    }

    protected function mapField(string $fieldName, bool $encode) {
        return $fieldName;
    }

    protected function listProps(): array {
        return [];
    }

    protected function objectProps(): array {
        return [];
    }
}
