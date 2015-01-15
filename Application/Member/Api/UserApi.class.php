<?php
// +----------------------------------------------------------------------
// | QuickMobile [ mobile fist ,quick mobile ! ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.quickmobile.cn All right reserved.
// +----------------------------------------------------------------------
// | Author: andy<xinyun678@gmail.com>
// +----------------------------------------------------------------------
namespace Member\Api;
use Common\Api\BaseApi;
use User\Api\UserApi;

/**
 * 本系统所有用户相关操作都走这里
 * @author andy
 *
 */
class UserApi extends BaseApi {
	
	/**
	 * 手机号码注册-注册到用户中心
	 * @param unknown_type $phone
	 * @param unknown_type $password
	 */
	public function register_with_phone_password($phone = '', $password = ''){
		if(IS_POST){
			$User = new UserApi;
			$username = md5($phone,true);//TODO,用户名自动生成唯一不重复的算法，目前看起来没什么问题，手机号是唯一的直接导致用户名也是唯一的,true参数保证生产16位用户名。
		 	$email = $username.'@quickmobile.com.cn';//TODO,这里的目的是为了让邮箱在用户中心注册可以验证通过。
			$uid = $User->register($username, $password,$email,$phone);
			if(0 < $uid){
				$this->call_return['msg'] = '注册成功!';
				$this->call_return['code'] = 1;
				$this->call_return['data'] = array('uid'=>$uid);
			}else {
				$this->call_return['msg'] = $this->showRegError($uid);
			}
		} else {
			$this->call_return['msg'] = '-注册异常-';
		}
		return $this->call_return;
	}
	
	/**
	 * 手机号码登录
	 * @param unknown_type $phone
	 * @param unknown_type $password
	 */
	public function login_with_phone_password($phone,$password=''){
		$user = new UserApi;
		$uid = $user->login($phone, $password,3);//3手机登录 登录到用户中心
		if(0 < $uid){ //UC登录成功
			/* 登录用户 */
			$member = D('Member');
			$user = $member->login($uid); //登录到系统
			if($user){ //登录用户
				$this->call_return['code'] = 1;
				$this->call_return['msg'] = '登陆成功~';
				$this->call_return['data'] = $user;
			} else {
				$this->call_return['msg'] = $member->getError();
			}
		} else { //登录失败
			switch($uid) {
				case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
				case -2: $error = '密码错误！'; break;
				default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
			}
			$this->call_return['msg'] = $error;
		}
		return $this->call_return;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}
}