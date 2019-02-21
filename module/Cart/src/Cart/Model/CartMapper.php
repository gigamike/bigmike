<?php
namespace Cart\Model;

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
use Cart\Model\CartEntity;

class CartMapper
{
	protected $tableName = 'cart';
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

		if(isset($filter['product_id'])){
		    $where->equalTo("product_id", $filter['product_id']);
		}

		if(isset($filter['quantity'])){
		    $where->equalTo("quantity", $filter['quantity']);
		}

		if (!empty($where)) {
			$select->where($where);
		}

		if(count($order) > 0){
		    $select->order($order);
		}

		// echo $select->getSqlString($this->dbAdapter->getPlatform());exit();

		if($paginated) {
		    $entityPrototype = new CartEntity();
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

		    $entityPrototype = new CartEntity();
		    $hydrator = new ClassMethods();
		    $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		    $resultset->initialize($results);
		}

		return $resultset;
	}

	public function save(CartEntity $cart)
	{
		$hydrator = new ClassMethods();
		$data = $hydrator->extract($cart);

		if ($cart->getId()) {
			// update action
			$action = $this->sql->update();
			$action->set($data);
			$action->where(array('id' => $cart->getId()));
		} else {
			// insert action
			$action = $this->sql->insert();
			unset($data['id']);
			$action->values($data);
		}
		$statement = $this->sql->prepareStatementForSqlObject($action);
		$result = $statement->execute();

		if (!$cart->getId()) {
			$cart->setId($result->getGeneratedValue());
		}
		return $result;
	}

	public function getCart($id)
	{
		$select = $this->sql->select();
		$select->where(array('id' => $id));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$result = $statement->execute()->current();
		if (!$result) {
			return null;
		}

		$hydrator = new ClassMethods();
		$cart = new CartEntity();
		$hydrator->hydrate($result, $cart);

		return $cart;
	}

	public function delete($id)
	{
    $delete = $this->sql->delete();
    $delete->where(array('id' => $id));

    $statement = $this->sql->prepareStatementForSqlObject($delete);
    return $statement->execute();
	}

	public function deleteByCreatedUserId($created_user_id)
	{
    $delete = $this->sql->delete();
    $delete->where(array('created_user_id' => $created_user_id));

    $statement = $this->sql->prepareStatementForSqlObject($delete);
    return $statement->execute();
	}

	public function getCarts($paginated=false, $filter = array(), $order=array())
	{
    $select = $this->sql->select();
		$select->columns(array(
				'id',
				'product_id',
				'quantity',
				'created_datetime',
				'created_user_id',
		));
		$select->join(
      'product',
      $this->tableName . ".product_id = product.id",
      array(
				'brand_id',
				'name',
				'description',
				'photo_name1',
				'photo_name2',
				'photo_name3',
				'panorama',
				'price',
				'discount_type',
				'discount',
			),
      $select::JOIN_INNER
    );
		$select->join(
      'brand',
      "product.brand_id = brand.id",
      array(
				'brand',
			),
      $select::JOIN_LEFT
    );

    $where = new \Zend\Db\Sql\Where();

		if(isset($filter['product_id'])){
			$where->equalTo($this->tableName . ".product_id", $filter['product_id']);
		}

		if(isset($filter['created_user_id'])){
			$where->equalTo($this->tableName . ".created_user_id", $filter['created_user_id']);
		}

    if (!empty($where)) {
        $select->where($where);
    }

    if(count($order) > 0){
			if($order[0] == 'RAND()'){
				$select->order(new \Zend\Db\Sql\Expression("RAND()"));
			}else{
				$select->order($order);
			}
    }

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
