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
	public static function getIncomesCategories()
	{
		echo json_encode(Income::getIncomesCategories());
	}
	
	/**
	 * Get expenses category assigned to user
	 *
	 * @return array. The array of expenses
	 */
	public static function getExpensesCategories()
	{
		echo json_encode(Expense::getExpensesCategories());
	}
	
	/**
	 * Get payments category assigned to user
	 *
	 * @return array. The array of payments
	 */
	public static function getPaymentsCategories()
	{
		echo json_encode(Expense::getPaymentsCategories());
	}
	
	/**
	 * Update name category assigned to user
	 *
	 * @param array $_POST. 
	 *
	 * @return bolean. True if update success, false otherwise
	 */
	public static function updateIncomeCategory()
	{
		$name = ucwords(strtolower($_POST['name']));
		$id = $_POST['id'];
		if (Income::updateIncomeCategory($name, $id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * remove name of income category assigned to user
	 *
	 * @param array $_POST. 
	 *
	 * @return bolean. True if update success, false otherwise
	 */
	public static function removeIncomeCategory()
	{
		$name = $_POST['name'];
		$id = $_POST['id'];
		if (Income::removeIncomeCategory($name, $id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	
}