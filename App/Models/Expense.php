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
	public static function getExpenses()
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
	 * Get full categories of payment from server
	 *
	 * @return array
	 */
	public static function getPayments()
	{
		$sql = 'SELECT id, name 
				FROM payment_methods_assigned_to_users
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
}