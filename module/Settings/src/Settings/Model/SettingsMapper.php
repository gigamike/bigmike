<?php
namespace Settings\Model;

use Zend\Db\Adapter\Adapter;
use Settings\Model\SettingsEntity;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

class SettingsMapper
{
	protected $tableName = 'settings';
	protected $dbAdapter;
	protected $sql;

	public function __construct(Adapter $dbAdapter)
	{
		$this->dbAdapter = $dbAdapter;
		$this->sql = new Sql($dbAdapter);
		$this->sql->setTable($this->tableName);
	}

	public function fetchAll()
	{
		$select = $this->sql->select();

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();

		$entityPrototype = new SettingsEntity();
		$hydrator = new ClassMethods();
		$resultset = new HydratingResultSet($hydrator, $entityPrototype);
		$resultset->initialize($results);
		return $resultset;
	}

	public function save(SettingsEntity $settings)
	{
		$hydrator = new ClassMethods();
		$data = $hydrator->extract($settings);

		if ($settings->getId()) {
			// update action
			$action = $this->sql->update();
			$action->set($data);
			$action->where(array('id' => $settings->getId()));
		} else {
			// insert action
			$action = $this->sql->insert();
			unset($data['id']);
			$action->values($data);
		}
		$statement = $this->sql->prepareStatementForSqlObject($action);
		$result = $statement->execute();

		if (!$settings->getId()) {
			$settings->setId($result->getGeneratedValue());
		}
		return $result;
	}

	public function getSettings($id)
	{
		$select = $this->sql->select();
		$select->where(array('id' => $id));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$result = $statement->execute()->current();
		if (!$result) {
			return null;
		}

		$hydrator = new ClassMethods();
		$settings = new SettingsEntity();
		$hydrator->hydrate($result, $settings);

		return $settings;
	}

	public function getSettingsByName($name)
	{
		$select = $this->sql->select();
		$select->where(array('name' => $name));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$result = $statement->execute()->current();
		if (!$result) {
			return null;
		}

		$hydrator = new ClassMethods();
		$settings = new SettingsEntity();
		$hydrator->hydrate($result, $settings);

		return $settings;
	}

	public function getMysqlVersion()
	{
	    $sql = "SHOW VARIABLES LIKE 'version'";
	    $stmt = $this->dbAdapter->createStatement($sql);
	    $stmt->prepare();
	    $result = $stmt->execute();

	    if ($result instanceof ResultInterface && $result->isQueryResult()) {
	        $resultSet = new ResultSet;
	        $resultSet->initialize($result);

	        foreach ($resultSet as $row) {
	            return $row->Value;
	        }
	    }

	    return null;
	}
}
