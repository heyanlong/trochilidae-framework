<?php

namespace Trochilidae\Container;

use Psr\Container\ContainerInterface;
use ArrayAccess;

class Container implements ArrayAccess, ContainerInterface
{

    protected static $instance;

    private $values = array();
    private $factories;
    private $protected;
    private $frozen = array();
    private $raw = array();
    private $keys = array();

    public static function setInstance($container)
    {
        static::$instance = $container;
    }

    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        return static::$instance[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        // TODO: Implement has() method.
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->keys[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if (!isset($this->keys[$offset])) {
            // TODO
        }

        if (
            isset($this->raw[$offset])
            || !is_object($this->values[$offset])
            || isset($this->protected[$this->values[$offset]])
            || !method_exists($this->values[$offset], '__invoke')) {
            return $this->values[$offset];
        }

        if (isset($this->factories[$this->values[$offset]])) {
            return $this->values[$offset]($this);
        }

        $raw = $this->values[$offset];
        $val = $this->values[$offset] = $raw($this);
        $this->raw[$offset] = $raw;
        $this->frozen[$offset] = true;
        return $val;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (isset($this->frozen[$offset])) {
            // TODO
        }

        $this->values[$offset] = $value;
        $this->keys[$offset] = true;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($this->keys[$offset])) {
            if (is_object($this->values[$offset])) {
                unset($this->factories[$this->values[$offset]], $this->protected[$this->values[$offset]]);
            }
            unset($this->values[$offset], $this->frozen[$offset], $this->raw[$offset], $this->keys[$offset]);
        }
    }
}