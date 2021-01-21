<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Signup controller
 */
class Signup extends \Core\Controller
{
	/**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
		if (isset($_SESSION['user_id'])) {
			View::renderTemplate('Profile/menu.html');
		} else {
			View::renderTemplate('Signup/new.html');
		}
    }
	
	/*
	 * Sign up a new user
	 *
	 * @return void
	 */
	public function createAction() 
	{
		$user = new User($_POST);
		
		if ($user->save()) {
			$this->redirect('/signup/success');
		} else {
			View::renderTemplate('Signup/new.html', [
				'user' => $user
			]);
		}
	} 
	
	/*
	 * Show the signup success page
	 *
	 * @return void
	 */
	public function successAction() {
		View::renderTemplate('Signup/new.html');
	}
}