<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 팝업을 삭제한다.
 * 
 * @file /modules/popup/process/@deletePopup.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 9. 19.
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx') ? explode(',',Request('idx')) : array();

if (count($idx) > 0) {
	$popups = $this->db()->select($this->table->popup)->where('type',array('HTML','IMAGE'),'IN')->where('idx',$idx,'IN')->get();
	foreach ($popups as $popup) {
		$content = json_decode($popup->content);
		
		if ($popup->type == 'HTML') $this->IM->getModule('attachment')->fileDelete($content->files);
		else $this->IM->getModule('attachment')->fileDelete($content->image);
	}
	$this->db()->delete($this->table->popup)->where('idx',$idx,'IN')->get();
}

$results->success = true;
?>