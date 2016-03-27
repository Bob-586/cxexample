<?php

function format_rights($rights) {
  $rights = (cx\app\main_functions::is_serialized($rights) === true) ? cx\app\main_functions::safe_unserialize($rights) : $rights;
  if (is_array($rights)) {
    $out = '';
    foreach($rights as $right) {
      $out .= $right . ", ";
    }
    return rtrim($out, ', ');
 }
 return $rights;
}

class cx_loader_app_users extends cx\app\app {

  public function __construct() {
    $copy = (defined('COPYRIGHT')) ? COPYRIGHT : '';
    $this->set_footer("&copy; Copyright 2014-" . date('Y') . ' ' . $copy);

    parent::__construct(); // Must load app constructor
  }

  public function index() {
    $this->auth(array('user'=>'login_check'));
    $this->datatables_code();
    
    $err_content = ob_get_clean();
    $js_view = new \cx\app\javascript_view('app'.DS.'users'.DS.'list_users'); // list_users.jsp
    $js_view->set('q', \cx\app\main_functions::get_globals(array('route','m')));
    $js = $js_view->fetch($this);
    
    $view = new \cx\app\view('app'. DS .'users'. DS .'index'); 
    $view->content .= $err_content;
    $this->add_to_javascript($js);
    $view->set_template('page');
    $view->fetch($this);
  }

  public function ajax_ssp_users_list() {
    $this->load_model();
    $db_options = array('table'=>'`users`', 'key'=>'`id`');
    $test = new cx\database\model($db_options);
    
    $columns = array(
      array( 'db' => "{$db_options['table']}.`id`", 'dt' => 0 ),
      array( 'db' => "{$db_options['table']}.`fname`", 
             'dt' => 1, 
             'textsize' => 30,
             'hyper' => $this->get_url('/app/users', 'edit_user', "id="), 
             'id' => "{$db_options['table']}.`{$db_options['key']}`",
      ),
      array( 'db' => "{$db_options['table']}.`lname`", 
             'dt' => 2, 
             'textsize' => 30,
             'hyper' => $this->get_url('/app/users', 'edit_user', "id="), 
             'id' => "{$db_options['table']}.`{$db_options['key']}`",
      ),
      array( 'db' => "{$db_options['table']}.`username`", 
             'dt' => 3, 
             'textsize' => 30,
             'hyper' => $this->get_url('/app/users', 'edit_user', "id="), 
             'id' => "{$db_options['table']}.`{$db_options['key']}`",
      ),               
      array( 'db' => "{$db_options['table']}.`rights`", 
             'dt' => 4,
             'fn_results' => 'format_rights',
             'hyper' => $this->get_url('/app/users', 'edit_user', "id="), 
             'id' => "{$db_options['table']}.`{$db_options['key']}`",
      ),

    );	
  
    $is_admin = $this->auth(array('user' => 'is_admin'));         
    $id = $this->session->get_int(cx_configure::a_get('cx','login') . 'id');
    
    $options['where'] = ($is_admin === true) ? " 1=1" : " id={$id}";
    $test->ssp_load($columns, $options);
  }

  
  public function edit_user() {
    $id = cx\app\static_request::init('get', 'id');

    if ($id->is_not_set()) {
      echo "Invalid id!";
      exit;
    }

    if ($id->to_int() !== $this->session->get_int(cx_configure::a_get('cx','login') . 'id')) {
      $this->auth(array('user'=>'admin_check'));
      $lock_rights_controls = false; // Admin
    } elseif ($this->auth(array('user'=>'is_admin')) === true) {
      $lock_rights_controls = false; // Admin can modify self, as they can create any user...
    } else {
      $lock_rights_controls = true; // User must not be able to grant self more rights!
    }

    $this->load_model();
    $db_options = array('table' => 'users', 'key' => 'id');
    $edit_user = new cx\database\model($db_options);

    if ($id->is_not_valid_id()) {
      // no existing data
      $model = array();
      $model['new'] = true;
    } else {
      $edit_user->load($id->to_int());
      $model = $edit_user->get_members();
      if ($model == array()) {
        echo "Invalid id!";
        exit;
      }
      $s_pwd = $model['password']; // Save Pwd
      unset($model['password']); // Remove scrambled DB password, so user does not see it!
      $model['new'] = false;
    }

    $model['lock_rights_controls'] = $lock_rights_controls;
    $model['rights_statuses'] = array('admin' => 'Administrator', 'mgr' => 'Manager', 'sales'=>'Sales', 'office'=>'Office', 'cus' => 'Customer');

    if (cx\app\static_request::init('post', 'save')->is_set()) {
      $edit_user->auto_set_members(); // Set all post vars to DB

      $confirm = $this->request->post_var('confirm');
      $pwd = $this->request->post_var('password');

      if (cx\app\static_request::init('post', 'username')->is_empty() || cx\app\static_request::init('post', 'fname')->is_empty()  || cx\app\static_request::init('post', 'lname')->is_empty()) {
        cx\app\main_functions::set_message('First/Last name or username is missing.');
        $saveme = false;
      } elseif ($model['new'] === false && $this->request->is_empty($confirm) && $this->request->is_empty($pwd)) {
        $edit_user->set_member('password', $s_pwd); // Keep current password!
        $saveme = true;
      } elseif ($this->request->is_not_empty($confirm) && $pwd === $confirm && strlen($pwd) > 6) {
        $this->load_model('users' . DS . 'users');
        $db_options = array('api' => false);
        $users = new cx\model\users($db_options);
        $edit_user->set_member('password', $users->get_pwd_hash($pwd)); // Assign new pwd
        $saveme = true;
      } else {
        cx\app\main_functions::set_message('Password not strong/does not match.');
        $saveme = false;
      }

      if ($saveme === true) {
        $success = $edit_user->save();

        $id = $edit_user->get_member('id');
        if ($success === true && $id > 0) {
          cx_redirect_url($this->get_url('/app/users', 'edit_user', 'id=' . $id));
        }
      }
    }

    $frm = $this->load_class('cx\form\form', array('name' => 'edit_user', 'defaults' => array('readonly' => false)));
    $frm->grab_form('app'. DS .'users'. DS .'edit_user', $model);
    $frm->end_form();

    $this->add_js(PROJECT_BASE_REF . '/assets/pwd-meter.min.js');
    $this->add_css(PROJECT_BASE_REF . '/assets/login.css');

    $index = $this->get_url('app/users', 'index');
    $this->breadcrumb = array($index=>"List Users");
    $this->active_crumb = "Edit User";
    $view = new \cx\app\view();
    $view->content = $frm->get_html();
    $this->scripts .= $frm->get_js();
    $view->set_template('page');
    $view->fetch($this);
  }

}
