<?php
/**
 * Demo
 */
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
	/**
	 * Demo function
	 */
	public function index(){
		$this->display('index');
	}
}