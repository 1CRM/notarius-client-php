<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;


class ObjectsList implements \ArrayAccess, ArrayConvertable, \IteratorAggregate, \Countable {
    private $list = null;
    private $memberClass;

    public function __construct(string $memberClass) {
        $this->memberClass = $memberClass;
    }

    public function unset() {
        $this->list = null;
    }

    public function clear() {
        $this->list = [];
    }

    public function offsetExists($offset): bool {
        return  is_array($this->list) && array_key_exists($offset, $this->list);
    }

    public function offsetGet($offset) {
        return $this->list[$offset];
    }

    public function offsetUnset($offset): void {
        if (is_array($this->list))
            unset($this->list[$offset]);
    }
    public function offsetSet($offset,  $value): void {
        if ($this->memberClass && !($value instanceof $this->memberClass)) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new \InvalidArgumentException("Invalid member type (expected {$this->memberClass}, got {$type})");
        }
        if (!is_array($this->list)) {
            $this->list = [];
        }
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    public function count(): int {
        return is_array($this->list) ? count($this->list) : 0;
    }

    public function toArray() {
        if (!is_array($this->list)) return null;
        return array_map(
            function ($value) {
                return $value instanceof ArrayConvertable
                    ? $value->toArray()
                    : $value;
            },
            array_values($this->list),
        );
    }

    public function getIterator(): \Traversable {
        if (is_array($this->list)) return new \ArrayIterator($this->list);
        return new \ArrayIterator([]);
    }
}
