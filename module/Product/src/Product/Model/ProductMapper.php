<?php
namespace Product\Model;

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
use Product\Model\ProductEntity;

class ProductMapper
{
	protected $tableName = 'product';
	protected $dbAdapter;
	protected $sql;

	public function __construct(Adapter $dbAdapter)
	{
		$this->dbAdapter = $dbAdapter;
		$this->sql = new Sql($dbAdapter);
		$this->sql->setTable($this->tableName);
	}

	public function fetch($paginated=false, $filter = array(), $order=array(), $limit = null)
	{
		$select = $this->sql->select();
		$where = new \Zend\Db\Sql\Where();

		if(isset($filter['id'])){
			$where->equalTo("id", $filter['id']);
		}

		if(isset($filter['name'])){
	    $where->equalTo("name", $filter['name']);
		}

		if(isset($filter['name_keyword'])){
			$where->addPredicate(
				new \Zend\Db\Sql\Predicate\Like("name", "%" . $filter['name_keyword'] . "%")
			);
		}

		if (!empty($where)) {
			$select->where($where);
		}

		if(count($order) > 0){
	    $select->order($order);
		}

		if(!is_null($limit)){
	    $select->limit($limit);
		}

		// echo $select->getSqlString($this->dbAdapter->getPlatform());exit();

		if($paginated) {
		    $entityPrototype = new ProductEntity();
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

		    $entityPrototype = new ProductEntity();
		    $hydrator = new ClassMethods();
		    $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		    $resultset->initialize($results);
		}

		return $resultset;
	}

	public function save(ProductEntity $product)
	{
		$hydrator = new ClassMethods();
		$data = $hydrator->extract($product);

		if ($product->getId()) {
			// update action
			$action = $this->sql->update();
			$action->set($data);
			$action->where(array('id' => $product->getId()));
		} else {
			// insert action
			$action = $this->sql->insert();
			unset($data['id']);
			$action->values($data);
		}
		$statement = $this->sql->prepareStatementForSqlObject($action);
		$result = $statement->execute();

		if (!$product->getId()) {
			$product->setId($result->getGeneratedValue());
		}
		return $result;
	}

	public function getProduct($id)
	{
		$select = $this->sql->select();
		$select->where(array('id' => $id));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$result = $statement->execute()->current();
		if (!$result) {
			return null;
		}

		$hydrator = new ClassMethods();
		$product = new ProductEntity();
		$hydrator->hydrate($result, $product);

		return $product;
	}

	public function getProducts($paginated=false, $filter = array(), $order=array(), $limit = null)
	{
    $select = $this->sql->select();
		$select->columns(array(
				'id',
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
				'stock',
		));
		$select->join(
      'product_category',
      $this->tableName . ".id = product_category.product_id",
      array(),
      $select::JOIN_LEFT
    );
		$select->join(
        'category',
        "category.id = product_category.category_id",
        array(
					'categories' => new \Zend\Db\Sql\Expression("GROUP_CONCAT(category.category)"),
        ),
        $select::JOIN_LEFT
    );
		$select->join(
      'brand',
      $this->tableName . ".brand_id = brand.id",
      array(
				'brand',
			),
      $select::JOIN_LEFT
    );

    $where = new \Zend\Db\Sql\Where();

		if(isset($filter['id_not_equal'])){
			$where->notEqualTo($this->tableName . ".id", $filter['id_not_equal']);
		}

		if(isset($filter['name'])){
		    $where->equalTo($this->tableName . ".name", $filter['name']);
		}

		if(isset($filter['keyword'])){
			$filter['keyword'] = urldecode($filter['keyword']);
			$where->addPredicate(
					new \Zend\Db\Sql\Predicate\Like($this->tableName . ".name", "%" . $filter['keyword'] . "%")
			);
		}

		if(isset($filter['category_id'])){
			$where->equalTo("product_category.category_id", $filter['category_id']);
		}
		if(isset($filter['search_category_id'])){
			$where->equalTo("product_category.category_id", $filter['search_category_id']);
		}

		if(isset($filter['category_ids']) && is_array($filter['category_ids']) && count($filter['category_ids']) > 0){
	    $where->in("product_category.category_id", $filter['category_ids']);
		}

		if(isset($filter['brand_id'])){
			$where->equalTo($this->tableName . ".brand_id", $filter['brand_id']);
		}
		if(isset($filter['search_brand_id'])){
			$where->equalTo($this->tableName . ".brand_id", $filter['search_brand_id']);
		}

		if(isset($filter['keyword'])){
			$where->addPredicate(
					new \Zend\Db\Sql\Predicate\Like("name", "%" . $filter['keyword'] . "%")
			);
		}

    if (!empty($where)) {
        $select->where($where);
    }

		$select->group($this->tableName . ".id");

    if(count($order) > 0){
			if($order[0] == 'RAND()'){
				$select->order(new \Zend\Db\Sql\Expression("RAND()"));
			}else{
				$select->order($order);
			}
    }

		if(!is_null($limit)){
	    $select->limit($limit);
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

	public function delete($id)
	{
	    $delete = $this->sql->delete();
	    $delete->where(array('id' => $id));

	    $statement = $this->sql->prepareStatementForSqlObject($delete);
	    return $statement->execute();
	}
}
