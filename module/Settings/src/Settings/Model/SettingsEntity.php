<?php
namespace Settings\Model;

class SettingsEntity
{
	protected $id;
	protected $name;
	protected $content;
	protected $modifiedDatetime;
	protected $modifiedUserId;
	
	public function __construct()
	{
		
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($value)
	{
		$this->id = $value;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($value)
	{
		$this->name = $value;
	}
	
	public function getContent()
	{
		return $this->content;
	}
	
	public function setContent($value)
	{
		$this->content = $value;
	}
	
	public function getModifiedDatetime()
	{
		return $this->modifiedDatetime;
	}
	
	public function setModifiedDatetime($value)
	{
		$this->modifiedDatetime = $value;
	}
	
	public function getModifiedUserId()
	{
		return $this->modifiedUserId;
	}
	
	public function setModifiedUserId($value)
	{
		$this->modifiedUserId = $value;
	}
}