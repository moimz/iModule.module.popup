<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 팝업데이터를 불러온다.
 * 
 * @file /modules/popup/process/@getPopup.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 9. 19.
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx');
$data = $this->db()->select($this->table->popup)->where('idx',$idx)->getOne();
if ($data == null) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
	return;
}

$data->site = $data->domain.'/'.$data->language;
$data->start_date = date('Y-m-d',$data->start_date);
$data->end_date = date('Y-m-d',$data->end_date);

$size = explode(',',$data->size);
$data->width = $size[0];
$data->height = $size[1];

if ($data->position == 'AUTO') {
	$data->x = 0;
	$data->y = 0;
	$data->auto_position = true;
} else {
	$position = explode(',',$data->position);
	$data->x = $position[0];
	$data->y = $position[1];
	$data->auto_position = false;
}

if ($data->type == 'HTML') {
	$content = json_decode($data->content);
	$data->html = $this->IM->getModule('wysiwyg')->decodeContent($content->text,false);
	$data->html_files = $content->files;
}

if ($data->type == 'IMAGE') {
	$content = json_decode($data->content);
	$data->image = $this->IM->getModule('attachment')->getFileInfo($content->image);
	$data->image_link = $content->link;
}

if ($data->type == 'LINK') {
	$data->link = $data->content;
}

if ($data->type == 'EXTERNAL') {
	$data->external = $data->content;
}

$results->success = true;
$results->data = $data;
?>