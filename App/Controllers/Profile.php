<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Date;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Payment;
use \App\Models\Balance;

/**
 * Profile controller
 */
class Profile extends Authenticated
{
	/**
	 * Before filter - called before each action method_exists
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();
		$this->user = Auth::getUser();
	}
	
	/**
	 * Show the profile menu
	 *
	 * @return void
	 */
	public function menuAction()
	{
		$startDate = Date::getFirstDayOfCurrentMonth();
		$endDate = Date::getLastDayOfCurrentMonth();
		
		$balance = new Balance($startDate, $endDate);
		
		View::renderTemplate('Profile/menu.html', [
			'balance' => $balance,
		]);

		$_SESSION['expenses'] = $balance->expenses;
	}
	
	/**
	 * Show add income form
	 *
	 * @return void
	 */
	public function incomeFormAction()
	{
		View::renderTemplate('Income/addIncome.html', [
			'date' => date('Y-m-d'),
			'incomes' => Income::getIncomesCategories()
		]);
	}
	
	/**
	 * Add new income to database
	 *
	 * @return void
	 */
	public function addIncomeAction()
	{
		$income = new Income($_POST);
		
		if ($income->addIncome()) {
			Flash::addMessage('Add income successful!');
			
			$this->redirect('/profile/incomeForm');
		} else {
			Flash::addMessage('Add income unsuccessful, please try again!', Flash::WARNING);
			
			View::renderTemplate('Income/addIncome.html', [
			'date' => $income->date,
			'incomes' => Income::getIncomesCategories(),
			'income' => $income
		]);
		}
	}
	
	/**
	 * Show add expense form
	 *
	 * @return void
	 */
	public function expenseFormAction()
	{
		View::renderTemplate('Expense/addExpense.html', [
			'date' => date('Y-m-d'),
			'payments' => Payment::getPaymentsCategories(),
			'expenses' => Expense::getExpensesCategories()
		]);
	}
	
	/**
	 * Add new expense to database
	 *
	 * @return void
	 */
	public function addExpenseAction()
	{
		$expense = new Expense($_POST);
		
		if ($expense->addExpense()) {
			Flash::addMessage('Add expense successful!');
			
			$this->redirect('/profile/expenseForm');
		} else {
			Flash::addMessage('Add expense unsuccessful, please try again!', Flash::WARNING);
			
			View::renderTemplate('Expense/addExpense.html', [
			'date' => $expense->date,
			'payments' => Payment::getPaymentsCategories(),
			'expenses' => Expense::getExpensesCategories(),
			'expense' => $expense
		]);
		}
	}
	
	/**
	 * Show balance for given period
	 *
	 * @return void
	 */
	public function showBalanceAction()
	{
		if (isset($_POST['selectDate'])) {
			$choice = $_POST['selectDate'];
		} else {
			$choice = 1;
		}
		
		switch($choice) {
			case 1:
				$startDate = Date::getFirstDayOfCurrentMonth();
				$endDate = Date::getLastDayOfCurrentMonth();
				$option = 'option1';
				break;
			case 2:
				$startDate = Date::getFirstDayOfPreviousMonth();
				$endDate = Date::getLastDayOfPreviousMonth();
				$option = 'option2';
				break;
			case 3:
				$startDate = Date::getFirstDayOfCurrentYear();
				$endDate = Date::getLastDayOfCurrentYear();
				$option = 'option3';
				break;
			case 4:
				$startDate = $_POST['startDate'];
				$endDate = $_POST['endDate'];
				$option = 'option4';
				break;
		}
	
		$balance = new Balance($startDate, $endDate);
		
		View::renderTemplate('Balance/showBalance.html', [
			'balance' => $balance,
			$option => 'selected'
		]);
		
		$_SESSION['expenses'] = $balance->expenses;
	}
	
	/**
	 * Check dates for custom period
	 *
	 * @retrun boolean. True if dates are correct, false otherwise
	 */
	public function checkDatesAction()
	{
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		if (Date::validateDate($startDate) && Date::validateDate($endDate) && $startDate <= $endDate) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * Get Expenses for chart
	 *
	 * @return array dates
	 */
	public function getExpensesAction()
	{
		echo json_encode($_SESSION['expenses']);
	}
	
	
}