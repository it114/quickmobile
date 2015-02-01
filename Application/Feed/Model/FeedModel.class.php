<?php

namespace Feed\Model;

use Think\Model;
	
class FeedModel extends Model
{	
	protected $_validate = array(
			array('content', '1,99999', '内容不能为空', self::EXISTS_VALIDATE, 'length'),
			array('content', '0,500', '内容太长', self::EXISTS_VALIDATE, 'length'),
	);
	
	protected $_auto = array(
			array('create_time', NOW_TIME, self::MODEL_INSERT),
			array('status', '1', self::MODEL_INSERT),
	);
	
	/**
	 * location 发布位置，格式：(经度,维度)=(省|市|县|其他地址信息)，经度、维度失败的化都是0；省市县等信息如果获取失败则默认是空字符串
	 * @param unknown_type $uid
	 * @param unknown_type $content
	 * @param unknown_type $location
	 * @param unknown_type $type
	 * @param unknown_type $from
	 * 
	 * -2代表微博已经存在了，重复发表
	 */
	public function add_feed($uid, $content=array(),$location='(0|0)=(""|""|""|"")',$type='feed',$from='mobile|android')
	{	
		$anonymous = $content['anonymous'] ?  $content['anonymous'] :0 ;
		$from = $content['from']?$content['from']:'';
		$content = json_encode($content);
		$md5_be_write = md5($content.$uid);
		$exist_feed_md5 = $this->field('md5')->where(array('md5'=>$md5_be_write))->find();
		if($exist_feed_md5) {
			return -2;
			return;
		}
		$ip = get_client_ip();
		//写入数据库
		$data = array('uid'=>$uid,'anonymous'=>$anonymous,'content'=>$content,'type'=>$type,'from'=>$from,'ip'=>$ip,'md5'=>$md5_be_write,'from'=>$from, 'location'=>$location);
		$data = $this->create($data);
		if(!$data) return false;
		$feed_id = $this->add($data);
		//返回微博编号
		return $feed_id;
	}


}