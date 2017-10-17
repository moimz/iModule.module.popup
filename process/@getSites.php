<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodule.kr)
 *
 * 팝업이 게시될 사이트를 불러온다.
 * 
 * @file /modules/popup/process/@getSites.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160903
 */
if (defined('__IM__') == false) exit;

if ($this->IM->getModule('member')->isAdmin() == false) {
	$domain = $this->db()->select($this->table->admin)->where('midx')->get('domain');
	if (count($admin) == 0) {
		$results->success = false;
		$results->message = $this->getErrorText('FORBIDDEN');
		return;
	}
}

$lists = $this->IM->db()->select($this->IM->getTable('site'));
if ($this->IM->getModule('member')->isAdmin() == false) $lists->where('domain',$domain,'IN');
$lists = $lists->get();
for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i]->display = $lists[$i]->title.'(';
	$lists[$i]->display.= $lists[$i]->is_ssl == 'TRUE' ? 'https://' : 'http://';
	$lists[$i]->display.= $lists[$i]->domain.__IM_DIR__.'/'.$lists[$i]->language.'/)';
	$lists[$i]->value = $lists[$i]->domain.'/'.$lists[$i]->language;
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>
