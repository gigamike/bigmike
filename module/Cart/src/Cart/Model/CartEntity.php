<?php
namespace Cart\Model;

class CartEntity
{
	protected $id;
	protected $product_id;
	protected $quantity;
	protected $created_datetime;
	protected $created_user_id;

	public function __construct()
	{
		$this->created_datetime = date('Y-m-d H:i:s');
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($value)
	{
		$this->id = $value;
	}

	public function getProductId()
	{
		return $this->product_id;
	}

	public function setProductId($value)
	{
		$this->product_id = $value;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setQuantity($value)
	{
		$this->quantity = $value;
	}

	public function getCreatedDatetime()
	{
		return $this->created_datetime;
	}

	public function setCreatedDatetime($value)
	{
		$this->created_datetime = $value;
	}

	public function getCreatedUserId()
	{
		return $this->created_user_id;
	}

	public function setCreatedUserId($value)
	{
		$this->created_user_id = $value;
	}
}
