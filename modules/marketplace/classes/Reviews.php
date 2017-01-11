<?php
class Reviews extends ObjectModel
{
	public $id_review;
	public $id_seller;
	public $id_customer;
	public $customer_email;
	public $rating;
	public $review;
	public $active;
	public $date_add;
	
	public static $definition = array(
		'table' => 'marketplace_seller_reviews',
		'primary' => 'id_review',
		'fields' => array(
			'id_seller' => array('type' => self::TYPE_INT),
			'id_customer' => array('type' => self::TYPE_INT),
			'customer_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
			'rating' => array('type' => self::TYPE_INT),		
			'review' => array('type' => self::TYPE_STRING),	
			'active' => array('type' => self::TYPE_INT),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		),
	);

	public function getSellerReviewById($id)
	{
		$review = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_reviews` WHERE id_review = '.(int)$id);
		if ($review)
			return $review;

		return false;
	}

	public static function getSellerReviewByIdSeller($id_seller, $active = true)
	{
		$reviews = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_reviews`
								WHERE `id_seller` ='.$id_seller.'
								AND `active` ='.($active ? '1' : '0').' ORDER BY date_add DESC');
		if (!empty($reviews))
			return $reviews;

		return false;
	}

	public static function getSellerAvgRating($id_seller)
	{
		$reviews = self::getSellerReviewByIdSeller($id_seller);
		if ($reviews)
		{
			$rating = 0;
			foreach($reviews as $review)
				$rating = $rating + $review['rating'];
			
			$avg_rating = (double)($rating/count($reviews));
			return $avg_rating;
		}
		return false;
	}
}