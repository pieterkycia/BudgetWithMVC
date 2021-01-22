<?php

namespace App;

/**
 * Date
 */
class Date
{
	/**
	 * Validation date
	 *
	 * @param string $date. The date to validate
	 *
	 * @return boolean. True if date is correct, false otherwise
	 */
	public static function validateDate($date)
	{
		if (static::checkFormat($date)) {
			if (static::checkValue($date)) {
				return true;
			}
		} 
		return false;
	}
	
	/**
	 * Check date format
	 *
	 * @param string $date. Date to check format
	 *
	 * @return boolean. True if format is correct, false otherwise
	 */
	protected static function checkFormat($date)
	{
		if (preg_match('/^[1-9]{1}[0-9]{3}-[0-9]{1,2}-[0-9]{1,2}$/', $date)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Check date value
	 *
	 * @param string $date. Date to check value
	 *
	 * @return boolean. True if value is correct, false otherwise
	 */
	protected static function checkValue($date)
	{
		$full_date = explode('-', $date);
		$year = $full_date[0];
		$month = $full_date[1];
		$day = $full_date[2];
		
		if (checkdate($month, $day, $year)) {
			return true;
		} else {
			return false;
		}
	}
}