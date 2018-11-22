<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 팝업목록을 불러온다.
 * 
 * @file /modules/popup/process/@getPopups.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 11. 22.
 */
if (defined('__IM__') == false) exit;

$start = Request('start');
$limit = Request('limit');
$sort = Request('sort');
$dir = Request('dir');

$domain = Request('domain');
$language = Request('language');

$lists = $this->db()->select($this->table->popup);
if ($domain && $language) $lists = $lists->where('domain', $domain)->where('language', $language);
if ($this->isAdmin() !== true) $lists->where('domain',$this->isAdmin(),'IN');
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