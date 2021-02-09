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
	 * Get category assigned to user
	 *
	 * @return array. The array of categories
	 */
	public static function getCategories()
	{
		$type = $_POST['type'];
		switch ($type) {
			case 'income':
				echo json_encode(Income::getIncomesCategories());
				break;
			case 'expense':
				echo json_encode(Expense::getExpensesCategories());
				break;
			case 'payment':
				echo json_encode(Expense::getPaymentsCategories());
				break;
		}	
	}
	
	/**
	 * Update name category assigned to user
	 *
	 * @param array $_POST. 
	 *
	 * @return bolean. True if update success, false otherwise
	 */
	public static function updateCategory()
	{
		$name = ucwords(strtolower($_POST['name']));
		$id = $_POST['id'];
		$type = $_POST['type'];
		
		if ($name == '') {
			echo 'false';
			return;
		}
		$error = true;
		switch ($type) {
			case 'income':
				if (Income::updateIncomeCategory($name, $id)) {
					$error = false;
				}
				break;
			case 'expense':
				if (Expense::updateExpenseCategory($name, $id)) {
					$error = false;
				}
				break;
			case 'payment':
				if (Expense::updatePaymentCategory($name, $id)) {
					$error = false;
				}
				break;
		}
		if ($error) {
			echo 'false';
		} else {
			echo 'true';
		}				
	}	
	
	/**
	 * remove name  category assigned to user
	 *
	 * @param array $_POST. 
	 *
	 * @return bolean. True if remove success, false otherwise
	 */
	public static function removeCategory()
	{
		$id = $_POST['id'];
		$type = $_POST['type'];
	
		$error = true;
		switch ($type) {
			case 'income':
				if (Income::removeIncomeCategory($id)) {
					$error = false;
				}
				break;
			case 'expense':
				if (Expense::removeExpenseCategory($id)) {
					$error = false;
				}
				break;
			case 'payment':
				if (Expense::removePaymentCategory($id)) {
					$error = false;
				}
				break;
		}
		if ($error) {
			echo 'false';
		} else {
			echo 'true';
		}				
	}	
	
	/**
	 * add new category 
	 *
	 * @param array $_POST
	 *
	 * @return bolean. True if add success, false otherwise
	 */
	public static function addCategory()
	{
		$name = ucwords(strtolower($_POST['name']));
		$type = $_POST['type'];
		
		if ($name == '') {
			echo 'false';
			return;
		}
		$error = true;
		switch ($type) {
			case 'income':
				if (Income::addIncomeCategory($name)) {
					$error = false;
				}
				break;
			case 'expense':
				if (Expense::addExpenseCategory($name)) {
					$error = false;
				}
				break;
			case 'payment':
				if (Expense::addPaymentCategory($name)) {
					$error = false;
				}
				break;
		}
		if ($error) {
			echo 'false';
		} else {
			echo 'true';
		}				
	}
	
	
}