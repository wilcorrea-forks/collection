<?php

namespace PhpBrasil\Collection\Resources;

use PhpBrasil\Collection\Pack;
use RuntimeException;
use function array_values;
use function PhpBrasil\Collection\Helper\prop;

/**
 * Trait TraitTransform
 * @package PhpBrasil\Collection\Resources
 */
trait TraitTransform
{
    /**
     * @param callable $callback
     * @return Pack
     */
    public function map(callable $callback)
    {
        $array = array_map($callback, $this->records, array_keys($this->records));
        return $this->build($array);
    }

    /**
     * @param callable $callback
     * @return Pack
     */
    public function filter(callable $callback = null)
    {
        if (!is_null($callback)) {
            $array = array_filter($this->records, $callback, ARRAY_FILTER_USE_BOTH);
        }
        if (!isset($array)) {
            $array = array_filter($this->records);
        }
        return $this->build(array_values($array));
    }

    /**
     * @param string $property
     * @return Pack
     */
    public function pluck($property)
    {
        $array = array_map(function ($item) use ($property) {
            return prop($item, $property);
        }, $this->records);
        return $this->build($array);
    }

    /**
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        $accumulator = $initial;
        $records = $this->records;
        $array = $this->records;
        array_walk($records, function($value, $key) use(&$accumulator, $callback, $array) {
            $accumulator = $callback($accumulator, $value, $key, $array);
        });
        return $accumulator;
    }

    /**
     * @param array $array
     * @return Pack|mixed
     */
    public function build(array $array)
    {
        if (!method_exists($this, 'create')) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            return new static($array);
        }
        return $this->create($array);
    }
}
