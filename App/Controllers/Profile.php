<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Date;

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
}