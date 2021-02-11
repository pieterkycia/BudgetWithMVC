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
	public static function getIncomesCategories()
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
	
	/**
	 * Get user incomes categories by id
	 *
	 * @retrun array
	 */
	protected static function getIncomesById()
	{
		$sql = 'SELECT income_category_assigned_to_user_id AS incomeId
				FROM incomes 
				WHERE user_id = :user_id';
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		
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
	 * Add income category to database
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
	 * Update income category in database
	 *
	 * @retrun boolean. True if update success, false otherwise
	 */
	public static function updateIncomeCategory($name, $id)
	{
		$savedIncomes = static::getIncomesCategories();

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
	
	/**
	 * Remove income category in database
	 *
	 * @retrun boolean. True if remove success, false otherwise
	 */
	public static function removeIncomeCategory($id)
	{
		$savedIncomes = static::getIncomesById();
		foreach ($savedIncomes as $key => $value) {
			
			if ($value['incomeId'] == $id) {
				static::moveCategoryToAnother($id);
			} 
		}
		$sql = 'DELETE FROM incomes_category_assigned_to_users
				WHERE id = :id';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
				
		return $stmt->execute();
	}
	
	/**
	 * Add income category to database
	 *
	 * @retrun boolean. True if add success, false otherwise
	 */
	public static function addIncomeCategory($name)
	{
		$savedIncomes = static::getIncomesCategories();
		foreach ($savedIncomes as $key => $value) {
			
			if ($value['name'] == $name) {
				return false;
			} 
		}
		$sql = 'INSERT INTO incomes_category_assigned_to_users
				VALUES (NULL, :user_id, :category_name)';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':category_name', $name, PDO::PARAM_STR);

		return $stmt->execute();	
	}
	
	/*
	 * Get id of incomes category named Another
	 *
	 * @return int
	 */
	protected static function getAnotherCategoryId()
	{
		$sql = 'SELECT id
				FROM incomes_category_assigned_to_users
				WHERE user_id = :user_id
				AND name = :name';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':name', 'Another', PDO::PARAM_STR);
		$stmt->execute();
		$id = $stmt->fetch();
		return $id['id'];
	}
	
	/*
	 * Move category to a category named Another
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	protected static function moveCategoryToAnother($id)
	{
		$anotherCategoryId = static::getAnotherCategoryId();
		$sql = 'UPDATE incomes
				SET income_category_assigned_to_user_id = :another_id
				WHERE income_category_assigned_to_user_id = :id';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':another_id', $anotherCategoryId, PDO::PARAM_INT);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
	}
	
}