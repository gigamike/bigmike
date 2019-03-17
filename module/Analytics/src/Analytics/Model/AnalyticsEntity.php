<?php
namespace Incentive\Model;

class IncentiveEntity
{
	protected $id;
	protected $height_centimeters;
	protected $weight_kilograms;
	protected $bmi;
	protected $bmi_category;
	protected $incentive;
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

	public function getHeightCentimeters()
	{
		return $this->height_centimeters;
	}

	public function setHeightCentimeters($value)
	{
		$this->height_centimeters = $value;
	}

	public function getWeightKilograms()
	{
		return $this->weight_kilograms;
	}

	public function setWeightKilograms($value)
	{
		$this->weight_kilograms = $value;
	}

	public function getBmi()
	{
		return $this->bmi;
	}

	public function setBmi($value)
	{
		$this->bmi = $value;
	}

	public function getBmiCategory()
	{
		return $this->bmi_category;
	}

	public function setBmiCategory($value)
	{
		$this->bmi_category = $value;
	}

	public function getIncentive()
	{
		return $this->incentive;
	}

	public function setIncentive($value)
	{
		$this->incentive = $value;
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
