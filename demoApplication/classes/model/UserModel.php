<?php

namespace app\model;

use \PDO;

class UserModel extends DatabaseModel
{
    public function getUserById($id)
    {
        return $this->_getUserByFieldValue(User::FIELD_ID, $id, PDO::PARAM_INT);
    }
    
    public function getUserByName($name)
    {
        return $this->_getUserByFieldValue(User::FIELD_NAME, $name);
    }
    
    public function getUserByEmail($email)
    {
        return $this->_getUserByFieldValue(User::FIELD_EMAIL, $email);
    }
    
    private function _getUserByFieldValue($fieldName, $value, $type = PDO::PARAM_STR)
    {
        $statement = $this->_connection->prepare('SELECT * FROM `user` WHERE `' . $fieldName . '` = :param');
        $statement->bindValue(':param', $value, $type);
        $statement->execute();
        
        $result = $statement->fetchObject('\\app\\model\\User');
        
        return false !== $result ? $result : null;
    }
    
    public function addUser(User $newUser)
    {
        if (null !== $this->getUserByName($newUser->name))
        {
            throw DatabaseModelException::uniqueConstraintFail(User::FIELD_NAME);
        }
        
        if (null !== $this->getUserByEmail($newUser->email))
        {
            throw DatabaseModelException::uniqueConstraintFail(User::FIELD_EMAIL);
        }
        
        $statement = $this->_connection->prepare(
            'INSERT INTO `user`(`name`,`full_name`,`email`,`password`)' .
            'VALUES(:name, :full_name, :email, :password)'
        );
        $statement->execute(array(
            ':name'      => $newUser->name,
            ':full_name' => $newUser->full_name,
            ':email'     => $newUser->email,
            ':password'  => $newUser->password
        ));
        
        $newUser->id = $this->_connection->lastInsertId();
    }
    
    public function generateSalt()
    {
        return substr(str_shuffle(md5(uniqid(rand(), true))), 0, 10);
    }
    
    public function hashPassword($password, $salt = '')
    {
        if (10 != strlen($salt))
        {
            $salt = $this->generateSalt();
        }
        
        $hash = sha1($password . $salt);
        
        return substr($hash, 0, 20) . $salt . substr($hash, 20);
    }
    
    public function validatePassword($originalHash, $password)
    {
        $originalSalt = substr($originalHash, 20, 10);
        $passwordHash = $this->hashPassword($password, $originalSalt);
        
        return 0 === strcmp($originalHash, $passwordHash);
    }
}