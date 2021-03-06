<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

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
			$this->redirect('/profile/showBalance');
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
			$user = User::findByEmail($user->email);
			$user->addFullDefaultCategoriesToUser();
			
			$this->redirect('/signup/success');
		} else {
			Flash::addMessage('Signup unsuccessful, please try again', Flash::WARNING);
			
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
		Flash::addMessage('Signup successful');
		
		View::renderTemplate('Login/new.html');
	}
}