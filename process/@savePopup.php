<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodule.kr)
 *
 * 팝업설정을 저장한다.
 * 
 * @file /modules/popup/process/@savePopup.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160903
 */
if (defined('__IM__') == false) exit;

$errors = array();
$idx = Request('idx');
$title = Request('title') ? Request('title') : $errors['title'] = $this->getErrorText('REQUIRED');
$type = Request('type') ? Request('type') : $errors['type'] = $this->getErrorText('REQUIRED');
$window = Request('window') ? Request('window') : $errors['window'] = $this->getErrorText('REQUIRED');
$start_date = Request('start_date') ? strtotime(Request('start_date')) : $errors['start_date'] = $this->getErrorText('REQUIRED');
$end_date = Request('end_date') ? strtotime(Request('end_date').' 24:00:00') : $errors['end_date'] = $this->getErrorText('REQUIRED');
$position = Request('auto_position') ? 'AUTO' : (strlen(Request('x')) > 0 && strlen(Request('y')) > 0 ? Request('x').','.Request('y') : $errors['x'] = $errors['y'] = $this->getErrorText('REQUIRED'));

if ($idx) {
	$popup = $this->db()->select($this->table->popup)->where('idx',$idx)->getOne();
	if ($popup == null) {
		$results->success = false;
		$results->message = $this->getErrorText('NOT_FOUND');
		return;
	}
}

if ($type == 'HTML') {
	$content = $this->IM->getModule('admin')->getWysiwygContent('html','popup','content');
	$content = json_encode($content,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}

if ($type == 'IMAGE') {
	if ($idx) {
		$content = json_decode($popup->content);
	} else {
		$content = new stdClass();
		$content->image = 0;
		$content->link = '';
	}
	
	if (isset($_FILES['image']) == true && $_FILES['image']['tmp_name']) {
		if (count($errors) == 0 && $content->image > 0) $this->IM->getModule('attachment')->fileDelete($content->image);
		$file = $this->IM->getModule('attachment')->fileSave($_FILES['image']['name'],$_FILES['image']['tmp_name'],'popup','image','PUBLISHED',true);
		$content->image = $file;
	} else {
		if (!$idx) {
			$errors['image'] = $this->getErrorText('REQUIRED');
		}
	}
	
	$content->link = Request('image_link');
	
	$image = $this->IM->getModule('attachment')->getFileInfo($content->image);
	$size = $image->width.','.$image->height;
	
	$content = json_encode($content,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}

if ($type == 'LINK') {
	$content = Request('link') ? Request('link') : $errors['link'] = $this->getErrorText('REQUIRED');
}

if ($type == 'EXTERNAL') {
	$content = Request('external') ? Request('external') : $errors['external'] = $this->getErrorText('REQUIRED');
}

if ($type != 'IMAGE') {
	$size = Request('width') && Request('height') ? Request('width').','.Request('height') : $errors['width'] = $errors['height'] = $this->getErrorText('REQUIRED');
}

if (count($errors) == 0) {
	$insert = array();
	$insert['title'] = $title;
	$insert['type'] = $type;
	$insert['window'] = $window;
	$insert['start_date'] = $start_date;
	$insert['end_date'] = $end_date;
	$insert['content'] = $content;
	$insert['size'] = $size;
	$insert['position'] = $position;
	$insert['reg_date'] = time();

	if ($idx) {
		$this->db()->update($this->table->popup,$insert)->where('idx',$idx)->execute();
	} else {
		$this->db()->insert($this->table->popup,$insert)->execute();
	}
	
	$results->success = true;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>