<?php

namespace cx\app;

trait assets {
  
  /**
   * This method is auto loaded upon Project Template useage....
   */
  private function set_code() {
    $this->add_main_css(CX_BASE_REF.'/assets/bootstrap/css/bootstrap.min.css');
    $this->add_main_css(CX_BASE_REF.'/assets/font-awesome/css/font-awesome.min.css');
    $this->add_main_js(CX_BASE_REF.'/assets/jquery/jquery-2.1.4.min.js');
    
    $this->add_main_css(CX_BASE_REF.'/assets/foundation-6/css/foundation.css');
//    $this->add_main_css(CX_BASE_REF.'/assets/foundation-6/css/app.css');  
    $this->add_main_js(CX_BASE_REF.'/assets/foundation-6/js/vendor/what-input.min.js');
    $this->add_main_js(CX_BASE_REF.'/assets/foundation-6/js/foundation.min.js');
    $this->add_main_js(CX_BASE_REF.'/assets/foundation-6/js/app.js');
    
    $this->add_main_js(CX_BASE_REF.'/assets/bootstrap/js/bootstrap.min.js');
    $this->add_main_js(CX_BASE_REF.'/assets/parsley/parsley.min.js');
    $this->add_main_js(CX_BASE_REF.'/assets/rob.js');
  }
  
}