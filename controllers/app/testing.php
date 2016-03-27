<?php 

/**
 * @copyright (c) 2015
 * @author Chris Allen, Robert Strutts 
 */

class cx_loader_app_testing extends cx\app\app {

  public function __construct() {
    
    if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== 'dev') {
      echo "Access Denied!";
      exit;
    }
    
    $copy = (defined('COPYRIGHT')) ? COPYRIGHT : '';
    $this->set_footer("&copy; Copyright 2014-" . date('Y') . ' ' . $copy);
    parent::__construct();
  }
  
  public function index() {
    $id = cx\app\static_request::init('get', 'id');
    if ($id->is_not_set()) {
      echo "Invalid id!";
      exit;
    }
   
    $this->load_model('app' . DS . 'testing');
    $db_options = array();
    $test = new cx\model\testing($db_options);
    
    if ($id->is_not_valid_id()) {
      // no existing data
      $model = array(); 
    } else {
      $test->load($id->to_int());
      $model = $test->get_members();      
    }      

    if (cx\app\static_request::init('request', 'save')->is_set()) {
      $test->auto_set_members();
      $success = $test->save();
 
      $id = $test->get_member('id');
      if ($success===true && $id > 0) {
        cx_redirect_url($this->get_url('/app/testing', 'index', 'id='.$id));
      }
    }    
    
    $this->set_title_and_header('Hello,');
    $this->registry->get('document')->set_keywords('testing');
    
    $frm = $this->load_class('cx\form\form', array('name' => 'product', 'defaults'=>array('readonly'=>false)));
    $frm->grab_form('test', $model);
    $frm->form('submit', 'save', array('id' => 'save', 
        'class'=>'btn btn-success', 'value' => 'save',
        'onclick'=>'return validatePage();'));
    $frm->end_form();
    $view = new \cx\app\view();
    $view->content = $frm->get_html();
    $view->set_template('page');
    $view->fetch($this);
  }
  
public function top() {
    $this->load_model('app' . DS . 'testing');
    $db_options = array();
    $test = new cx\model\testing($db_options);
    
    $options['limit'] = 50;
    
    $test->load("", $options);
    $allow_html = true;
    $rows = $test->get_members($allow_html);
    
    foreach($rows as $row) {
      echo "ID#{$row['id']} : {$row['data']} <br>";
    }
  }
  
  public function generic() {
    $this->load_model();
    $db_options = array('table'=>'test', 'key'=>'id');
    $test = new cx\database\model($db_options);
//    $options['where'] = " 1=2 ";
    $options['fields'] = "`test`.`id`, `test`.`data`";
    $options['paginator'] = 'true';
    $test->load("", $options);
    
    $view = new \cx\app\view('app/testing/generic');
    $allow_html = false;
    $view->set('rows', $test->get_members($allow_html));
    if ($test->get_paginator_object() === false) {
      $view->set('no_results', false);
      $view->set('paginator_items', "");    
      $view->set('paginator_links', "");
      $view->set('paginator_entries', "");
    } else {
      $view->set('no_results', ($test->get_paginator_object()->items_total > 0) ? false : true);
      $view->set('paginator_items', $test->get_paginator_object()->display_items_per_page());    
      $view->set('paginator_links', $test->get_paginator_links());
      $view->set('paginator_entries', $test->get_paginator_object()->get_entries());
    }   
    $view->set_template('page');
    $view->fetch($this);
  }
  
  public function ssp() {
    $this->datatables_code();
//    p();
//    $i = 1/0;
//    echo 'ooooooooooooooooooooooooooooo';
    $err_content = ob_get_clean();
    $js_view = new \cx\app\javascript_view('app/testing/ssp_test'); // ssp_test.jsp
    $js_view->set('q', \cx\app\main_functions::get_globals(array('route','m')));
    $js = $js_view->fetch($this);
    
    $view = new \cx\app\view('app/testing/ssp_test');
    $view->content .= $err_content;
    $this->add_to_javascript($js);
    $view->set_template('page');
    $view->fetch($this);
  }
  
  public function ajax_ssp() {
    $this->load_model();
    $db_options = array('table'=>'`test`', 'key'=>'`id`');
    $test = new cx\database\model($db_options);
    
    $columns = array(
      array( 'db' => "{$db_options['table']}.`id`", 'dt' => 0 ),
      array( 'db' => "{$db_options['table']}.`data`", 
             'dt' => 1, 
             'textsize' => 30,
             'hyper' => $this->get_url('/app/testing', 'echome', "id="), 
             'id' => "{$db_options['table']}.`{$db_options['key']}`",
             'fn' => 'get_data'
      ),
    );	
  
    $options['where'] = " 1=1";
    $test->ssp_load($columns, $options);
  }
  
  public function echome() {
    echo $this->request->get_var('id');
  }
 
  public function hash() {
    $enc = $this->load_class('cx\common\crypt');
    echo $enc->get_large_random_hash();
  }
  
  public function test_curl() {
    $curl = $this->load_class('cx\app\cx_curl');
    $curl->hostname = 'dev';
    $curl->port = 80;
    $curl->ssl = false;
    $curl->json_decode = true;
    cx_dump($curl->post(PROJECT_BASE_REF. '/api/app/testing/ajax_name', array('name'=>'bob', 'return'=>'json', 'debug'=>'true')));
  }
  
  public function ajax() {
    $view = new \cx\app\view('app/testing/ajax_name');
    $view->set_template('page');
    $view->fetch($this);
  }
  
  private function ajax_name_api(array $data) {
    if ($this->request->is_not_set($data['name'])) {
      \cx\app\cx_api::error(array('code'=>422, 'reason'=>'Name not set'));
    }

    if ($this->request->is_empty($data['name'])) {
      \cx\app\cx_api::error(array('code'=>422, 'reason'=>'Name is a required field'));
    }    
    
    \cx\app\cx_api::ok(array('code'=>200, 'name'=>$data['name']));
  }
  
  public function ajax_name() {    
    $this->auth(array('ajax'=>true, 'user'=>'is_logged_in'));
    $data['name'] = $this->request->post_var('name');
    
    if ($this->is_api()) {
      $this->ajax_name_api($data);
    } elseif (! $this->request->is_ajax()) {
      echo 'Error no ajax';
    } else {
      if ($this->request->is_empty($data['name'])) {
        echo "Name is a required field";
      } else {
        echo $data['name'];
      }
    }

  }
  
  public function get_pwd() {
    $this->load_model('users' . DS . 'users');
    $db_options = array();
    $users = new cx\model\users($db_options);
    echo $users->get_pwd_hash($this->request->get_var('pwd'));
  }
  
  public function p() {
    print_r($_SERVER);
  }
  
  public function checkvars() {
    $expected = array('year'=>'number', 'go');
    $this->request->set_allowed($expected, 'post');

    if (! $this->request->is_set($this->request->post_var('go'))) {
      ?>
    <form method="POST">
        <?= $this->request->do_csrf_token(); ?>
        
        <input type="text" name="year" />
        <input type="submit" name="go" value="test" />
    </form>
      <?php
      exit;
    }
        
    if ($this->request->verify_csrf_token()) {
      foreach($_POST as $k=>$p) {
        echo $this->request->encode_clean($k) . "=" . $this->request->encode_clean($p) . "<br />";
      }
    } else {
      echo "HACKER!";
    }
  }
  
  /**
   * Easy way to get/update disabled functions
   */
  public function kill_bad_fns() {
    // preg_replace :: is safe as of PHP 7!!
    $dis = "apache_child_terminate,apache_setenv,curl_multi_exec,define_syslog_variables,escapeshellarg,escapeshellcmd,eval,exec,fp,fput,ftp_connect,ftp_exec,ftp_get,ftp_login,ftp_nb_fput,ftp_put,ftp_raw,ftp_rawlist,highlight_file,ini_alter,ini_get_all,ini_restore,inject_code,mysql_pconnect,openlog,parse_ini_file,passthru,pcntl_alarm,pcntl_exec,pcntl_fork,pcntl_get_last_error,pcntl_getpriority,pcntl_setpriority,pcntl_signal,pcntl_signal_dispatch,pcntl_sigprocmask,pcntl_sigtimedwait,pcntl_sigwaitinfo,pcntl_strerror,pcntl_wait,pcntl_waitpid,pcntl_wexitstatus,pcntl_wifcontinued,pcntl_wifexited,pcntl_wifsignaled,pcntl_wifstopped,pcntl_wstopsig,pcntl_wtermsig,phpAds_XmlRpc,phpAds_remoteInfo,phpAds_xmlrpcDecode,phpAds_xmlrpcEncode,phpinfo,php_uname,popen,posix_getpwuid,posix_kill,posix_mkfifo,posix_setpgid,posix_setsid,posix_setuid,posix_uname,proc_close,proc_get_status,proc_nice,proc_open,proc_terminate,shell_exec,show_source,syslog,system,xmlrpc_entity_decode";
    $a = explode(",", $dis);
    $no_dups = array_unique($a);
    asort($no_dups);
    echo "disable_functions = ";
    $s = '';
    foreach($no_dups as $bad) {
      $s .= $bad . ",";
    }
    echo rtrim($s, ",");
  }
  
  public function fail() {
    $view = new \cx\app\view();
    $view->set_template('page');
    $this->set_errors(array('api'=>array('Login'=>'Failed to get user/pwd.')));
    $view->fetch($this);
  }  
  
  public function e() {   
    $enc = new \cx\common\crypt();
    $enc->set_use_openssl(false);
    $enc->change_security('high'); // low, medium, medium-high, and high
    $enc->set_cleartext('Hello, World');
    echo $enc->encrypt();
//    $enc->set_ciphertext('cCv3f2nGKRHL50BHFM26BE97oFbBe5rm');
    echo "<br>".$enc->decrypt() . '<br>';
//    echo $enc->generate_valid_key() . '<br>';
//    $enc->list_algoritms() . '<br>';
//    $enc->list_openssl_methods();
  }
  
  public function sleep() {
    sleep(5);
    $view = new \cx\app\view();
    $view->content = 'zzz';
    $view->set_template('page');
    $view->fetch($this);
  }
  
  public function sss() {
    echo (isset($_SESSION['junk'])) ? "GET: ". $_SESSION['junk'] : 'NONE';
    $_SESSION['junk'] = 'Wowy!!!';
    echo "<br> SET: " . $_SESSION['junk'] . "<br>";
  }
  
  public function tags() {
    echo "<div>";
  }
  
  public function check() {
    $this->load_model(); // Does Require db.php and model.php for Database to work!!
    $db_options = array('table'=>'users', 'key'=>'id');
    $users = new cx\database\model($db_options);
    $users->set_primary_key('username'); // Changes the field to load from key of 'id' to 'username'
    $username = 'bob_586';
    $password = 'FAKE_PWD';
    $users->load($username);
    if ($users->get_member($db_options['key']) > 0 && $users->check_pwd($password, $users->get_member('password'))) {
       echo 'Logged IN';
    } else {
       echo 'BAD PASSWORD or USERNAME';
    }    
  }
  
}