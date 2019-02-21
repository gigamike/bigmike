<?php
namespace Brand\Model;

class BrandEntity
{
	protected $id;
	protected $brand;
	protected $description;
	protected $logo_name;
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

	public function getBrand()
	{
		return $this->brand;
	}

	public function setBrand($value)
	{
		$this->brand = $value;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($value)
	{
		$this->description = $value;
	}

	public function getLogoName()
	{
		return $this->logo_name;
	}

	public function setLogoName($value)
	{
		$this->logo_name = $value;
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
