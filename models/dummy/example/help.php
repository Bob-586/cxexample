<?php 

 /**
 * @copyright (c) 2014
 * @author Chris Allen, Robert Strutts
 */

namespace cx\model;
use cx\database\model as the_model;

class help extends the_model {

  protected $table = 'help';
  protected $key = 'id';
  
  public function __construct($options = array()) {
    $options['table'] = $this->table;
    $options['key'] = $this->key;
    parent::__construct($options);
  }
  
  public function caching() {
    return false;
  }
  
  public function get_row_example($id) {
    $where = "`{$this->key}` = :key";
    $bind = array(':key'=>$id);
    $options['fetch'] = 'fetch_row';
    $options['where'] = $where;
    // See: /cx/classes/cx/database/db.php
    // $this->database->insert($table, $info);
    // $this->database->update($table, $info, $where, $bind);  
    return $this->database->select($this->table, $options, $bind);
  }
  
  public function pdo_example() {
    $sql = "SELECT `id`,`section`,`enabled` FROM `categories` ORDER BY `section` ASC";
    $pdostmt = $this->database->prepare($sql);
    if($pdostmt->execute() !== false) {
      return $pdostmt->fetchAll(PDO_ASSCO);
    }
  }

} // end of model class
