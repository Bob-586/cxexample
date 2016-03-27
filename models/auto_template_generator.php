<?php

/**
 * @copyright (c) 2015
 * @author Chris Allen, Robert Strutts
 */

namespace cx\model;
use cx\database\model as the_model;

class auto_template_generator extends the_model {

  private $folder_name;
  private $file_name;
  private $web_page_name;
  private $perms;
  private $make;

  public function __construct($folder, $file, $web_page_name, $perms, $make) {
    $this->folder_name = str_replace('..', '', preg_replace("/[^a-zA-Z0-9_]+/", "", $folder));
    $this->file_name = str_replace('..', '', preg_replace("/[^a-zA-Z0-9_]+/", "", $file));
    $this->web_page_name = str_replace('..', '', preg_replace("/[^a-zA-Z0-9_]+/", "", $web_page_name));
    $this->perms = $perms;
    $this->make = $make;
  }

  private function get_header() {
    return "
 /**
 * @copyright (c) 2014
 * @author Chris Allen, Robert Strutts
 */\r\n\r\n";
  }

  private function get_open_code() {
    return "<pre>\r\n<code>\r\n\r\n<textarea rows='16' cols='140' spellcheck='false'>\r\n";
  }

  private function get_close_code() {
    return "\r\n</textarea>\r\n</code>\r\n</pre>\r\n";
  }

  private function get_auth() {
    $s = "";
    $admin = false;
    $mgr = false;
    $sales = false;
    $office = false;
    $cus = false;
    
    if (count($this->perms) <2) {
      return '';
    }
    
    foreach($this->perms as $junk=>$perm) {
      if ($perm == 'admin') {
        $s .= "\r\n    \$model['cx_admin'] = \$this->auth(array('user' => 'is_admin'));\r\n";
        $admin = true;
      }
      if ($perm == 'mgr') {
        $s .= "\r\n    \$model['cx_mgr'] = \$this->auth(array('user' => 'is_manager'));\r\n";
        $mgr = true;
      }
      if ($perm == 'sales') {
        $s .= "\r\n    \$model['cx_sales'] = \$this->auth(array('user' => 'is_sales'));\r\n";
        $sales = true;
      }
      if ($perm == 'office') {
        $s .= "\r\n    \$model['cx_office'] = \$this->auth(array('user' => 'is_office'));\r\n";
        $office = true;
      }
      if ($perm == 'cus') {
        $s .= "\r\n    \$model['cx_cus'] = \$this->auth(array('user' => 'is_customer'));\r\n";
        $cus = true;
      }
    }
    
    if (! empty($s)) {
      $s .= "/* if (";
      if ($admin) {
        $s .= "\$model['cx_admin'] === false && ";
      }
      if ($mgr) {
        $s .= "\$model['cx_mgr'] === false && ";
      }
      if ($sales) {
        $s .= "\$model['cx_sales'] === false && ";
      }
      if ($office) {
        $s .= "\$model['cx_office'] === false && ";
      }
      if ($cus) {
        $s .= "\$model['cx_cus'] === false && ";
      }
      
      $s = rtrim($s, "&& ");
      $s .= ") { \r\n  cx\app\main_functions::set_message('You must signin with proper access rights to view this page.');
      \$this->do_login_redirect();
      }\r\n */ \r\n";
    }

    return $s;
  }

  private function is_make($opt) {
    if (count($this->make) > 1) {
      foreach($this->make as $junk=>$s) {
        if ($opt == $s) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * If file does not exist use it, else use _new... file 
   */
  private function get_save_name($file) {
    $new_file = str_replace("_new", "", $file);
    if (! is_file($new_file)) {
      return $new_file;
    } else {
      return $file;
    }
  }

  private function build_js() {
    $js_dir = PROJECT_BASE_DIR. 'assets' . DS . $this->folder_name . DS . $this->file_name . DS . 'js';
    $js_file = $this->get_save_name($js_dir . DS . $this->web_page_name . '_new.js');
    
    if (! is_dir($js_dir)) {
      mkdir($js_dir, 0777, TRUE); // recursive folders is true.
    }
    
    $fh = fopen($js_file, 'w') or die("can't open file");
    fwrite($fh, $this->get_header());
    fclose($fh);
  }

  private function build_css() {
    $css_dir = PROJECT_BASE_DIR. 'assets' . DS . $this->folder_name . DS . $this->file_name . DS . 'css';
    $css_file = $this->get_save_name($css_dir . DS . $this->web_page_name . '_new.css');
    
    if (! is_dir($css_dir)) {
      mkdir($css_dir, 0777, TRUE); // recursive folders is true.
    }
    
    $fh = fopen($css_file, 'w') or die("can't open file");
    fwrite($fh, $this->get_header());
    fclose($fh);    
  }

  private function build_model() {
    $model_dir = PROJECT_BASE_DIR. 'models' . DS . $this->folder_name . DS . $this->file_name;
    $model_file = $this->get_save_name($model_dir . DS . $this->web_page_name . '_new.php');
    
    if (! is_dir($model_dir)) {
      mkdir($model_dir, 0777, TRUE); // recursive folders is true.
    }
    
    $fh = fopen($model_file, 'w') or die("can't open file");
    fwrite($fh, "<?php \r\n");
    fwrite($fh, $this->get_header());
    fwrite($fh, "namespace cx\model;
use cx\database\model as the_model;

class $this->web_page_name extends the_model {

  protected \$table = '{$this->web_page_name}';
  protected \$key = 'id';
  
  public function __construct(\$options = array()) {
    \$options['table'] = \$this->table;
    \$options['key'] = \$this->key;
    parent::__construct(\$options);
  }
  
  public function caching() {
    return false;
  }
  
  public function get_row_example(\$id) {
    \$where = \"`{\$this->key}` = :key\";
    \$bind = array(':key'=>\$id);
    \$options['fetch'] = 'fetch_row';
    \$options['where'] = \$where;
    // See: /cx/classes/cx/database/db.php
    // \$this->database->insert(\$table, \$info);
    // \$this->database->update(\$table, \$info, \$where, \$bind);  
    return \$this->database->select(\$this->table, \$options, \$bind);
  }
  
  public function pdo_example() {
    \$sql = \"SELECT `id`,`section`,`enabled` FROM `categories` ORDER BY `section` ASC\";
    \$pdostmt = \$this->database->prepare(\$sql);
    if(\$pdostmt->execute() !== false) {
      return \$pdostmt->fetchAll(PDO_ASSCO);
    }
  }

} // end of model class
");
    fclose($fh);        
  }
  
  private function build_view() {
    $view_dir = PROJECT_BASE_DIR. 'views' . DS . $this->folder_name . DS . $this->file_name;
    $view_file = $this->get_save_name($view_dir . DS . $this->web_page_name . '_new.php');
    
    if (! is_dir($view_dir)) {
      mkdir($view_dir, 0777, TRUE); // recursive folders is true.
    }
    
    $fh = fopen($view_file, 'w') or die("can't open file");
    fwrite($fh, "<?php \r\n");
    fwrite($fh, $this->get_header());
    fwrite($fh, "echo \$form_data;");
    fwrite($fh, "\r\n?>");
    fclose($fh);        
  }
  
  private function build_form() {
    $form_dir = PROJECT_BASE_DIR. 'forms' . DS . $this->folder_name . DS . $this->file_name;
    $form_file = $this->get_save_name($form_dir . DS . $this->web_page_name . '_new.php');
    
    if (! is_dir($form_dir)) {
      mkdir($form_dir, 0777, TRUE); // recursive folders is true.
    }
    
    $fh = fopen($form_file, 'w') or die("can't open file");
    fwrite($fh, "<?php \r\n");
    fwrite($fh, $this->get_header());
    fwrite($fh, "
\$size = (isset(\$this->defaults['size']) ? \$this->defaults['size'] : '20');
\$rows = (isset(\$this->defaults['rows']) ? \$this->defaults['rows'] : '16');
\$cols = (isset(\$this->defaults['cols']) ? \$this->defaults['cols'] : '80');

// \$this->form('start_div', 'pwd', array('div-id'=>'pwd-container', 'div-class'=>'container'));

// \$this->form('COMMAND_NAME_GOES_HERE', 'ELEMENT_NAME', array('ATTRIBUTE'=>'PARM',....));

\$this->form('text', 'fname', array('size'=>\$size,
    'maxlength'=>'47', 'label'=>'Frist Name',
    'class'=>'form-control input-sm chat-input', 'required'=>true,
    'placeholder'=>'Enter the First Name'));
 
// \$this->form('end_div', 'pwd');

    \$this->form('submit', 'update', array('label'=>'Submit'));

// To build from MySQL Table: sitename/newtoolz/app/home/make_form.html?table=TABLENAME
      ");
    fclose($fh);    
  }

  public function generator() {
    
    if (!is_writable(PROJECT_BASE_DIR) || !is_dir(PROJECT_BASE_DIR)) {
      echo PROJECT_BASE_DIR . " is not writable!";
    }
    
    if ($this->is_make('js')) {
      $this->build_js();
      $js_code = "\$this->add_js('./assets/{$this->folder_name}/{$this->file_name}/js/{$this->web_page_name}.js');";
    } else {
      $js_code = '';
    }
    
    if ($this->is_make('css')) {
      $this->build_css();
      $css_code = "\$this->add_css('./assets/{$this->folder_name}/{$this->file_name}/css/{$this->web_page_name}.css');";
    } else {
      $css_code = '';
    }
    
    if ($this->is_make('model')) {
      $this->build_model();
      $model_code = "
    \$this->load_model('{$this->folder_name}' . DS . '{$this->file_name}' . DS . '{$this->web_page_name}');
    \$db_options = array();
    \${$this->web_page_name} = new cx\\model\\{$this->web_page_name}(\$db_options);        
        ";
    } else {
      $model_code = "    
    \$this->load_model();
    \$db_options = array('table' => '{$this->web_page_name}', 'key' => 'id');
    \$edit_user = new cx\\database\\model(\$db_options);
";
    }
    
    if ($this->is_make('view')) {
      $this->build_view();
      $view_code = "\$this->load_view('{$this->folder_name}' . DS . '{$this->file_name}' . DS. '{$this->web_page_name}', \$page);";
    } else {
      $view_code = "\$this->do_view(\$page['form_data']);";
    }
    
    if ($this->is_make('frm')) {
      $this->build_form();
      $form_code = "
    \$frm = \$this->load_class('cx\\form\\form', array('name' => '{$this->web_page_name}', 'defaults' => array('readonly' => false)));
    \$frm->grab_form('{$this->folder_name}' . DS . '{$this->file_name}' . DS. '{$this->web_page_name}', \$model);
    \$frm->end_form();
    \$page['form_data'] = \$frm->get_html();
";
    } else {
      $form_code = "\$page['form_data'] = '';";
    }
    
    $s = "<?php \r\n";
    $s .= $this->get_header();
    
    $s .= " /* \r\n * Controller: @link URL = websitename/{$this->folder_name}/{$this->file_name}/{$this->web_page_name}.html \r\n";
    $s .= " * Controller file path: projectpath/controllers/{$this->folder_name}/{$this->file_name}.php\r\n";
    $s .= " */ \r\n \r\n";
    
    $s .= "class cx_loader_{$this->folder_name}_{$this->file_name} extends cx\app\app {
  public function __construct() {
    parent::__construct(); // Must load app constructor
  }\r\n";
    $s .= "
  public function {$this->web_page_name}() {
    \$this->breadcrumb = array(\"javascript:;\"=>\"Home\");
    \$this->active_crumb = \"Index\";

    \$this->set_title('Hello,');
    " . $this->get_auth() . "
    $model_code
    $form_code
  
    $js_code
    $css_code

    // \$this->add_js_onready('ON READY CODE GOES HERE!');

    /*
      \$this->load_view('{$this->folder_name}' . DS . '{$this->file_name}' . DS. '{$this->web_page_name}', \$page, 'TEMPLATE_NAME'); // default is page
        OR
      \$this->load_part_view('{$this->folder_name}' . DS . '{$this->file_name}' . DS. '{$this->web_page_name}-Col3A', \$page);  
      \$this->load_part_view('{$this->folder_name}' . DS . '{$this->file_name}' . DS. '{$this->web_page_name}', \$page);
      \$this->end_view('TEMPLATE_NAME_GOES_HERE'); // default is page  
    */

    $view_code
  } // End of page method
} // End of controller class
      "; 
    
    if ($this->is_make('con')) {  
      $controller_dir = PROJECT_BASE_DIR. 'controllers' . DS . $this->folder_name;
      $controller_file = $this->get_save_name($controller_dir . DS . $this->file_name . '_new.php');

      if (! is_dir($controller_dir)) {
        mkdir($controller_dir, 0777, TRUE); // recursive folders is true.
      }

      $fh = fopen($controller_file, 'w') or die("can't open file");
      fwrite($fh, $s);
      fclose($fh);  
    } else {
      $out = $this->get_open_code();
      $out .= $s;
      $out .= $this->get_close_code();
      echo $out;
    }
    
    return "Done...";
  }

}