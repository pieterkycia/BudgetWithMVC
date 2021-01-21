<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
	/*
	 * Error messages
	 *
	 * @var array
	 */
	public $errors = [];
	
	/*
	 * Class constructor
	 *
	 * @param array $data. Initial property values
	 *
	 * @return void
	 */
	public function __construct($data = []) 
	{
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

    /*
	 * Validate current property values, adding validation error messages to the errors array property
	 *
	 * @return void
	 */
	 
	public function validate()
	{
		// Name
		if ($this->username == '') {
			$this->errors['username'] = 'Name is required';
		}
		
		// Email address
		if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
			$this->errors['email'] = 'Invalid email'; 
		}
		
		if (static::emailExists($this->email)) {
			$this->errors['email'] = 'Email already taken'; 
		}
		
		// Password
		if (isset($this->password)) {
			if (strlen($this->password) < 6) {
				$this->errors['password'] = 'Please enter at least 6 characters for the password';
			}
			
			if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
				$this->errors['password'] = 'Passwords needs at least one letter';
			}
			
			if (preg_match('/.*\d+.*/i', $this->password) == 0) {
				$this->errors['password'] = 'Passwords needs at least one number';
			}	
		}
		
		//Password confirmation
		if ($this->password != $this->repeat_password) {
			$this->errors['repeat_password'] = 'Passwords must be the same';
		}
	}
	
	/*
	 * See if a user record already exists with the specified email
	 *
	 * @param string $email. Email address
	 *
	 * @return boolean. True if a record already exists with the specified email, false otherwise
	 */
	public static function emailExists($email) 
	{
		$user = static::findByEmail($email);
		
		if ($user) {
			return true;
		}
		return false;
	}
	
	/**
	 * Find a user model by email address
	 *
	 * @param string $email. Email address to search for
	 *
	 * @return mixed User object if found, false otherwise
	 */
	public static function findByEmail($email)
	{
		$sql = 'SELECT * FROM users WHERE email = :email';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	/**
	 * Find a user model by ID
	 *
	 * @param string $id. The user ID
	 *
	 * @return mixed. User object if found, false otherwise
	 */
	public static function findByID($id)
	{
		$sql = 'SELECT * FROM users WHERE id = :id';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	/**
     * Save the user model with the current property values
     *
     * @return void
     */
    public function save()
    {
		$this->validate();
		
		if (empty($this->errors)) {
			$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
			
			$sql = 'INSERT INTO users (username, password, email)
					VALUES (:username, :password_hash, :email)';
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
			
			return $stmt->execute();
		}
		return false;
    }
	
	/**
	 * Authenticate a user by email and password
	 *
	 * @param string $email. Email address
	 * @param string $password. PASSWORD_DEFAULT
	 *
	 * @return mixed. The user object or false if authentication fails
	 */
	public static function authenticate($email, $password)
	{
		$user = static::findByEmail($email);
		
		if ($user) {
			if (password_verify($password, $user->password)) {
				return $user;
			}
		}
		return false;
	}
}
