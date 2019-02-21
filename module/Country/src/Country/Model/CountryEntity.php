<?php
namespace Country\Model;

class CountryEntity
{
	protected $id;
	protected $country_code;
	protected $country_name;

	public function getId()
	{
		return $this->id;
	}

	public function setId($value)
	{
		$this->id = $value;
	}

	public function getCountryCode()
	{
		return $this->country_code;
	}

	public function setCountryCode($value)
	{
		$this->country_code = $value;
	}
	
	public function getCountryName()
	{
		return $this->country_name;
	}
	
	public function setCountryName($value)
	{
		$this->country_name = $value;
	}
}