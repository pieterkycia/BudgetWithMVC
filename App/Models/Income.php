<?php

namespace App\Models;

/**
 * Income model
 */
class Income extends \Core\Model
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
	
	/**
	 * Get full categories of incomes from server
	 *
	 * @retrun array
	 */
	public static function getIncomes()
	{
		$sql = 'SELECT id, name 
				FROM incomes_category_assigned_to_users 
				WHERE user_id = :user_id';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindParam(':user_id', $_SESSION['user_id']);
		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	 /*
	 * Validate current property values, adding validation error messages to the errors array property
	 *
	 * @return void
	 */
	 
	public function validate()
	{
		
	}
}