<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Date;
use \App\Models\Income;
use \App\Models\Expense;

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
	 * Show the profile
	 *
	 * @return void
	 */
	public function menuAction()
	{
		View::renderTemplate('Profile/menu.html', [
			'user' => $this->user
		]);
	}
	
	public function incomeFormAction()
	{
		View::renderTemplate('Income/addIncome.html', [
			'date' => date('Y-m-d'),
			'incomes' => Income::getIncomes()
		]);
	}
	
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
			'incomes' => Income::getIncomes(),
			'income' => $income
		]);
		}
	}
	
	public function expenseFormAction()
	{
		View::renderTemplate('Expense/addExpense.html', [
			'date' => date('Y-m-d'),
			'payments' => Expense::getPayments(),
			'expenses' => Expense::getExpenses()
		]);
	}
	
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
			'payments' => Expense::getPayments(),
			'expenses' => Expense::getExpenses(),
			'expense' => $expense
		]);
		}
	}
}