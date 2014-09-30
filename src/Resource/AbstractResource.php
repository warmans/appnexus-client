<?php
namespace ANClient\Resource;

use ANClient\Entity;
use ANClient\Http;

/**
 * @package ANClient\Resource
 */
abstract class AbstractResource
{
    /**
     * @var \ANClient\Http
     */
    protected $client;

    /**
     * @param Http $client
     */
    public function __construct(Http $client)
    {
        $this->client = $client;
    }

    /**
     * e.g. publisher_id
     *
     * @return string
     */
    abstract public function getIdName();

    /**
     * E.g. site, publisher
     *
     * @return string
     */
    abstract public function getSingularName();

    /**
     * E.g. sites, publishers
     *
     * @return string
     */
    abstract public function getPluralName();

    /**
     * The class name of the entity
     *
     * @return mixed
     */
    public function getEntityClassName()
    {
        return '\\ANClient\\Entity';
    }

    /**
     * Resource uri path
     *
     * @return string
     */
    public function getPath()
    {
        return '/' . $this->getSingularName();
    }

    /**
     * @return Http
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \ANClient\Entity $resource
     * @param array $params
     * @return AbstractEntity
     */
    public function persist(Entity $resource, $params = [])
    {
        $result = $this->client->dispatch(
            'POST',
            $this->getPath(),
            ['query' => $params, 'json' => array($this->getSingularName() => $resource)]
        );

        //hydrate resource with id etc.
        $resource->hydrate($result);

        return $resource;
    }

    /**
     * @param array $properties
     * @return mixed
     */
    public function newEntity(array $properties = [])
    {
        $entityClass = "{$this->getEntityClassName()}";
        return new $entityClass($this, $properties);
    }

    /**
     * Fetch a set of entities
     *
     * @param array $conditions
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \RuntimeException
     */
    public function fetch(array $conditions = [], $limit=99, $offset=0)
    {
        $result = $this->client->dispatch(
            'GET',
            $this->getPath(),
            ['query' => array_merge($conditions, ['num_elements' => $limit, 'start_element' => $offset])]
        );

        if (!isset($result[$this->getPluralName()])) {
            throw new \RuntimeException($this->getPluralName().' was not found in result');
        }

        //build a result set containing instances of this object
        $collection = array();
        foreach ($result[$this->getPluralName()] as $rawEntity) {
            $collection[] = $this->newEntity($rawEntity);
        }

        return $collection;
    }

    /**
     * Keep fetching until all entities have been retrieved
     *
     * @param array $conditions
     * @return array
     */
    public function fetchAll(array $conditions = [])
    {
        $collection = array();
        $offset = 0;
        while($more = $this->fetch($conditions, 99, $offset)) {
            $collection = array_merge($collection, $more);
            $offset += count($more);
        }

        return $collection;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function fetchId($id)
    {
        $result = $this->client->dispatch('GET', $this->getPath(), ['id' => $id]);

        if (!isset($result[$this->getSingularName()])) {
            throw new \RuntimeException($this->getSingularName().' was not found in result');
        }

        return $result[$this->getSingularName()];
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteId($id)
    {
        $this->client->dispatch('DELETE', $this->getPath(), ['id' => $id]);
        return true;
    }
}
