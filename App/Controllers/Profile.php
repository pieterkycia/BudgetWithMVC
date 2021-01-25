<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Date;
use \App\Models\Income;

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
}