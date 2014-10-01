<?php
namespace ANClient\Resource;

use ANClient\Client;
use ANClient\Entity;

/**
 * @package ANClient\Resource
 */
abstract class AbstractResource
{
    /**
     * @var \ANClient\Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
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
     * The fully qualified class name of the entity.
     *
     * @return string
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
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \ANClient\Entity $entity
     * @param array $params
     * @return \ANClient\Entity
     */
    public function persist(Entity $entity, $params = [])
    {
        $result = $this->client->dispatch(
            'POST',
            $this->getPath(),
            ['query' => $params, 'json' => array($this->getSingularName() => $entity)]
        );

        //hydrate resource with id etc.
        $entity->hydrate($result);

        return $entity;
    }

    /**
     * @param array $properties
     * @return \ANClient\Entity
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
    public function fetch(array $conditions = [], $limit = 99, $offset = 0)
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
        while ($more = $this->fetch($conditions, 99, $offset)) {
            $collection = array_merge($collection, $more);
            $offset += count($more);
        }

        return $collection;
    }

    /**
     * @param $id
     * @return \ANClient\Entity
     * @throws \RuntimeException
     */
    public function fetchId($id)
    {
        $result = $this->client->dispatch('GET', $this->getPath(), ['query' => ['id' => $id]]);

        if (!isset($result[$this->getSingularName()])) {
            throw new \RuntimeException($this->getSingularName().' was not found in result');
        }

        return $this->newEntity($result[$this->getSingularName()]);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteId($id)
    {
        $this->client->dispatch('DELETE', $this->getPath(), ['query' => ['id' => $id]]);
        return true;
    }
}
