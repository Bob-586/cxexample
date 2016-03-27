<?php

/**
 * @copyright (c) 2015
 * @author Chris Allen, Robert Strutts
 */

namespace cx\app;
use cx\app\main_functions as main_fn;

trait security {
  
  /**
   * 
   * @todo make me
   */
  public function api_is_logged_in() {
    return true;
  }

  public function auth_is_logged_in() {
    $id = $this->session->get_int(\cx_configure::a_get('cx','login') . 'id');
    return ($this->request->is_valid_id($id));
  }

  public function auth_login_check() {
    if ($this->auth_is_logged_in() === false) {
      main_fn::set_message('That page requires User rights, please sign-in.');
      $this->do_login_redirect();
    }
  }

  public function auth_admin_check() {
    if ($this->auth_is_admin() === false) {
      main_fn::set_message('That page requires Administrative rights, please sign-in.');
      $this->do_login_redirect();
    }
  }

  public function auth_is_api_user() {
    return main_fn::found($this->session->session_var(\cx_configure::a_get('cx','login') . 'rights'), 'api');
  }

  public function auth_is_admin() {
    return main_fn::found($this->session->session_var(\cx_configure::a_get('cx','login') . 'rights'), 'admin');
  }

  public function auth_is_manager() {
    return main_fn::found($this->session->session_var(\cx_configure::a_get('cx','login') . 'rights'), 'mgr');
  }

  public function auth_is_sales() {
    return main_fn::found($this->session->session_var(\cx_configure::a_get('cx','login') . 'rights'), 'sales');
  }

  public function auth_is_office() {
    return main_fn::found($this->session->session_var(\cx_configure::a_get('cx','login') . 'rights'), 'office');
  }

  public function auth_is_customer() {
    return main_fn::found($this->session->session_var(\cx_configure::a_get('cx','login') . 'rights'), 'cus');
  }

  public function get_login_full_name() {
    if ($this->auth_is_logged_in() === false) {
      return false;
    }
    return $this->session->session_var(\cx_configure::a_get('cx','login') . 'fname') . $this->session->session_var(\cx_configure::a_get('cx','login') . 'lname');
  }
    // 'admin' => 'Administrator', 'mgr' => 'Manager', 'sales'=>'Sales', 'office'=>'Office', 'cus' => 'Customer');
}
