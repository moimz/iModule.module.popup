<?php
/**
 * 이 파일은 iModule 팝업모듈의 일부입니다. (https://www.imodules.io)
 *
 * 운영자 리스트를 가져옵니다.
 *
 * @file /modules/popup/process/@getAdmins.php
 * @author Eunseop Lim (eslim@naddle.net)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 7. 17.
 */
if (defined('__IM__') == false) exit;

$lists = $this->db()->select($this->table->admin)->get();
for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$member = $this->IM->getModule('member')->getMember($lists[$i]->midx);
	
	$lists[$i]->name = $member->name;
	$lists[$i]->email = $member->email;

	if ($lists[$i]->domain != null) {
		if ($lists[$i]->domain == '*') {
			$lists[$i]->site = '모든 사이트';
		} else {
			$domains = explode(',',$lists[$i]->domain);
			$lists[$i]->site = '';
			foreach ($domains as $idx=>$domain) {
				$site = $this->IM->getSites($domain, $this->IM->getLanguage());
				$lists[$i]->site.= ($idx != 0 ? ', ' : '').$site->title.'(';
				$lists[$i]->site.= $site->is_ssl == true ? 'https://' : 'http://';
				$lists[$i]->site.= $site->domain.__IM_DIR__.'/'.$site->language.'/)';
			}
		}
	}
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>