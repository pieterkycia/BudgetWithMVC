<?php

namespace App\Controllers;

use \Core\View;
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
		View::renderTemplate('Settings/settings.html');
	}
}