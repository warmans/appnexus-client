<?php
namespace ANClient;

use ANClient\Resource\AbstractResource;
use ArrayAccess;

/**
 * Represents a single resource entity e.g. a publisher
 *
 * @package ANClient
 */
class Entity implements ArrayAccess, \JsonSerializable
{
    /**
     * @var AbstractResource
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
     * @return AbstractResource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Replace all properties with given array
     *
     * @param array $properties
     */
    public function hydrate(array $properties)
    {
        /**
         * Filtering is required because when you request an entity it can return NULL values for fields. However
         * if you changed something and PUT it back to the API the request could fail because a NULL field is validated
         * as an integer. Filtering out NULLs seems to make the most sense.
         */
        $this->properties = array_filter($properties, function($val) { return $val !== null; });
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
     * Duplicate entity excluding the id.
     *
     * @param Resource\AbstractResource $resource
     * @return static
     */
    public function duplicate(AbstractResource $resource = null)
    {
        $properties = $this->toArray();
        unset($properties['id']);
        return new static($resource ?: $this->resource, $properties);
    }

    /**
     * Shortcut to persist a resource
     *
     * @param array $params
     * @return \ANClient\Entity
     */
    public function persist(array $params = [])
    {
        return $this->resource->persist($this, $params);
    }

    /**
     * @param AbstractResource $childResource
     * @param array $conditions
     * @param int $limit use -1 to return everything
     * @param int $offset
     * @return array
     */
    public function fetchChildren(AbstractResource $childResource, array $conditions = [], $limit = -1, $offset = 0)
    {
        $conditions = array_merge([$this->resource->getIdName() => $this['id']], $conditions);

        if($limit === -1) {
            return $childResource->fetchAll($conditions);
        } else {
            return $childResource->fetch($conditions, $limit, $offset);
        }
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
        //setting a key to null removes it from the object. See hydrate() comments.
        if($value === null) {
            unset($this[$offset]);
        } else {
            $this->properties[$offset] = $value;
        }
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->properties;
    }
}
