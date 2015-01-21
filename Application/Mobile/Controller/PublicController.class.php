<?php

namespace Mobile\Controller;
use Mobile\Controller\MobileController;
use Member\Api\MemberApi;

class PublicController extends  MobileController {
	
	public function login(){
		$usertel = I('get.tel');
		$password = I('get.password');
		$user_api = new MemberApi();
		$res = $user_api->login_with_phone_password($usertel,$password); 
		if($res){
			if($res['code'] == 1) {
				$this->api_success('登录成功!',$res['data']);
			} else {
				$this->api_error($res['msg']);
			}
		} else  {
			$this->api_error('登录失败');
		}
	}
	
	public function register(){
		$usertel = I('post.tel');
		$password = I('post.password');
		$user_api = new MemberApi();
		$result = $user_api->register_with_phone_password($usertel,$password);
		if($result){
			if($result['code']==1){
				$this->api_success('注册成功',$result['data']);
			} else {
				$this->api_error($result['msg']);
			}
		} else { 
			$this->api_error('注册失败');
		}
		
	}
}