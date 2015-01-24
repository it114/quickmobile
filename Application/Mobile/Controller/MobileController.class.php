<?php

namespace Mobile\Controller;

use Think\Controller;

class MobileController extends  Controller {
	
	protected $ret_data = array('code'=>0,'msg'=>'fail');
	
	public function _initialize(){
		$this->verify_request();
	}
	
	private function verify_request(){ 
		$os = I('get.os','');
		$os_version = I('get.os_version','');
		$api_version = I('get.api_version','');
		$t = I('get.t','');
		$uuid = I('get.uuid','');
		$token = I('get.token','');
		if($os&&$os_version&&$api_version&&$t&&$uuid&&$token){			
			$tmp = md5($os.$os_version.$api_version.$t.$uuid); 
			if($tmp != $token){
				$this->api_error('口令错误');
			}
		} else {
			$this->api_error('参数错误');
		}
	}
	
	public function _empty() {
		$this->api_error('-接口错误-');//
	}
	
	protected function api_error($msg='',$data = array(),$code =0){
		$this->ret_data['msg'] =  $msg;
		$this->ret_data['data'] = $data;
		$this->ret_data['code'] = $code;
		$this->ajaxReturn($this->ret_data);
	}
	
	protected function api_success($msg='',$data=array(),$code = 1){
		$this->ret_data['msg'] =  $msg;
		$this->ret_data['data'] = $data;
		$this->ret_data['code'] = $code;
		$this->ajaxReturn($this->ret_data);
	}
	
}