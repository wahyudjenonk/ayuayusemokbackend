<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/smarty/libs/Smarty.class.php');
class nsmarty extends Smarty {

  function __construct(){
	parent::__construct();
	$this->setTemplateDir(APPPATH.'views/');   
	$this->setCompileDir('__tmp/');
  }
}
?>