<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Payment;
use \App\Models\User;
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
	public static function getCategoriesAction()
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
				echo json_encode(Payment::getPaymentsCategories());
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
	public static function updateCategoryAction()
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
				$response = Expense::updateExpenseCategory($name, $id, $_POST['limit']); 
				echo $response;
				return;
			case 'payment':
				if (Payment::updatePaymentCategory($name, $id)) {
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
	public static function removeCategoryAction()
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
				if (Payment::removePaymentCategory($id)) {
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
	public static function addCategoryAction()
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
				if (Payment::addPaymentCategory($name)) {
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
	
	/*
	 * Get user data
	 *
	 * @return array
	 */
	public static function getUserDataAction()
	{
		echo json_encode(User::findById($_SESSION['user_id']));
	}
	
	/*
	 * Edit user name
	 *
	 * @return boolean. True if edit name success, false otherwise
	 */
	public static function editUserNameAction()
	{
		$name = ucwords(strtolower($_POST['name']));
		
		if ($name == '') {
			echo 'false';
			return;
		}
		if (User::editUserName($name)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/*
	 * Edit user email
	 *
	 * @return boolean. True if edit email success, false otherwise
	 */
	public static function editUserEmailAction()
	{
		$email = $_POST['email'];
		
		if ($email == '') {
			echo 'false';
			return;
		}
		if (User::editUserEmail($email)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/*
	 * Edit user password
	 *
	 * @return boolean. True if edit password success, false otherwise
	 */
	public static function editUserPasswordAction()
	{
		$password = $_POST['password'];
		
		if ($password == '') {
			echo 'false';
			return;
		}
		if (User::editUserPassword($password)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	
}