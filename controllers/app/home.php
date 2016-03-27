<?php

class cx_loader_app_home extends cx\app\app {
    
  public function __construct() {
    $copy = (defined('COPYRIGHT')) ? COPYRIGHT : '';
    $this->set_footer("&copy; Copyright 2014-" . date('Y') . ' ' . $copy);
    parent::__construct(); // Must load app constructor
  }
  
  /**
   * Purpose: To block access to core files and LOG ACCESS ATTEMPTS
   */
  public function deny() {
    $IP = cx\app\main_functions::get_real_ip_address();
    $log = "Folder: " . $this->request->get_var('f') . ", ";
    if (strstr($IP, ', ')) {
      $ips = explode(', ', $IP);
      $i = 0;
      foreach ($ips as $ip_address) {
        $i++;
        $host = cx\app\main_functions::get_host($ip_address);
        $log .= "IP Address[{$i}]: {$ip_address}, HOST: {$host} \r\n";
      }
    } else {
      $host = cx\app\main_functions::get_host($IP);
      $log .= "IP Address: {$IP}, HOST: {$host} \r\n";
    }
    $log .= str_repeat("-=", 10);

    $filename = "denied_access.log.txt";
    $logger = $this->load_class('cx\common\log', $filename);
    $logger->write($log);
    unset($logger); // save now

    cx\app\main_functions::set_message('Attempt to access core files has been logged with your IP!', 'danger');
    $this->error404($ignore = true);
  }

  public function index() {     
    $view = new \cx\app\view('app' . DS . 'home' . DS . 'index');  
    $this->breadcrumb = array("javascript:;" => "Home");
    $this->active_crumb = "Index";
//    $this->set_title_and_header('Hello,');
    $view->set_template('page');

    $last_modified_time = filemtime(PROJECT_BASE_DIR . 'views'. DS . 'app' . DS . 'home' . DS . 'index.php');
    $view->set_page_cache($last_modified_time);
    
    $view->fetch($this);
  }

  public function main() {
    $view = new \cx\app\view('app' . DS . 'main');
    $this->set_title_and_header('Main Page');
    $index = $this->get_url('app/home', 'index');
    $this->breadcrumb = array($index => "Index");
    $this->active_crumb = "Main";

    $id = $this->session->get_int(cx_configure::a_get('cx','login') . 'id');
    /**
     * @todo add api check / auth
     */
    if ($this->request->is_not_valid_id($id)) {
      cx_redirect_url($this->get_url('/app/' . DEFAULT_PROJECT, 'login'));
    }

    $view->set('fname', $this->session->session_var(cx_configure::a_get('cx','login') . 'fname'));
    $view->set('lname', $this->session->session_var(cx_configure::a_get('cx','login') . 'lname'));
    $rights = $this->session->session_var(cx_configure::a_get('cx','login') . 'rights');
    $view->set('rights', (cx\app\main_functions::is_serialized($rights) === true) ? cx\app\main_functions::safe_unserialize($rights) : $rights);
    $view->set_template('page');
    $view->fetch($this);
  }

  public function build_template() {
    $view = new \cx\app\view();  
//    $this->auth(array('user' => 'admin_check'));

    $folder_name = $this->request->request_var('folder_name');
    $file_name = $this->request->request_var('file_name');
    $web_page_name = $this->request->request_var('web_page_name');  

    $perms = $this->request->request_var('rights');
    $make = $this->request->request_var('make');
    
    if ($this->request->is_not_empty($folder_name) && $this->request->is_not_empty($file_name)) {
      $this->load_model('auto_template_generator');
      $form_gen = new cx\model\auto_template_generator($folder_name, $file_name, $web_page_name,  $perms, $make);
      $ret = "<style>code { \r\n color: blue !important; \r\n } \r\n </style>\r\n";
      $ret .= $form_gen->generator();
      $view->content = $ret;
    } else {
      $model['rights_statuses'] = array('admin' => 'Administrator', 'mgr' => 'Manager', 'sales'=>'Sales', 'office'=>'Office', 'cus' => 'Customer');
      $frm = $this->load_class('cx\form\form', array('name' => 'templates', 'defaults' => array('readonly' => false)));
      $frm->grab_form('auto_template_generator', $model);
      $frm->end_form();
      $view->content = $frm->get_html();
    }
    $view->set_template('page');
    $view->fetch($this);
  }

  public function make_form() {
    $this->auth(array('user' => 'admin_check'));

    if ($this->request->get_var('table') !== false) {
      $this->load_model('auto_form_generator');
      $form_gen = new cx\model\auto_form_generator($this->request->get_var('table'));
      $form_gen->generator();
    } else {
      echo 'Sorry, please enter MySQL [table] name varible!';
    }
  }

  public function login() {    
    $this->load_model('users' . DS . 'users');
    $db_options = array('api' => false);
    $users = new cx\model\users($db_options);

    $cc_name = $users->get_username_from_cookie();
    $username = ($this->request->is_not_empty($this->request->request_var('username'))) ? $this->request->request_var('username') : $cc_name;
    $password = $this->request->request_var('password');

    if ($this->request->is_not_empty($username) && $this->request->is_not_empty($password)) {
      $success = $users->is_user($username, $password);

      if ($success == true) {
        cx_redirect_url($this->get_url('/app/' . DEFAULT_PROJECT, 'main'));
      } else {
        cx_set_message('Invalid Username or Password!');
      }
    }
    $model['pwd'] = (!empty($cc_name)) ? "**********" : '';
    $model['username'] = $username;
    $frm = $this->load_class('cx\form\form', array('name' => 'login', 'defaults' => array('readonly' => false)));
    $frm->grab_form('app/user_login', $model);
    $frm->end_form();

    $view = new \cx\app\view();
    $view->content = $frm->get_html();
    $this->scripts .= $frm->get_js();
    $view->set_template('page');
    $view->fetch($this);
   
  }

  public function logout() {
    // Unset all of the session variables.
    $_SESSION = array();

    // Ensure remember me is deleted.
    setcookie(\cx_configure::a_get('cx', 'session_variable') . 'id', '', time() - 42000);

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
      );
    }

    // Finally, destroy the session.
    session_destroy();
    header('Location: ' . $this->get_url('/app/' . DEFAULT_PROJECT, 'index'));
  }

  public function error() {
    $view = new \cx\app\view();
    $view->set_template('error');
    $view->fetch($this);
  }

  public function debug() {
    $this->auth(array('user' => 'admin_check'));
    
    $view = new \cx\app\view();
    $view->set_template('page');
    
    $view->content .= "<h4>Service Logs</h4>";
    
    if (is_readable(CX_LOGS_DIR . 'slow.txt')) {
      $view->content .= "Speed Log<br><pre>";
      $view->content .= file_get_contents(CX_LOGS_DIR . 'slow.txt');
      $view->content .= "</pre><br>";
    }
    
    if (is_readable(CX_LOGS_DIR . 'Errors.log.txt')) {
      $view->content .= "Error Log<br><pre>";
      $view->content .= file_get_contents(CX_LOGS_DIR . 'Errors.log.txt');
      $view->content .= "</pre><br>";
    }
    
    if (is_readable(CX_LOGS_DIR . 'denied_access.log.txt')) {
      $view->content .= "Access Violations<br><pre>";
      $view->content .= file_get_contents(CX_LOGS_DIR . 'denied_access.log.txt');
      $view->content .= "</pre><br>";
    }
    
    $view->fetch($this);
  }
  
}
