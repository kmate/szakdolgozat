<?php

namespace fw\tests;

use \fw\KeyValueStorage;
use \PHPUnit_Framework_TestCase;

class KeyValueStorageTest extends PHPUnit_Framework_TestCase
{
    private $_storage;
    
    public function setUp()
    {
        $this->_storage = new KeyValueStorage();
    }
    
    public function tearDown()
    {
        unset($this->_storage);
    }
    
    public function testDoesNotHaveNonexistentKey()
    {
        $this->assertFalse(isset($this->_storage->NonexistentKey));
    }
    
    public function testGetReturnsNullForNonexistentKey()
    {
        $this->assertNull($this->_storage->get('NonexistentKey'));
    }
    
    public function testGetWithDefaultValue()
    {
        $this->assertEquals('default_value', $this->_storage->get('NonexistentKey', 'default_value'));
    }
    
    /**
     * @dataProvider keyValueProvider
     */
    public function testGetWithMagicMethod($key, $value)
    {
        $this->_storage->set($key, $value);
        
        $this->assertEquals($value, $this->_storage->{$key});
    }
    
    /**
     * @dataProvider keyValueProvider
     */
    public function testSetWithMagicMethod($key, $value)
    {
        $this->_storage->{$key} = $value;
        
        $this->assertEquals($value, $this->_storage->get($key));
    }
    
    public function testSetTransformsArray()
    {
        $this->_storage->set('key1', array('key2' => 'value2'));
        
        $this->assertEquals('value2', $this->_storage->key1->key2);
    }
    
    public function testSetReturnsStorage()
    {
        $this->assertEquals($this->_storage, $this->_storage->set('AnyKey', 'AnyValue'));
    }
    
    public function keyValueProvider()
    {
        return array(
            array('boolean', true),
            array('number' , 42),
            array('string' , 'test'),
            array('object' , new \stdClass()),
            array('storage', new KeyValueStorage(array(1, 2, 3)))
        );
    }
    
    /**
     * @dataProvider arrayProvider
     */
    public function testHasWorksOnArrayWrapper(array $array)
    {
        $arrayWrapperStorage = new KeyValueStorage($array);
        
        $this->assertTrue($arrayWrapperStorage->has('key1'));
        $this->assertTrue(isset($arrayWrapperStorage->key2));
    }
    
    /**
     * @dataProvider arrayProvider
     */
    public function testGetWorksOnArrayWrapper(array $array)
    {
        $arrayWrapperStorage = new KeyValueStorage($array);
        
        $this->assertEquals('value1', $arrayWrapperStorage->get('key1'));
        $this->assertEquals('value2', $arrayWrapperStorage->key2);
    }
    
    /**
     * @dataProvider arrayProvider
     */
    public function testSetWorksOnArrayWrapper(array $array)
    {
        $arrayWrapperStorage = new KeyValueStorage($array);
        
        $arrayWrapperStorage->set('key3', 'value3');
        $arrayWrapperStorage->key4 = 'value4';
        
        $this->assertEquals('value3', $arrayWrapperStorage->get('key3'));
        $this->assertEquals('value4', $arrayWrapperStorage->key4);
    }
    
    public function arrayProvider()
    {
        return array(
            array(array(
                'key1' => 'value1',
                'key2' => 'value2'
            ))
        );
    }
    
    /**
     * @dataProvider multiLevelArrayProvider
     */
    public function testCanBeConvertedToArray(array $array)
    {
        $arrayWrapperStorage = new KeyValueStorage($array);
        
        $this->assertEquals($array, $arrayWrapperStorage->toArray());
    }
    
    public function multiLevelArrayProvider()
    {
        return array(
            array(
                array(
                    'key1' => array(
                        'key2' => array(
                            'value2'
                        )
                    )
                )
            ),
            array(
                array(
                    'key1' => array(
                        'key2' => array(
                            'key3' => array(
                                'value3'
                            )
                        )
                    )
                )
            )
        );
    }
}
