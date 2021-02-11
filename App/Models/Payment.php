<?php

namespace App\Models;

use PDO;

/**
 * Payment model
 */
class Payment extends \Core\Model
{
	/**
	 * Get full categories of payments from server
	 *
	 * @retrun array
	 */
	public static function getPaymentsCategories()
	{
		$sql = 'SELECT id, name 
				FROM payment_methods_assigned_to_users
				WHERE user_id = :user_id';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindParam(':user_id', $_SESSION['user_id']);
		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	/**
	 * Get user payments categories by id
	 *
	 * @retrun array
	 */
	protected static function getPaymentsById()
	{
		$sql = 'SELECT payment_method_assigned_to_user_id AS paymentId
				FROM expenses 
				WHERE user_id = :user_id';
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	/**
	 * Update payment category in database
	 *
	 * @retrun boolean. True if update success, false otherwise
	 */
	public static function updatePaymentCategory($name, $id)
	{
		$savedPayments = static::getPaymentsCategories();

		foreach ($savedPayments as $key => $value) {

			if ($value['name'] == $name) {
				return false;
			} 
		}
		$sql = 'UPDATE payment_methods_assigned_to_users
				SET name = :name
				WHERE id = :id';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
				
		$stmt->execute();
		return true;	
	}
	
	/**
	 * Add payment category to database
	 *
	 * @retrun boolean. True if add success, false otherwise
	 */
	public static function addPaymentCategory($name)
	{
		$savedPayments = static::getPaymentsCategories();
		foreach ($savedPayments as $key => $value) {
			
			if ($value['name'] == $name) {
				return false;
			} 
		}
		$sql = 'INSERT INTO payment_methods_assigned_to_users
				VALUES (NULL, :user_id, :category_name)';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':category_name', $name, PDO::PARAM_STR);

		return $stmt->execute();	
	}
	
	/**
	 * Remove payment category in database
	 *
	 * @retrun boolean. True if remove success, false otherwise
	 */
	public static function removePaymentCategory($id)
	{
		$savedPayments = static::getPaymentsById();
		foreach ($savedPayments as $key => $value) {
			
			if ($value['paymentId'] == $id) {
				static::moveCategoryToAnother($id);
			} 
		}
		$sql = 'DELETE FROM payment_methods_assigned_to_users
				WHERE id = :id';
					
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
				
		return $stmt->execute();
	}
	
	/*
	 * Get id of payment category named Another
	 *
	 * @return int
	 */
	protected static function getAnotherCategoryId()
	{
		$sql = 'SELECT id
				FROM payment_methods_assigned_to_users
				WHERE user_id = :user_id
				AND name = :name';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':name', 'Another', PDO::PARAM_STR);
		$stmt->execute();
		$id = $stmt->fetch();
		return $id['id'];
	}
	
	/*
	 * Move category to a category named Another
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	protected static function moveCategoryToAnother($id)
	{
		$anotherCategoryId = static::getAnotherCategoryId();
		$sql = 'UPDATE expenses
				SET payment_method_assigned_to_user_id = :another_id
				WHERE payment_method_assigned_to_user_id = :id';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':another_id', $anotherCategoryId, PDO::PARAM_INT);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
	}
}