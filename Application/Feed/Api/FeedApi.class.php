<?php

namespace Feed\Api;

class FeedApi extends \Common\Api\BaseApi {
	
	private $feed_model = null;
	private $comment_model =  null;
	private $default_cache_time = 864000;
    public function __construct(){
    	$this->feed_model = D('Feed/Feed');
    	$this->comment_model = D('Feed/FeedComment');
     
	}
	
	public function list_feed($page = 1, $count = 30,$uid= -1, $map = array(), $keywords = '')
	{	
		$uid = intval($uid);
		if($uid!==-1 & 0< $uid){
			$map['uid'] = $uid;
		}
		
		if (isset($keywords) && $keywords != '') {
			$map['content'] = array('like', "%{$keywords}%");
		}
		
		$list = $this->feed_model->field()->where($map)
		->order('comment_count desc,create_time desc')->page($page, $count)->select();
		if($list) { 
			//查询数据库获取每一条微博的作者的详细信息，当当前UID为微博的UID的是，不查询，因为这个时候用户知道自己的信息是多少，如果是处于登录状态的化。
			$member_model  = D('Member/Member');
			foreach ($list as &$v) {  
					$v['pic_host'] = C('FEED_PICTURE_HOST');
					$tmp_user_info =   S('user_info_'.$v['uid']); 
					if(!$tmp_user_info) {
						$tmp_user_info = $member_model->info($v['uid']);
						//缓存每一条微博信息,只要涉及这条微博的更新，都会重新查询，TODO,缓存优化，以后换成redis？
						if($tmp_user_info) {
							$v['user_info'] = $tmp_user_info;
							S('user_info_'.$v['uid'],$tmp_user_info,$this->default_cache_time);
						} else {
							$v['user_info'] = array();
						}
					} else {
						$v['user_info'] = $tmp_user_info;
					}  

					//涂鸦列表信息，暂时读取最新30条，缓存时间5分钟
					$comment_list = S('comment_list'.$v['id']); 
					if(!$comment_list){
						$comment_list = $this->list_comment($v['id'],1,30);
						if($comment_list&&$comment_list['code']==1){
							S('comment_list',$comment_list,300);
							$v['comment_list'] = $comment_list;
						} else {
							$v['comment_list'] = array();
						}
					} else {
						$v['comment_list'] = $comment_list;
					}
			}
			$this->call_return['code'] = 1;
			$this->call_return['msg'] ='查询成功';
			$this->call_return['data'] = $list;
		} else {
			$this->call_return['code'] = 0;
			$this->call_return['msg'] ='查询失败,'.$this->feed_model->getError();
		}
		return $this->call_return;
	}
	
	/**
	 * // ($uid, $content='',$location='(0|0)=(""|""|""|"")',$type='feed',$from='mobile|android')
	 * @param unknown_type $uid
	 * @param unknown_type $content
	 * @param unknown_type $type
	 * @param unknown_type $from
	 */
	public function send_feed($uid,$content,$jin_du,$wei_du,$loc_info, $type = 'origin', $from = '')
	{	
		$location = get_location($jin_du, $wei_du, $loc_info);
		//TODO,发布是否频繁的检测
		$feed_id =  $this->feed_model->add_feed($uid,$content,$location,$type,$from);
		if($feed_id){
			if($feed_id == -2){
				$this->call_return['code'] = -2;
				$this->call_return['msg'] = '发布失败，重复发表!';
			} else {
				$this->call_return['code'] = 1;
				$this->call_return['msg'] = '发布成功!';
				$this->call_return['data'] = array('feed_id'=>$feed_id);
			}
			
		} else {
			$this->call_return['code'] = 0;
			$this->call_return['msg'] = '发布失败,'.$this->feed_model->getError();
		}
		return $this->call_return;
	}
	
	public function send_comment($uid,$feed_id, $content, $jin_du,$wei_du,$loc_info,$comment_id = 0)
	{
		$location = get_location($jin_du, $wei_du, $loc_info);
		//TODO,发布是否频繁的检测 add_comment($uid, $feed_id, $content, $location,$comment_id = 0)
		$post_comment_id = $this->comment_model->add_comment($uid,$feed_id,$content,$location,$comment_id);
		if($post_comment_id){
			if($post_comment_id == -2){
				$this->call_return['code'] = 0;
				$this->call_return['msg'] = '发布失败，重复发表!';
			} else {
				$this->call_return['code'] = 1;
				$this->call_return['msg'] = '发布成功!';
				$this->call_return['data'] = array('comment_id'=>$post_comment_id);
			}
		} else {
			$this->call_return['code'] = 0;
			$this->call_return['msg'] = '发布失败,'.$this->comment_model->getError();
		}
		return $this->call_return;
	}
	
	public function list_comment($feed_id, $page = 1, $count = 10)
	{
		$list = $this->comment_model->where('feed_id='.$feed_id)->order('create_time desc')->page($page,$count)->select(); 
	  	
		if($list) {
			$member_model  = D('Member/Member');
			foreach ($list as &$v){   
				$v['pic_host'] =C('FEED_PICTURE_HOST');  
				$tmp_user_info =   S('user_info_'.$v['uid']);  
			    if(!$tmp_user_info) {
			    	$tmp_user_info = $member_model->info($v['uid']); 
			    	$v['user_info'] = $tmp_user_info;
			    	S('user_info_'.$v['uid'],$tmp_user_info,$this->default_cache_time);
			    } else {
			    	$v['user_info'] = $tmp_user_info;
			    }
			}
			$this->call_return['code'] = 1;
			$this->call_return['msg'] = '获取成功!';
			$this->call_return['data'] =  $list;
		} else {
			$this->call_return['code'] = 0;
			$this->call_return['msg'] = '获取失败!';
		}
		return $this->call_return;
	}
	
	public function delete_feed($feed_id)
	{
		
	}
	
	public function delete_comment($comment_id)
	{
		
	}
}