<?php
namespace ANClient;

use ANClient\Http;
use ANClient\Resource\AbstractResource;
use ArrayAccess;

/**
 * Represents a single resource entity e,g, a publisher
 *
 * @package ANClient
 */
class Entity implements ArrayAccess, \JsonSerializable
{
    /**
     * @var AbstractResourceCollection
     */
    protected $resource;

    /**
     * @var array
     */
    protected $properties = array();

    /**
     * @param AbstractResource $resource
     * @param array $properties
     */
    public function __construct(AbstractResource $resource, array $properties = [])
    {
        $this->resource = $resource;
        $this->properties = $properties;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->properties[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return empty($this->properties[$offset]) ? null : $this->properties[$offset];
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->properties[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }

    /**
     * Get a property of the entity
     *
     * @param $name
     * @return mixed
     */
    public function getProperty($name)
    {
        return empty($this->properties[$name]) ? null : $this->properties[$name];
    }

    /**
     * Replace all properties with given array
     *
     * @param array $properties
     */
    public function hydrate(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Export properties as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->properties;
    }

    /**
     * @param AbstractResource $childResource
     * @param array $conditions
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchChildren(AbstractResource $childResource, array $conditions = [], $limit = 99, $offset = 0)
    {
        return $childResource->fetch(
            array_merge($conditions, [$this->resource->getIdName() => $this['id']]),
            $limit,
            $offset
        );
    }

    /**
     * @param AbstractResource $childResource
     * @param array $conditions
     * @return array
     */
    public function fetchAllChildren(AbstractResource $childResource, array $conditions = [])
    {
        return $childResource->fetchAll(array_merge($conditions, [$this->resource->getIdName() => $this['id']]));
    }
}
