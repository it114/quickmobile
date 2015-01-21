<?php

namespace Mobile\Controller;
use Feed\Api\FeedApi;
use Mobile\Controller\MobileController;

class FeedController  extends  MobileController{
	
	//send_weibo($uid,$content,$jin_du,$wei_du,$loc_info, $type = 'origin', $from = '')
	public function send(){
		$uid     = 		I('post.uid',0);
		//$content = 		I('post.content','');
		$jin_du  = 		I('post.jin_du',0);
		$wei_du  = 		I('post.wei_du',0);
		$loc_info =		I('post.loc_info');
		$type 	  =		'origin';//暂时不支持转发。origin：原始微博；repost：转发；
		$from	  =		I('post.from','');

		if(!$uid||!$jin_du||!$wei_du||!$loc_info||!$from) {
			$this->api_error('提交参数错误');
		}
		//理图片上传
		//content保存原始图片的路径和涂鸦图片的路径协议：
		//<main_pic>http://www.infuntu.com/pic/1234.png</main_pic><tuya_pic>http://www.infuntu.com/pic/1234.png</tuya_pic>
		//读取上传的图片
		$upload_data = upload_file(array('main_pic','tuya_pic'),'Uploads/Tuya/');
		if($upload_data&&$upload_data['code'] == 0){
			$this->api_error('上传图片失败,'.$upload_data['msg']);
		}
		//savepath	上传文件的保存路径
//name	上传文件的原始名称
//savename	上传文件的保存名称
		$content='<main_pic>'.$upload_data['data']['main_pic']['savename'].'</main_pic>'.
		'<tuya_pic>'.$upload_data['data']['tuya_pic']['savename'].'</tuya_pic>';
		//
		$feed_api  = new FeedApi();
		$res = $feed_api->send_feed($uid, $content, $jin_du, $wei_du, $loc_info);
		if($res){
			if($res['code'] == 1){
				$this->api_success('发布成功!',array('feed_id'=>$res['data']['feed_id']));
			} else {
				//TODO处理删除图片的逻辑
				$this->api_error($res['msg']);
			}
		} else {
			//TODO处理删除图片的逻辑
			$this->api_error('发表失败');
		}
		
	}
	
	public function index(){
		echo 'fdsfsd';
	}
}