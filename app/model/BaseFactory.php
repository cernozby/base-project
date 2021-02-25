<?php
namespace App\model;

use http\Exception\BadQueryStringException;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\Database\Row;
use Nette\Database\Table\Selection;
use Nette\DI\Container;
use PDOException;

Class BaseFactory extends BaseModel {
  
  /**
   * @var
   */
  public $data;
  /**
   * @var
   */
  public $id;
  /**
   * @var
   */
  public $table;
  /**
   * @var string
   */
  public $primary;
  
  
  /**
   * BaseFactory constructor.
   * @param Explorer $database
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    parent::__construct($database, $container, $linkGenerator);
    $this->primary = $this->getPrimaryName();
  }
  
  
  /**
   * 
   * @param int $id
   * @return $this
   */
  public function initId(int $id) :?object {
    if ($this->data && $this->getId() === $id) {
      return $this;
    }
    
    $this->id = $id;
    $this->load();

    return $this->data ? $this : null;
  }
  
  /**
   * @return int
   */
  public function getId() : int {
    return $this->id;
  }

  /**
   * 
   * @param array | Row $data
   * @return $this
   */
  public function initData($data) : ?object {
    if (!$data) {
      return null;
    }

    $this->data = $data;
    $primary = $this->primary;
    if ($data instanceof Row) {
      $data = get_object_vars($data);
    } else {
      $data = $data->toArray();
    }
    $this->id = $data[$primary];
    return $this;
  }
  
  /**
   * @return array|null
   */
  public function getData() : ?array {
    return $this->data;
  }
  
  /**
   *
   */
  public function load() : void {
    $this->data = $this->getTable()->select('*')->where($this->primary . ' = ?', $this->getId())->fetch();
  }
  
  /**
   * @param $col
   * @return |null
   */
  public function get($col) {
      return $this->data->$col ?? null;
  }
  
  /**
   * @param array $array
   * @return array
   */
  public function getArray(array $array) : array {
    $result = array();
    foreach ($array as $a) {
      $result[$a] = $this->get($a);
    }
    return $result;
  }
  
  
  /**
   * @param $col
   * @param $value
   */
  public function set($col, $value) : void {
    try {
      $this->getTable()->where($this->primary . ' = ?', $this->getId())->update(array($col => $value));
      $this->load();
    } catch (PDOException $e) {
      throw new \ModelException($e);
    }
  }
  
  
  /**
   * @param array $values
   */
  public function setArray(array $values) : void {
    try {
      $this->getTable()->where($this->primary . ' = ?', $this->getId())->update($values);
      $this->load();
    } catch (PDOException $e) {
      throw new BadQueryStringException($e);
    }
  }
  
  
  /**
   * @param string|null $name
   */
  public function getTable(string $name = null) : Selection  {
    if ($name) {
      return $this->db->table($name);
    }
    return $this->db->table($this->getTableName());
  }

  /**
   * Get name of object table
   * @return string
   */
  public function getTableName() : string {
    return $this->table;
  }
  
  /**
   * @param array $array
   * @return array
   */
  public function arrayToObject(array $array) : array {
    $objects = array();
    foreach ($array as $a) {
      $instance = $this->container->createInstance(get_class($this));
      $objects[] = $instance->initData($a);
    }
    return $objects;
  }

  /**
   * Get primary name of table
   */
  private function getPrimaryName() : string {
    return 'id_' . $this->table;
  }
  
  /**
   *
   */
  public function delete() : void {
    $this->getTable()
            ->where($this->primary . ' = ?', $this->getId())
            ->delete();
  }
  
  
  /**
   * @param $data
   * @param false $lastMod
   */
  public function update($data, $lastMod = false) : void {
    
    if ($lastMod) {
      $data['last_mod'] = new Nette\Utils\DateTime();
    }
    $this->getTable()
            ->where($this->primary . ' = ?', $this->getId())
            ->update($data);
  }
  
  /**
   * @param $data
   * @return mixed
   */
  public function insert(array $data) {
    $row = $this->getTable()->insert($data);
    return $row->getPrimary();
  }
  
  
  /**
   * @return array|null
   */
  public function getAll() :? array {
    return $this->getTable()->select('*')->fetchAll();
  }
  
  /**
   * @param string $column
   * @return array|null
   */
  public function getAllFromOneColumn(string $column) : array {
    return $this->getTable()->select($column)->fetchAll() ? : [];
  }
  
  /**
   * @param array $columns
   * @return array|null
   */
  public function getAllColumns(array $columns) :? array {
        return $this->getTable()->select($columns)->fetchAll();
  }
  
  public function existInColumn(string $column, string $value) : bool {
    return $this->db->table($this->table)->where($column.' = ?', $value)->fetch() ? true : false;
  }
}