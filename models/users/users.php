<?php
namespace cx\model;
use cx\database\model as the_model;
use cx\common\crypt as crypt;

class users extends the_model {

  protected $table = 'users';
  protected $key = 'id';
  private $api = true; // using a api call?
  private $now;
  private $max;
  private $retries;
  
  public function __construct($options) {
    $this->api = (isset($options['api'])) ? $options['api'] : true;
    $this->now = time();
    $throttling_seconds = (\cx_configure::a_get('security', 'throttling_login_seconds')) ? \cx_configure::a_get('security', 'throttling_login_seconds') : 15;
    $this->max = $this->now - $throttling_seconds;
    $this->retries = (\cx_configure::a_get('security', 'retries_allowed_before_throttling')) ? \cx_configure::a_get('security', 'retries_allowed_before_throttling') : 3;
    parent::__construct(array('table' => $this->table, 'key' => $this->key));
  }
 
  public function is_user($username, $password) {
    if ($this->api === false) {
      $c_ary = $this->get_cookie();
      if ($c_ary !== false) {
        if ($this->check_cookie($c_ary) === true) {
          return true;
        }
      }
    }

    if (! empty($username)) {
      
      $this->set_primary_key('username');
      $this->load($username);

      // If less than xx seconds since last failure, deny user
      if ($this->get_member('failures') > $this->retries) {
        if ($this->get_member('last_failure') > $this->max) {
          return false;
        }
      }
      
      if ($this->get_member($this->key) > 0 && $this->check_pwd($password, $this->get_member('password'))) {
        $this->login_success($this->get_members());
        return true;
      }
    }
    
    // If username, log bad attempts
    $id = $this->get_member($this->key);
    if ($id > 0) {
      $failures = ($this->get_member('failures') < 200) ? $this->get_member('failures') : 200; // Only track 200...as DB limit is 255!
      $failures++;
      $sql = "UPDATE `{$this->table}` SET `failures`=:failures, `last_failure`=:last_failure WHERE `{$this->key}`=:id LIMIT 1";
      $pdostmt = $this->database->prepare($sql);
      $pdostmt->bindValue(':failures', $failures, \PDO::PARAM_INT);
      $pdostmt->bindValue(':last_failure', $this->now, \PDO::PARAM_INT);
      $pdostmt->bindValue(':id', $id, \PDO::PARAM_INT);
      $pdostmt->execute();      
    }     
    
    return false;
  }

  public function get_username_from_cookie() {
    $c_ary = $this->get_cookie();
    if ($c_ary === false) {
      return '';
    }
    $c_aid = (isset($c_ary['x'])) ? $c_ary['x'] : ''; //cookie "array" for id

    if (! empty($c_aid)) {
      $this->set_primary_key('identifier');
      $this->load($c_aid);
      return $this->get_member('username');
    }
    return '';
  }

  private function get_cookie() {
    // Check to see if they have a cookie for access
    return $this->request->cookie_var('id');
  }

  private function check_cookie($c_ary) {
    $c_aid = $c_ary['x']; //cookie "array" for identifier
    $c_apwd = $c_ary['a']; //cookie "array" for password

    if (!empty($c_aid)) {
      $this->set_primary_key('identifier');
      $this->load($c_aid);

      // If less than xx seconds since last failure, deny user
      if ($this->get_member('failures') > $this->retries) {
        if ($this->get_member('last_failure') > $this->max) {
          return false;
        }
      }
      
      $username = $this->request->request_var('username');
      // If another user is trying to login, let them...by ignoring the cookie data.
      if ($this->request->is_empty($username) || $username == $this->get_member('username')) {
        // Make sure cookie data is same as database, if so log them in.
        
        if ($this->get_member($this->key) > 0 && crypt::make_hash($this->get_member('password')) == $c_apwd && crypt::make_hash($this->get_member('username')) == $c_aid) {
          $this->login_success($this->get_members());
          return true;
        }
      }
    }
    return false;
  }

  private function set_cookie($c_id, $c_pwd, $username) {
    if ($this->request->request_var('login') == 'rememberme') {
      $c_usr = crypt::make_hash($username);
      $c_pwd = crypt::make_hash($c_pwd);
      $c_a = array('x' => $c_usr, 'a' => $c_pwd);
      $this->request->set_cookie_var('id', $c_a, 14, "days");
      
      $sql = "UPDATE `{$this->table}` SET `identifier`=:identifier WHERE `{$this->key}`=:key LIMIT 1";
      $pdostmt = $this->database->prepare($sql);
      $pdostmt->bindValue(':identifier', $c_usr, \PDO::PARAM_STR);
      $pdostmt->bindValue(':key', $c_id, \PDO::PARAM_INT);
      $pdostmt->execute();
    }
  }

  private function login_success($a) {
    $this->set_cookie($a['id'], $a['password'], $a['username']);
    
    // For Security, regenerate the session!!
    session_regenerate_id();
    
    // store all login data in session
    foreach($a as $key=>$value) {
      if ($key == 'password') {
        continue; // do not store passwords!!
      }
      $this->session->set_session_var(\cx_configure::a_get('cx','login') . $key, $value);
    }
    
    if ($a['failures'] > 0) {
      $sql = "UPDATE `{$this->table}` SET `failures`='0' WHERE `{$this->key}`=:key LIMIT 1";
      $pdostmt = $this->database->prepare($sql);
      $pdostmt->bindValue(':key', $a['id'], \PDO::PARAM_INT);
      $pdostmt->execute();
    }
  }
  
  public function add_user($name, $username, $password, $user_type) {
    // Check if they are already in the system...
    $this->set_primary_key('username');
    $this->load($username);
    if ($this->get_member($this->key) > 0) {
      return false; // Already in the system!
    }
    
    $this->set_primary_key($this->key);
    $this->empty_data();
    
    $name = explode($name, " ");

    $this->set_member('fname', $name[0]);
    $this->set_member('lname', $name[1]);
    $this->set_member('username', $username);
    $this->set_member('password', $this->get_pwd_hash($password));
    $this->set_member('rights', $user_type);
    
    $success = $this->save();
    if ($success) {
      $this->login_success($this->get_members());
      return true;
    }
    return false;
  }
 
}