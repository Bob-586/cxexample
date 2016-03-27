<?php

/**
 * @copyright (c) 2015
 * @author Chris Allen, Robert Strutts
 */

$size = (isset($this->defaults['size']) ? $this->defaults['size'] : '20');
$rows = (isset($this->defaults['rows']) ? $this->defaults['rows'] : '16');
$cols = (isset($this->defaults['cols']) ? $this->defaults['cols'] : '80');

$this->form('text', 'folder_name', array('size'=>$size,
    'maxlength'=>'27', 'label'=>'Root Folder Name',
    'class'=>'txt-field righty', 'required'=>true,
    'div-class'=>'small-field-box',
    'placeholder'=>'Enter Root Folder name'));

$this->form('text', 'file_name', array('size'=>$size,
    'maxlength'=>'27', 'label'=>'Controller File Name / Sub Folder',
    'class'=>'txt-field righty', 'required'=>true,
    'div-class'=>'small-field-box',
    'placeholder'=>'Enter Controller File name (Sub Folder to other files)'));

$this->form('text', 'web_page_name', array('size'=>$size,
    'maxlength'=>'27', 'label'=>'Controller Method name',
    'class'=>'txt-field righty', 'required'=>true,
    'div-class'=>'small-field-box',
    'placeholder'=>'Enter Web Page File name'));

$this->form('checkboxes', 'rights', array('options'=>$model['rights_statuses']));

$do = array('con'=>'Controller', 'js'=>'JavaScript', 'css'=>'CSS', 'model'=>'Model', 'view'=>'View', 'frm'=>'Form');
$this->form('checkboxes', 'make', array('options'=>$do, 'checked'=>array('js'=>true, 'css'=>true, 'model'=>true, 'view'=>true, 'frm'=>true)));

$this->form('submit', 'update', array('label'=>'Create'));
