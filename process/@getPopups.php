<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodule.kr)
 *
 * 팝업목록을 불러온다.
 * 
 * @file /modules/popup/process/@getPopups.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2017. 12. 7.
 */
if (defined('__IM__') == false) exit;

$start = Request('start');
$limit = Request('limit');
$sort = Request('sort');
$dir = Request('dir');

if ($this->IM->getModule('member')->isAdmin() == false) {
	$domain = $this->db()->select($this->table->admin)->where('midx')->get('domain');
	if (count($admin) == 0) {
		$results->success = false;
		$results->message = $this->getErrorText('FORBIDDEN');
		return;
	}
}

$lists = $this->db()->select($this->table->popup);
if ($this->IM->getModule('member')->isAdmin() == false) $lists->where('domain',$domain,'IN');
$total = $lists->copy()->count();
$lists = $lists->limit($start,$limit)->orderBy($sort,$dir)->get();
for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$site = $this->IM->getSites($lists[$i]->domain,$lists[$i]->language);
	$lists[$i]->site = $site->title.'(';
	$lists[$i]->site.= $site->is_ssl == true ? 'https://' : 'http://';
	$lists[$i]->site.= $site->domain.__IM_DIR__.'/'.$site->language.'/)';
	
}

$results->success = true;
$results->lists = $lists;
$results->total = $total;
?>