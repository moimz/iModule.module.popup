<?php
/**
 * 이 파일은 iModule 팝업모듈의 일부입니다. (https://www.imodules.io)
 *
 * 관리자 정보를 가져온다.
 * 
 * @file /modules/popup/process/@getAdmin.php
 * @author Eunseop Lim (eslim@naddle.net)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 7. 17.
 */
if (defined('__IM__') == false) exit;

$midx = Request('midx');
$admin = $this->db()->select($this->table->admin)->where('midx',$midx)->getOne();
if ($admin == null) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
	return;
}

$member = $this->IM->getModule('member')->getMember($midx);

$results->success = true;
$results->member = $member;
$results->domain = $admin->domain == '*' ? '*' : explode(',',$admin->domain);
?>