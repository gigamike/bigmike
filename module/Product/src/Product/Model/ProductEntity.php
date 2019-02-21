<?php
namespace Product\Model;

class ProductEntity
{
	protected $id;
	protected $brand_id;
	protected $name;
	protected $description;
	protected $photo_name1;
	protected $photo_name2;
	protected $photo_name3;
	protected $panorama;
	protected $price;
	protected $stock;
	protected $discount_type;
	protected $discount;
	protected $created_datetime;
	protected $created_user_id;
	protected $modified_datetime;
	protected $modified_user_id;

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

	public function getBrandId()
	{
		return $this->brand_id;
	}

	public function setBrandId($value)
	{
		$this->brand_id = $value;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($value)
	{
		$this->name = $value;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($value)
	{
		$this->description = $value;
	}

	public function getPhotoName1()
	{
		return $this->photo_name1;
	}

	public function setPhotoName1($value)
	{
		$this->photo_name1 = $value;
	}

	public function getPhotoName2()
	{
		return $this->photo_name2;
	}

	public function setPhotoName2($value)
	{
		$this->photo_name2 = $value;
	}

	public function getPhotoName3()
	{
		return $this->photo_name3;
	}

	public function setPhotoName3($value)
	{
		$this->photo_name3 = $value;
	}

	public function getPanorama()
	{
		return $this->panorama;
	}

	public function setPanorama($value)
	{
		$this->panorama = $value;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function setPrice($value)
	{
		$this->price = $value;
	}

	public function getStock()
	{
		return $this->stock;
	}

	public function setStock($value)
	{
		$this->stock = $value;
	}

	public function getDiscountType()
	{
		return $this->discount_type;
	}

	public function setDiscountType($value)
	{
		$this->discount_type = $value;
	}

	public function getDiscount()
	{
		return $this->discount;
	}

	public function setDiscount($value)
	{
		$this->discount = $value;
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

	public function getModifiedDatetime()
	{
		return $this->modified_datetime;
	}

	public function setModifiedDatetime($value)
	{
		$this->modified_datetime = $value;
	}

	public function getModifiedUserId()
	{
		return $this->modified_user_id;
	}

	public function setModifiedUserId($value)
	{
		$this->modified_user_id = $value;
	}
}
