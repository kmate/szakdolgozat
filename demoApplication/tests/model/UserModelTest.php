<?php

namespace app\tests\model;

use \fw\config\XmlConfiguration;
use \app\model\User;
use \app\model\UserModel;
use \PHPUnit_Extensions_Database_TestCase;

if (!defined('MODEL_TEST_ASSETS_PATH'))
{
    define('MODEL_TEST_ASSETS_PATH', TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'model');
}

if (!defined('MODEL_TEST_CONFIG_PATH'))
{
    define('MODEL_TEST_CONFIG_PATH', MODEL_TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'configuration.xml');
}

if (!defined('MODEL_DATASET_PATH'))
{
    define('MODEL_DATASET_PATH', MODEL_TEST_ASSETS_PATH . DIRECTORY_SEPARATOR . 'data.xml');
}

class UserModelTest extends PHPUnit_Extensions_Database_TestCase
{
    private $_model;
    
    public function getConnection()
    {
        $configuration = new XmlConfiguration(MODEL_TEST_CONFIG_PATH);
        
        $this->_model = new UserModel($configuration);
        $this->_model->connect();
        
        return $this->createDefaultDBConnection(
            $this->_model->getConnection(),
            $configuration->model->datasources->test_data_source->dbname
        );
    }
    
    public function getDataSet()
    {
        return $this->createFlatXmlDataSet(MODEL_DATASET_PATH);
    }
    
    public function testGetUserByIdReturnsNullWhenNotFound()
    {
        $user = $this->_model->getUserById(1000);
        
        $this->assertNull($user);
    }
    
    public function testGetUserByIdReturnsCorrectUser()
    {
        $expectedUser = User::create(
            1,
            'test1',
            'Teszt Felhasználó',
            'test@localhost.hu',
            'f84aaea2797117edbcdfd8cd782a719c09c05e86e16fc5e9e5'
        );
        
        $user = $this->_model->getUserById($expectedUser->id);
        
        $this->assertEquals($expectedUser, $user);
    }
    
    public function testAddUser()
    {
        $newUser = User::create(
            null,
            'test4',
            'Negyedik Teszt Felhasználó',
            'test4@localhost.hu',
            $this->_model->hashPassword('password4')
        );
        
        $this->_model->addUser($newUser);
        
        $user = $this->_model->getUserById($newUser->id);
        
        $this->assertEquals($newUser, $user);
    }
    
    /**
     * @expectedException     \app\model\DatabaseModelException
     * @expectedExceptionCode 2 (DatabaseModelException::UNIQUE_CONSTRAINT_FAIL)
     */
    public function testRegisterUserWithSameName()
    {
        $newUser = User::create(
            null,
            'test1',
            'Ötödik Teszt Felhasználó',
            'test5@localhost.hu',
            $this->_model->hashPassword('password5')
        );
        
        $this->_model->addUser($newUser);
        
        $user = $this->_model->getUserById($newUser->id);
        
        $this->assertEquals($newUser, $user);
    }
    
    /**
     * @expectedException     \app\model\DatabaseModelException
     * @expectedExceptionCode 2 (DatabaseModelException::UNIQUE_CONSTRAINT_FAIL)
     */
    public function testRegisterUserWithSameEmail()
    {
        $newUser = User::create(
            null,
            'test6',
            'Hatodik Teszt Felhasználó',
            'test@localhost.hu',
            $this->_model->hashPassword('password6')
        );
        
        $this->_model->addUser($newUser);
        
        $user = $this->_model->getUserById($newUser->id);
        
        $this->assertEquals($newUser, $user);
    }
    
    public function testSaltGeneratorReturnsTenHexCharacters()
    {
        $salt = $this->_model->generateSalt();
        
        $this->assertRegExp('/^[0-9a-f]{10}$/', $salt);
    }
    
    public function testGeneratedSaltChangesForEveryCall()
    {
        $salt1 = $this->_model->generateSalt();
        $salt2 = $this->_model->generateSalt();
        
        $this->assertNotEquals($salt1, $salt2);
    }
    
    public function testPasswordHashGeneratorReturnsFiftyHexCharacters()
    {
        $hash = $this->_model->hashPassword('');
        
        $this->assertRegExp('/^[0-9a-f]{50}$/', $hash);
    }
    
    public function testSamePasswordsHaveDifferentHashes()
    {
        $hash1 = $this->_model->hashPassword('');
        $hash2 = $this->_model->hashPassword('');
        
        $this->assertNotEquals($hash1, $hash2);
    }
    
    /**
     * @dataProvider passwordHashProvider
     */
    public function testCanValidatePasswordByHash($originalHash, $password, $valid)
    {
        $this->assertEquals($valid, $this->_model->validatePassword($originalHash, $password));
    }
    
    public function passwordHashProvider()
    {
        return array(
            array('f84aaea2797117edbcdfd8cd782a719c09c05e86e16fc5e9e5', 'password1', true),
            array('f84aaea2797117edbcdfd8cd782a719c09c05e86e16fc5e9e5', '', false),
            array('d2eb5ed4aa70e52e861c51156710a0ce86c03c38bae789f992', 'password2', true),
            array('d2eb5ed4aa70e52e861c51156710a0ce86c03c38bae789f992', 'password1', false),
            array('d2eb5ed4aa70e52e861c51156710a0ce86c03c38bae789f992', 'invalid password 2', false),
            array('6b99e0bc571965ad4473a4be44406ad1351e5eecf6751c6886', 'password3', true)
        );
    }
}