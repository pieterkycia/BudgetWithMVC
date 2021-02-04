<?php

namespace App\Models;

use PDO;
use \App\Date;

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
	 
	protected function validate()
	{
		//Amount
		$this->amount = preg_replace('/,/', '.', $this->amount);
		if (! preg_match('/^[0-9]+(\.[0-9]{1,2}){0,1}$/', $this->amount)) {
			$this->errors['error_amount'] = 'Invalid amount!';
		}
		
		//Date
		if (! Date::validateDate($this->date)) {
			$this->errors['error_date'] = 'Invalid date!';
		}
		
		//Radiobox
		if (! isset($this->category)) {
			$this->errors['error_radio'] = 'Choose one category!';
		}
	}
	
	/**
	 * Add income to database
	 *
	 * @retrun boolean. True if object property values is correct, false otherwise
	 */
	public function addIncome()
	{
		$this->validate();
		
		if (empty($this->errors)) {
			
			$sql = 'INSERT INTO incomes 
					VALUES (NULL, :user_id, :category, :amount, :date, :comment)';
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(':category', $this->category, PDO::PARAM_INT);
			$stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
			$stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
			$stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
			
			return $stmt->execute();
		}
		return false;
	}
	
	/**
	 * Update income in database
	 *
	 * @retrun boolean. True if True if update success, false otherwise
	 */
	public static function updateIncome($name, $id)
	{
		$savedIncomes = static::getIncomes();

		foreach ($savedIncomes as $key => $value) {

			if ($value['name'] == $name) {
				return false;
			} 
		}
		$sql = 'UPDATE incomes_category_assigned_to_users
				SET name = :name
				WHERE id = :id';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
				
		$stmt->execute();
		return true;	
	}
	
}