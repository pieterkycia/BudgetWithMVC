<?php

namespace App\Models;

use PDO;
use \App\Date;

/**
 * Expense model
 */
class Expense extends \Core\Model
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
	 * Get full categories of expenses from server
	 *
	 * @retrun array
	 */
	public static function getExpensesCategories()
	{
		$sql = 'SELECT id, name 
				FROM expenses_category_assigned_to_users 
				WHERE user_id = :user_id';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindParam(':user_id', $_SESSION['user_id']);
		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	/**
	 * Get user expenses categories by id
	 *
	 * @retrun array
	 */
	protected static function getExpensesById()
	{
		$sql = 'SELECT expense_category_assigned_to_user_id AS expenseId
				FROM expenses 
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
		
		//Payment radiobox
		if (! isset($this->payment_category)) {
			$this->errors['error_payment'] = 'Choose one category!';
		}
		
		//Radiobox
		if (! isset($this->category)) {
			$this->errors['error_radio'] = 'Choose one category!';
		}
	}
	
	/**
	 * Add expense to database
	 *
	 * @retrun boolean. True if object property values is correct, false otherwise
	 */
	public function addExpense()
	{
		$this->validate();
		
		if (empty($this->errors)) {
			
			$sql = 'INSERT INTO expenses 
					VALUES (NULL, :user_id, :category, :payment_category, :amount, :date, :comment)';
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(':category', $this->category, PDO::PARAM_INT);
			$stmt->bindValue(':payment_category', $this->payment_category, PDO::PARAM_INT);
			$stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
			$stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
			$stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
			
			return $stmt->execute();
		}
		return false;
	}
	
	/**
	 * Update expense category in database
	 *
	 * @retrun boolean. True if update success, false otherwise
	 */
	public static function updateExpenseCategory($name, $id)
	{
		$savedExpenses = static::getExpensesCategories();

		foreach ($savedExpenses as $key => $value) {

			if ($value['name'] == $name) {
				return false;
			} 
		}
		$sql = 'UPDATE expenses_category_assigned_to_users
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
	 * Add expense category to database
	 *
	 * @retrun boolean. True if add success, false otherwise
	 */
	public static function addExpenseCategory($name)
	{
		$savedExpenses = static::getExpensesCategories();
		foreach ($savedExpenses as $key => $value) {
			
			if ($value['name'] == $name) {
				return false;
			} 
		}
		$sql = 'INSERT INTO expenses_category_assigned_to_users
				VALUES (NULL, :user_id, :category_name)';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':category_name', $name, PDO::PARAM_STR);

		return $stmt->execute();	
	}
	
	/**
	 * Remove expense category in database
	 *
	 * @retrun boolean. True if remove success, false otherwise
	 */
	public static function removeExpenseCategory($id)
	{
		$savedExpenses = static::getExpensesById();
		foreach ($savedExpenses as $key => $value) {
			
			if ($value['expenseId'] == $id) {
				static::moveCategoryToAnother($id);
			} 
		}
		$sql = 'DELETE FROM expenses_category_assigned_to_users
				WHERE id = :id';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
				
		return $stmt->execute();
	}
	
	/*
	 * Get id of expense category named Another
	 *
	 * @return int
	 */
	protected static function getAnotherCategoryId()
	{
		$sql = 'SELECT id
				FROM expenses_category_assigned_to_users
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
		$sql = 'UPDATE expenses
				SET expense_category_assigned_to_user_id = :another_id
				WHERE expense_category_assigned_to_user_id = :id';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':another_id', $anotherCategoryId, PDO::PARAM_INT);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
	}
}