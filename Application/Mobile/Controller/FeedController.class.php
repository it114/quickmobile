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
		$anonymous 	=	I('post.anonymous'); //0,实名，1匿名
		if(!$uid||!$jin_du||!$wei_du||!$loc_info||!$from) {
			$this->api_error('提交参数错误');
		}
		//理图片上传
		//content保存原始图片的路径和涂鸦图片的路径协议：
		//<main_pic>Feed/2015-01-23/54c120ed4677d.jpg</main_pic><tuya_pic>Feed/2015-01-23/54c120ed47eed.jpg</tuya_pic>
		//读取上传的图片
		$upload_data = upload_file(array('main_pic','tuya_pic'),'Uploads/Tuya/');
		if($upload_data&&$upload_data['code'] == 0){
			$this->api_error('上传图片失败,'.$upload_data['msg']);
		}  
		//插入主图信息
// 		$main_pic_info = array(
// 				'uid'=>$uid,
// 				'pic_path'=>$upload_data['data']['main_pic']['savepath'].$upload_data['data']['main_pic']['savename'],
// 				'create_time'=>time(),
// 				);
		//$add_pic_info = D('pics')->add($main_pic_info);
		//savepath	上传文件的保存路径
		//name	上传文件的原始名称
		//savename	上传文件的保存名称
// 		$content='<main_pic>'.$upload_data['data']['main_pic']['savepath'].$upload_data['data']['main_pic']['savename'].'</main_pic>'.
// 		'<tuya_pic>'.$upload_data['data']['tuya_pic']['savepath'].$upload_data['data']['tuya_pic']['savename'].'</tuya_pic>';
		$content = array(
					'main_pic'=>$upload_data['data']['main_pic']['savepath'].$upload_data['data']['main_pic']['savename'],
					'tuya_pic'=>$upload_data['data']['tuya_pic']['savepath'].$upload_data['data']['tuya_pic']['savename'],
					'anonymous'=>$anonymous,
					'from'=>$from,
				);
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
	
	// list_feed($page = 1, $count = 30,$uid= -1, $map = array(), $keywords = '')
    public function listfeeds(){
    	$page 		= I('get.page',0);
    	$count 		= I('get.count',20);
    	$uid		= I('get.uid',-1);
    	$keywords   = I('get.keywords','');
    	
    	if(!$page){
    		$this->api_error('提交参数错误');
    	}
    	
    	$feed_api = new FeedApi();
    	$ret = $feed_api->list_feed($page,$count,$uid,array(),$keywords);
    	if($ret['code'] != 0){
    		if($ret['code'] == 1){
    			$this->api_success('查询成功',$ret['data']);
    		} else {
    			$this->api_error('查询失败');
    		}
    	} else  {
    		$this->api_error('查询失败,'.$ret['msg']);
    	}
    }
    
    /**
     * 发表回复涂鸦内容
     */
    public function sendcomment(){
    	$jin_du  = 		I('post.jin_du',0);
    	$wei_du  = 		I('post.wei_du',0);
    	$loc_info =		I('post.loc_info','');
    	$type 	  =		'origin';//暂时不支持转发。origin：原始微博；repost：转发；
    	$from	  =		I('post.from','');
    	$feed_id  =		I('post.feed_id',0);
    	$uid      =		I('post.uid',0);
    	$main_pic_path	=   I('post.main_pic_path','');
    	if(!$uid||!$jin_du||!$wei_du||!$loc_info||!$from||!$feed_id||!$main_pic_path) {
    		$this->api_error('提交参数错误');
    	}
    	//理图片上传
    	//content保存原始图片的路径和涂鸦图片的路径协议：
    	//<main_pic>Feed/2015-01-23/54c120ed4677d.jpg</main_pic><tuya_pic>Feed/2015-01-23/54c120ed47eed.jpg</tuya_pic>
    	//读取上传的图片
    	$upload_data = upload_file(array('tuya_pic'),'Uploads/Tuya/');
    	if($upload_data&&$upload_data['code'] == 0){
    		$this->api_error('上传图片失败,'.$upload_data['msg']);
    	}
    	//savepath	上传文件的保存路径
    	//name	上传文件的原始名称
    	//savename	上传文件的保存名称
    	//TODO，完成main_pic的保存逻辑
//     	$content='<main_pic>'.'TODO...'.'</main_pic>'.
//     			'<tuya_pic>'.$upload_data['data']['tuya_pic']['savepath'].$upload_data['data']['tuya_pic']['savename'].'</tuya_pic>';
    	$content = array(
	    			'main_pic'=>$main_pic_path,
	    			'tuya_pic'=>$upload_data['data']['tuya_pic']['savepath'].$upload_data['data']['tuya_pic']['savename']
    			);
    	//
    	$feed_api  = new FeedApi();
    	$res = $feed_api->send_comment($uid,$feed_id, $content, $jin_du, $wei_du, $loc_info);
    	if($res){
    		if($res['code'] == 1){
    			$this->api_success('发布成功!',array('comment_id'=>$res['data']['comment_id']));
    		} else {
    			//TODO处理删除图片的逻辑
    			$this->api_error($res['msg']);
    		}
    	} else {
    		//TODO处理删除图片的逻辑
    		$this->api_error('发表失败');
    	}
    }
    
    
    public function listcomment(){
    	$feed_id = I('get.feed_id',0);
    	$page  =   I('get.page',0);
    	$count =   I('get.count',0);
    	
    	if(!feed_id||!$page||!$count) {
    		$this->api_error('参数缺少');
    	}
    	
    	$feed_api = new FeedApi();
    	$data = $feed_api->list_comment($feed_id,$page,$count);
    	if($data){
    		if($data['code'] == 1){
    			$this->api_success('获取成功',$data['data']);
    		} else {
    			$this->api_error('获取失败');
    		}
    	} else {
    		$this->api_error('获取异常');
    	}
    }
	
	public function index(){
 		
	}
}