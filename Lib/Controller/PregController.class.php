<?php
/**
*
*@descripe 正则表达式控制器
*/
class PregController{
	
	public function index(){

	}

	/**
	*@descripe 匹配手机号码
	*/
	public function phone($subject){
		$pattern = '/^1[3578]{1}\d{9}$/';
		return preg_match($pattern, $subject);
	}

	/**
	*@descripe 匹配汉字
	*/
	public function chinese($subject){
		$pattern = '/^[\x{4e00}-\x{9fa5}]{2,10}$/u';
		return preg_match($pattern, $subject);
	}


	/**
	*@descripe 匹配身份证
	*/
	public function idCard($subject){
		$pattern = '/^(\d{15}|\d{17}[0-9|X|x])$/';
		return preg_match($pattern, $subject);
	}


	/**
	 * [acaeter description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 * @descripe 验证英文
	 */
	
	public function character($subject){
		$pattern = '/^[a-zA-Z]{2,20}$/';
		return preg_match($pattern, $subject);
	}


	/**
	 * [email description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 * @descripe 验证邮箱
	 */
	
	public function email($subject){
		$pattern = "/[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/";
		return preg_match($pattern, $subject);
	}
}