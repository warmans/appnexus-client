<?php
namespace ANClient;

use ANClient\Resource\PublisherResource;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Entity
     */
    private $object;

    /**
     * @var Resource\AbstractResource
     */
    private $resource;

    public function setUp()
    {
        $this->resource = $this->getMockBuilder('\\ANClient\\Resource\\AbstractResource')->disableOriginalConstructor()->getMock();

        //append some values for abstract methods
        $this->resource->expects($this->any())->method('getIdName')->will($this->returnValue('some_id'));

        $this->object = new Entity($this->resource, ["id" => 1, "foo" => "bar"]);
    }

    public function testGetResourceReturnsConfiguredResource()
    {
        $this->assertSame($this->object->getResource(), $this->resource);
    }

    public function testHydrateReplacesExistingProperties()
    {
        $this->object->hydrate(["id" => 2, "name" => "whatever"]);
        $this->assertEquals(2, $this->object['id']);
        $this->assertEquals("whatever", $this->object['name']);
        $this->assertNull($this->object['foo']);
    }

    public function testHydrateNullValueIsDiscarded()
    {
        $this->object->hydrate(['foo'=>'bar', 'baz'=>null]);
        $this->assertEquals(['foo'=>'bar'], $this->object->toArray());
    }

    public function testSetNullValueUnsetsKey()
    {
        $this->object['foo'] = null;
        $this->assertEquals(['id'=>1], $this->object->toArray());
    }

    public function testToArrayExportsConfigredProperties()
    {
        $this->assertEquals(["id" => 1, "foo" => "bar"], $this->object->toArray());
    }

    public function testDuplicateReturnsSameType()
    {
        $this->assertInstanceOf(get_class($this->object), $this->object->duplicate());
    }

    public function testDuplicateUnsetsId()
    {
        $dupe = $this->object->duplicate();
        $this->assertNull($dupe['id']);
    }

    public function testDuplicateRetainsNonUniqueFields()
    {
        $dupe = $this->object->duplicate();
        $this->assertEquals("bar", $dupe['foo']);
    }

    public function testDuplicateUsesSameResourceIfNotSpecified()
    {
        $dupe = $this->object->duplicate();
        $this->assertSame($this->object->getResource(), $dupe->getResource());
    }

    public function testDuplicateUsesConfiguedResource()
    {
        $newResource = $this->getMockBuilder('\\ANClient\\Resource\\AbstractResource')->disableOriginalConstructor()->getMock();
        $dupe = $this->object->duplicate($newResource);
        $this->assertSame($newResource , $dupe->getResource());
    }

    public function testPersistPassesEntityToResource()
    {
        $this->resource->expects($this->once())->method('persist')->with($this->equalTo($this->object), $this->anything());
        $this->object->persist();
    }

    public function testPersistPassesParamsToResource()
    {
        $this->resource->expects($this->once())->method('persist')->with($this->anything(), $this->equalTo(["publisher_id" => 100]));
        $this->object->persist(["publisher_id" => 100]);
    }

    public function testFetchChildrenWithNoLimitCallsFetchAll()
    {
        $child = $this->getMockBuilder('\\ANClient\\Resource\\AbstractResource')->disableOriginalConstructor()->getMock();
        $child->expects($this->once())->method('fetchAll');
        $this->object->fetchChildren($child, [], -1);
    }

    public function testFetchChildrenWithNoLimitAppendsParentIdToConditions()
    {
        $child = $this->getMockBuilder('\\ANClient\\Resource\\AbstractResource')->disableOriginalConstructor()->getMock();
        $child->expects($this->once())->method('fetchAll')->with($this->equalTo(["some_id" => 1, "a" => "b"]));
        $this->object->fetchChildren($child, ["a" => "b"]);
    }

    public function testFetchChildrenWithLimitCallsFetch()
    {
        $child = $this->getMockBuilder('\\ANClient\\Resource\\AbstractResource')->disableOriginalConstructor()->getMock();
        $child->expects($this->once())->method('fetch');
        $this->object->fetchChildren($child, [], 10);
    }

    public function testFetchChildrenWithLimitAppendsParentIdToConditions()
    {
        $child = $this->getMockBuilder('\\ANClient\\Resource\\AbstractResource')->disableOriginalConstructor()->getMock();
        $child->expects($this->once())->method('fetch')->with($this->equalTo(["some_id" => 1, "a" => "b"]));
        $this->object->fetchChildren($child, ["a" => "b"], 10);
    }

    public function testFetchChildrenWAppiesLimitAndOffset()
    {
        $child = $this->getMockBuilder('\\ANClient\\Resource\\AbstractResource')->disableOriginalConstructor()->getMock();
        $child->expects($this->once())->method('fetch')->with($this->anything(), $this->equalTo(10), $this->equalTo(5));
        $this->object->fetchChildren($child, [], 10, 5);
    }

    public function testArrayAccessImplementation()
    {
        //check setter/getter
        $this->object['a'] = 'b';
        $this->assertEquals('b', $this->object['a']);

        //check unset
        unset($this->object['a']);
        $this->assertEquals(null, $this->object['a']);
    }

    public function testJsonSerilaizableImplemenation()
    {
        $this->assertEquals('{"id":1,"foo":"bar"}', json_encode($this->object));
    }
}
