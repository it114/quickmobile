<?php

namespace Wap\Controller;


class TestController extends \Think\Controller {
	
	
	public function	upload(){
		if(IS_GET){
			$this->display();
		}else {
			echo R('Feed/Feed/send',$_POST);
		}
		
	}
		
} 
