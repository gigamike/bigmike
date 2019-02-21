<?php
namespace Product\Model;

class ProductCategoryEntity
{
	protected $id;
	protected $product_id;
	protected $category_id;

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

	public function getCategoryId()
	{
		return $this->category_id;
	}

	public function setCategoryId($value)
	{
		$this->category_id = $value;
	}
}
