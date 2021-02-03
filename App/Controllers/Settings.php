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
			'incomes' => Income::getIncomes(),
			'expenses' => Expense::getExpenses(),
			'payments' => Expense::getPayments(),
			'user_id' => $_SESSION['user_id']
		]);
	}
}