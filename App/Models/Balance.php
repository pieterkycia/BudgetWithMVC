<?php

namespace App\Models;

use PDO;

/**
 * Balance model
 */
class Balance extends \Core\Model
{
	/**
	 * Get sum of incomes for a given period
	 *
	 * @param string $startDate. The start date for a given period
	 * @param string $endDate. The end date for a given period
	 *
	 * @retrun array
	 */
	public static function getIncomes($startDate, $endDate)
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
	 * Get sum of expenses for a given period
	 *
	 * @param string $startDate. The start date for a given period
	 * @param string $endDate. The end date for a given period
	 *
	 * @retrun array
	 */
	public static function getExpenses($startDate, $endDate)
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
	
}