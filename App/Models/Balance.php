<?php

namespace App\Models;

use PDO;

/**
 * Balance model
 */
class Balance extends \Core\Model
{
	/**
	 * Incomes
	 *
	 * @var array
	 */
	public $incomes = [];
	
	/**
	 * Expenses
	 *
	 * @var array
	 */
	public $expenses = [];
	
	/**
	 * Sum of incomes
	 *
	 * @var string
	 */
	public $sumOfIncomes;
	
	/**
	 * Sum of expenses
	 *
	 * @var string
	 */
	public $sumOfExpenses;
	
	/**
	 * Class constructor
	 *
	 * @param string $startDate. The start date for a given period
	 * @param string $endDate. The end date for a given period
	 */
	public function __construct($startDate, $endDate)
	{
		$this->incomes = $this->getIncomes($startDate, $endDate);
		$this->expenses = $this->getExpenses($startDate, $endDate);
		$this->sumOfIncomes = $this->getSumOfIncomes($startDate, $endDate);
		$this->sumOfExpenses = $this->getSumOfExpenses($startDate, $endDate);
	}
	
	/**
	 * Get incomes for a given period
	 *
	 * @param string $startDate. The start date for a given period
	 * @param string $endDate. The end date for a given period
	 *
	 * @retrun array
	 */
	private function getIncomes($startDate, $endDate)
	{
		$sql = 'SELECT name, SUM(amount) AS amount
				FROM incomes AS i
				INNER JOIN incomes_category_assigned_to_users AS ic
				ON i.income_category_assigned_to_user_id = ic.id 
				AND i.user_id = ic.user_id 
				AND ic.user_id = :user_id
				AND date_of_income BETWEEN :startDate AND :endDate
				GROUP BY name 
				ORDER BY amount DESC';
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
		$stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	/**
	 * Get expenses for a given period
	 *
	 * @param string $startDate. The start date for a given period
	 * @param string $endDate. The end date for a given period
	 *
	 * @retrun array
	 */
	private function getExpenses($startDate, $endDate)
	{
		$sql = 'SELECT name, SUM(amount) AS amount
				FROM expenses AS e
				INNER JOIN expenses_category_assigned_to_users AS ec
				ON e.expense_category_assigned_to_user_id = ec.id 
				AND e.user_id = ec.user_id 
				AND ec.user_id = :user_id
				AND date_of_expense BETWEEN :startDate AND :endDate
				GROUP BY name 
				ORDER BY amount DESC';
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
		$stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	/**
	 * **
	 * Get sum of incomes for a given period
	 *
	 * @param string $startDate. The start date for a given period
	 * @param string $endDate. The end date for a given period
	 *
	 * @retrun array
	 */
	private function getSumOfIncomes($startDate, $endDate)
	{
		$sql = 'SELECT SUM(amount) AS sum 
				FROM incomes AS i
				INNER JOIN incomes_category_assigned_to_users AS ic
				ON i.income_category_assigned_to_user_id = ic.id 
				AND i.user_id = ic.user_id 
				AND ic.user_id = :user_id
				AND date_of_income BETWEEN :startDate AND :endDate
				ORDER BY amount DESC';
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
		$stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$sum = $stmt->fetch();

		if ($sum[0] != NULL) {
			return $sum[0];
		} else {
			return 0;
		}
	}
	
	/**
	 * Get sum of expenses for a given period
	 *
	 * @param string $startDate. The start date for a given period
	 * @param string $endDate. The end date for a given period
	 *
	 * @retrun array
	 */
	private function getSumOfExpenses($startDate, $endDate)
	{
		$sql = 'SELECT SUM(amount) AS sum 
				FROM expenses AS e
				INNER JOIN expenses_category_assigned_to_users AS ec
				ON e.expense_category_assigned_to_user_id = ec.id 
				AND e.user_id = ec.user_id 
				AND ec.user_id = :user_id
				AND date_of_expense BETWEEN :startDate AND :endDate
				ORDER BY amount DESC';
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
		$stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$sum = $stmt->fetch();

		if ($sum[0] != NULL) {
			return $sum[0];
		} else {
			return 0;
		}
	}
	
}