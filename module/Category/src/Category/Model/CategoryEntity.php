<?php
namespace Category\Model;

class CategoryEntity
{
	protected $id;
	protected $category;
	protected $photo_name;
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

	public function getCategory()
	{
		return $this->category;
	}

	public function setCategory($value)
	{
		$this->category = $value;
	}

	public function getPhotoName()
	{
		return $this->photo_name;
	}

	public function setPhotoName($value)
	{
		$this->photo_name = $value;
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
