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
	
	/**
	 * Get first day of current month
	 *
	 * @return string $date. Date with first day of current month
	 */
	public static function getFirstDayOfCurrentMonth()
	{
		$dateInSeconds = mktime(0, 0, 0, date('m'), 1, date('Y'));
		$date = date('Y-m-01', $dateInSeconds);
		return $date;
	}
	
	/**
	 * Get last day of current month
	 *
	 * @return string $date. Date with last day of current month
	 */
	public static function getLastDayOfCurrentMonth()
	{
		$dateInSeconds = mktime(0, 0, 0, date('m')+1, 0, date('Y'));
		$date = date('Y-m-d', $dateInSeconds);
		return $date;
	}
	
	/**
	 * Get first day of previous month
	 *
	 * @return string $date. Date with first day of previous month
	 */
	public static function getFirstDayOfPreviousMonth()
	{
		$dateInSeconds = mktime(0, 0, 0, date('m')-1, 1, date('Y'));
		$date = date('Y-m-d', $dateInSeconds);
		return $date;
	}
	
	/**
	 * Get last day of previous month
	 *
	 * @return string $date. Date with last day of previous month
	 */
	public static function getLastDayOfPreviousMonth()
	{
		$dateInSeconds = mktime(0, 0, 0, date('m'), 0, date('Y'));
		$date = date('Y-m-d', $dateInSeconds);
		return $date;
	}
	
	/**
	 * Get first day of previous year
	 *
	 * @return string $date. Date with first day of previous year
	 */
	public static function getFirstDayOfCurrentYear()
	{
		$dateInSeconds = mktime(0, 0, 0, 1, 1, date('Y'));
		$date = date('Y-m-d', $dateInSeconds);
		return $date;
	}
	
	/**
	 * Get last day of previous year
	 *
	 * @return string $date. Date with last day of previous year
	 */
	public static function getLastDayOfCurrentYear()
	{
		$dateInSeconds = mktime(0, 0, 0, 12, 31, date('Y'));
		$date = date('Y-m-d', $dateInSeconds);
		return $date;
	}
	
	/**
	 * Get first day of given date
	 *
	 *@param string $date. Given date
	 *
	 * @return string $date. Date with first day of given date
	 */
	public static function getFirstDayOfGivenDate($date)
	{
		$full_date = explode('-', $date);
		$year = $full_date[0];
		$month = $full_date[1];
		$day = $full_date[2];
		
		$dateInSeconds = mktime(0, 0, 0, $month, 1, $year);
		$date = date('Y-m-d', $dateInSeconds);
		return $date;
	}
	
	/**
	 * Get last day of given date
	 *
	 *@param string $date. Given date
	 *
	 * @return string $date. Date with last day of given date
	 */
	public static function getLastDayOfGivenDate($date)
	{
		$full_date = explode('-', $date);
		$year = $full_date[0];
		$month = $full_date[1];
		$day = $full_date[2];
		
		$dateInSeconds = mktime(0, 0, 0, $month+1, 0, $year);
		$date = date('Y-m-d', $dateInSeconds);
		return $date;
	}
	
}