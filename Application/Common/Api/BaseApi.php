<?php

namespace Common\Api;

/**
 * api的定位是本模块向其他模块开放的可以调用的接口；
 * api中的函数原则上不会有展示层面上的逻辑。
 * api返回值一定是一个数组，数组包含3部分code、msg、data（array）
 * api调用方根据函数的返回值方知调用结果。
 * @author andy
 *
 */
class BaseApi {
	/**
	 * code = 0函数操作成功；code = 1函数操作失败！
	 * @var unknown_type
	 */
	protected $call_return = array('code'=>0,'msg'=>'error','data'=>array());
	
	
	
}