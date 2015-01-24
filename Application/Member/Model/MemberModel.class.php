<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// + Copyright (c) 2014 http://www.quickmobile.cn All right reserved.
// +----------------------------------------------------------------------
// + Author: andy<xinyun678@gmail.com>
// +----------------------------------------------------------------------
namespace Member\Model;
use Think\Model;
use User\Api\UserApi;

/**
 * 系统会员模型，这里来自于同步Home模块，未来Home模块中的该类可以不要。
 * 
 */
class MemberModel extends Model {

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('login', 0, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('last_login_ip', 0, self::MODEL_INSERT),
        array('last_login_time', 0, self::MODEL_INSERT),
        array('update_time', NOW_TIME),
        array('status', 1, self::MODEL_INSERT),
    );
	
    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
        if(!$user){ //未注册
            /* 在当前应用中注册用户 */
        	$Api = new UserApi();
        	$info = $Api->info($uid);
            $user = $this->create(array('nickname' => $info[1], 'status' => 1));
            $user['uid'] = $uid;
            if(!$this->add($user)){
                $this->error = '用户信息注册失败，请重试！';
                return false;
            }
        } elseif(1 != $user['status']) {
            $this->error = '用户未激活或已禁用！'; //应用级别禁用
            return false;
        }
        //记录行为
        action_log('user_login', 'member', $uid, $uid);
        return $user;//这里返回用户信息给调用者
    }
    
    public function info($uid = -1,$field = array('uid','nickname','uid','birthday','sex','avatar','qq','score')){
    	$uid = intval($uid);
    	if($uid!==-1 && 0< $uid) {
    		$user = $this->field($field)->find($uid);
    		if($user) {
    			return $user;
    		}
    		$this->error = '查询用户信息失败：'.$this->error; 
    	} else {
    		$this->error = '用户id参数错误';
    		return false;
    	}
    }

}
