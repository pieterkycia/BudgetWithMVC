<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;
use \App\Models\Expense;
/**
 * Settings controller
 */
class Settings extends Authenticated
{
	/**
	 * Show settings form
	 *
	 * @return void
	 */
	public function showSettingsAction()
	{
		View::renderTemplate('Settings/settings.html', [
			'user_id' => $_SESSION['user_id']
		]);
	}
	
	/**
	 * Get incomes category assigned to user
	 *
	 * @return array. The array of incomes
	 */
	public static function getIncomes()
	{
		echo json_encode(Income::getIncomes());
	}
	
	/**
	 * Get expenses category assigned to user
	 *
	 * @return array. The array of expenses
	 */
	public static function getExpenses()
	{
		echo json_encode(Expense::getExpenses());
	}
	
	/**
	 * Get payments category assigned to user
	 *
	 * @return array. The array of payments
	 */
	public static function getPayments()
	{
		echo json_encode(Expense::getPayments());
	}
	
	/**
	 * Update name category assigned to user
	 *
	 * @param string $name. The name of category.
	 * @param string $id. The id of category.
	 *
	 * @return bolean. True if update success, false otherwise
	 */
	public static function updateIncomeCategory()
	{
		$name = $_POST['name'];
		$id = $_POST['id'];
		if (Income::updateIncome($name, $id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	
}