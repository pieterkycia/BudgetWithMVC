<?php

namespace App\Models;

use PDO;

/**
 * User model
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
			$this->errors['error_username'] = 'Name is required!';
		}
		
		// Email address
		if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
			$this->errors['error_email'] = 'Invalid email!'; 
		}
		
		if (static::emailExists($this->email)) {
			$this->errors['error_email'] = 'Email already taken!'; 
		}
		
		// Password
		if (isset($this->password)) {
			if (strlen($this->password) < 6) {
				$this->errors['error_password'] = 'Please enter at least 6 characters for the password!';
			}
			
			if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
				$this->errors['error_password'] = 'Password needs at least one letter!';
			}
			
			if (preg_match('/.*\d+.*/i', $this->password) == 0) {
				$this->errors['error_password'] = 'Password needs at least one number!';
			}	
		}
		
		//Password confirmation
		if ($this->password != $this->repeat_password) {
			$this->errors['error_repeat_password'] = 'Passwords must be the same!';
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
	 * Add default categories to user
	 *
	 * @param string $categoryType. The type of category
	 *
	 * @return void
	 */
	protected function addDefaultCategoriesToUser($categoryType)
	{
		$sql = 'SELECT name FROM ' . $categoryType . '_default';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
			
		$stmt->execute();
		$categories = $stmt->fetchAll();
		
		if ($categoryType == 'expenses_category') {
			$sql = 'INSERT INTO ' . $categoryType . '_assigned_to_users 
				VALUES (NULL, :user_id, :category, 0)';
		} else {
			$sql = 'INSERT INTO ' . $categoryType . '_assigned_to_users 
				VALUES (NULL, :user_id, :category)';
		}
		
		foreach ($categories as $category) {
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
			$stmt->bindValue(':category', $category['name'], PDO::PARAM_STR);
			
			$stmt->execute();
		}
	}
	
	/**
	 * Add full default categories to user
	 *
	 * @return void
	 */
	public function addFullDefaultCategoriesToUser()
	{
		$this->addDefaultCategoriesToUser('incomes_category');
		$this->addDefaultCategoriesToUser('expenses_category');
		$this->addDefaultCategoriesToUser('payment_methods');
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
	
	public static function editUserName($name) {
		$sql = 'UPDATE users
				SET username = :username
				WHERE id = :user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue('username', $name, PDO::PARAM_STR);
		$stmt->bindValue('user_id', $_SESSION['user_id'], PDO::PARAM_STR);
		return $stmt->execute();
	}
	
	public static function editUserEmail($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			return false;
		}
		
		if (static::emailExists($email)) {
			return false;
		}

		$sql = 'UPDATE users
				SET email = :email
				WHERE id = :user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue('email', $email, PDO::PARAM_STR);
		$stmt->bindValue('user_id', $_SESSION['user_id'], PDO::PARAM_STR);
		return $stmt->execute();
	}
	
	public static function editUserPassword($password)
	{
		if (strlen($password) < 6) {
			return false;
			//$this->errors['error_password'] = 'Please enter at least 6 characters for the password!';
		}

		if (preg_match('/.*[a-z]+.*/i', $password) == 0) {
			return false;
			//$this->errors['error_password'] = 'Password needs at least one letter!';
		}
			
		if (preg_match('/.*\d+.*/i', $password) == 0) {
			return false;
			//$this->errors['error_password'] = 'Password needs at least one number!';
		}	
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		
		$sql = 'UPDATE users
				SET password = :password_hash
				WHERE id = :user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue('password_hash', $password_hash, PDO::PARAM_STR);
		$stmt->bindValue('user_id', $_SESSION['user_id'], PDO::PARAM_STR);
		return $stmt->execute();
	}
}
