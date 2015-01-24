<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-10
 * Time: PM9:01
 */

namespace Feed\Model;

use Think\Model;

class FeedCommentModel extends Model
{
	//TODO
    protected $_validate = array(
        array('content', '1,99999', '内容不能为空', self::EXISTS_VALIDATE, 'length'),
        array('content', '0,500', '内容太长', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 目前comment_id没有用到，不能评论别人
     * @param unknown_type $uid
     * @param unknown_type $feed_id
     * @param unknown_type $content
     * @param unknown_type $location
     * @param unknown_type $comment_id
     * @return number|boolean|Ambigous <\Think\mixed, boolean, unknown>
     */
    public function add_comment($uid, $feed_id, $content, $location,$comment_id = 0)
    {
    	$comment_be_write_md5 = md5($uid.$feed_id.$content);
    	$exists_comment_md5 = $this->field('md5')->where('md5='.$comment_be_write_md5)->find();
    	if($exists_comment_md5) {return -2;}
        //写入数据库
        $data = array('uid' => $uid, 'content' => $content, 'feed_id' => $feed_id, 'comment_id' => $comment_id,'ip'=>get_client_ip(),'location'=>$location,'md5'=>$comment_be_write_md5);
        $data = $this->create($data);
        if (!$data) return false;
        $comment_id = $this->add($data);
		
        //增加微博评论数量
        D('Feed/Feed')->where(array('id' => $feed_id))->setInc('comment_count');
		//更新了微博评论数量，置缓存为null
        S('feed_' . $feed_id,null);
        //返回评论编号
        return $comment_id;
    }
	
    public function delete_comment($comment_id)
    {
        //获取微博编号
        $comment = D('Feed/FeedComment')->find($comment_id);
        $feed_id = $comment['feed_id'];

        //将评论标记为已经删除
        D('Feed/FeedComment')->where(array('id' => $comment_id))->setField('status', -1);

        //减少微博的评论数量
        D('Feed/Feed')->where(array('id' => $feed_id))->setDec('comment_count');
        S('feed_' . $feed_id,null);
        //返回成功结果
        return true;
    }
    
}