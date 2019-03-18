<?php
namespace Incentive\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Incentive\Model\IncentiveEntity;

class IncentiveMapper
{
	protected $tableName = 'incentive';
	protected $dbAdapter;
	protected $sql;

	public function __construct(Adapter $dbAdapter)
	{
		$this->dbAdapter = $dbAdapter;
		$this->sql = new Sql($dbAdapter);
		$this->sql->setTable($this->tableName);
	}

	public function fetch($paginated=false, $filter = array(), $order=array())
	{
		$select = $this->sql->select();
		$where = new \Zend\Db\Sql\Where();

		if(isset($filter['id'])){
			$where->equalTo("id", $filter['id']);
		}

		if(isset($filter['created_user_id'])){
		    $where->equalTo("created_user_id", $filter['created_user_id']);
		}

		if(isset($filter['month']) && !empty($filter['month'])){
	    $where->addPredicate(
	      new \Zend\Db\Sql\Predicate\Expression("MONTH(created_datetime) = '" . $filter['month'] . "'")
	    );
		}

		if(isset($filter['year']) && !empty($filter['year'])){
		  $where->addPredicate(
		    new \Zend\Db\Sql\Predicate\Expression("YEAR(created_datetime) = '" . $filter['year'] . "'")
		  );
		}

		if (!empty($where)) {
			$select->where($where);
		}

		if(count($order) > 0){
		    $select->order($order);
		}

		// echo $select->getSqlString($this->dbAdapter->getPlatform());exit();

		if($paginated) {
		    $entityPrototype = new IncentiveEntity();
		    $hydrator = new ClassMethods();
		    $resultset = new HydratingResultSet($hydrator, $entityPrototype);

			$paginatorAdapter = new DbSelect(
				$select,
				$this->dbAdapter,
				$resultset
			);
			$paginator = new Paginator($paginatorAdapter);
			return $paginator;
		}else{
	    $statement = $this->sql->prepareStatementForSqlObject($select);
	    $results = $statement->execute();

	    $entityPrototype = new IncentiveEntity();
	    $hydrator = new ClassMethods();
	    $resultset = new HydratingResultSet($hydrator, $entityPrototype);
	    $resultset->initialize($results);
		}

		return $resultset;
	}

	public function save(IncentiveEntity $incentive)
	{
		$hydrator = new ClassMethods();
		$data = $hydrator->extract($incentive);

		if ($incentive->getId()) {
			// update action
			$action = $this->sql->update();
			$action->set($data);
			$action->where(array('id' => $incentive->getId()));
		} else {
			// insert action
			$action = $this->sql->insert();
			unset($data['id']);
			$action->values($data);
		}
		$statement = $this->sql->prepareStatementForSqlObject($action);
		$result = $statement->execute();

		if (!$incentive->getId()) {
			$incentive->setId($result->getGeneratedValue());
		}
		return $result;
	}

	public function getIncentive($id)
	{
		$select = $this->sql->select();
		$select->where(array('id' => $id));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$result = $statement->execute()->current();
		if (!$result) {
			return null;
		}

		$hydrator = new ClassMethods();
		$incentive = new IncentiveEntity();
		$hydrator->hydrate($result, $incentive);

		return $incentive;
	}

	public function delete($id)
	{
    $delete = $this->sql->delete();
    $delete->where(array('id' => $id));

    $statement = $this->sql->prepareStatementForSqlObject($delete);
    return $statement->execute();
	}

	public function getIncentives($paginated=false, $filter = array(), $order=array())
	{
    $select = $this->sql->select();
		$select->columns(array(
			'height_centimeters',
			'weight_kilograms',
			'bmi',
			'bmi_category',
			'incentive',
      'month' => new \Zend\Db\Sql\Expression("MONTH(created_datetime)"),
      'year' => new \Zend\Db\Sql\Expression("YEAR(created_datetime)"),
		));

    $where = new \Zend\Db\Sql\Where();

		if(isset($filter['created_user_id'])){
			$where->equalTo("created_user_id", $filter['created_user_id']);
		}

		if(isset($filter['month']) && !empty($filter['month'])){
	    $where->addPredicate(
	      new \Zend\Db\Sql\Predicate\Expression("MONTH(created_datetime) = '" . $filter['month'] . "'")
	    );
		}

		if(isset($filter['year']) && !empty($filter['year'])){
		  $where->addPredicate(
		    new \Zend\Db\Sql\Predicate\Expression("YEAR(created_datetime) = '" . $filter['year'] . "'")
		  );
		}

    if (!empty($where)) {
      $select->where($where);
    }

    if(count($order) > 0){
			$select->order($order);
    }

		$select->group(array("year", "month"));

    // echo $select->getSqlString($this->dbAdapter->getPlatform()) . "<br>"; exit();

    if($paginated) {
      $paginatorAdapter = new DbSelect(
        $select,
        $this->dbAdapter
      );
      $paginator = new Paginator($paginatorAdapter);
      return $paginator;
    }else{
      $statement = $this->sql->prepareStatementForSqlObject($select);
      $result = $statement->execute();
      if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet;
        $resultSet->initialize($result);
      }
    }

    return $resultSet;
	}
}
