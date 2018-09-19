<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 활성화된 팝업을 가져온다.
 * 
 * @file /modules/popup/process/getPopups.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 9. 19.
 */
if (defined('__IM__') == false) exit;

$site = $this->IM->getSite(false);
$popups = $this->db()->select($this->table->popup,'idx,window,size,position')->where('domain',$site->domain)->where('language',$site->language)->where('start_date',time(),'<=')->where('end_date',time(),'>')->orderBy('start_date','asc')->get();


for ($i=0, $loop=count($popups);$i<$loop;$i++) {
	$size = explode(',',$popups[$i]->size);
	unset($popups[$i]->size);
	
	$popups[$i]->width = $size[0] + $this->getModule()->getTemplet($this->getModule()->getConfig('templet'))->getPackage()->width;
	$popups[$i]->height = $size[1] + $this->getModule()->getTemplet($this->getModule()->getConfig('templet'))->getPackage()->height;
	
	if ($popups[$i]->position != 'AUTO') {
		$position = explode(',',$popups[$i]->position);
		$popups[$i]->x = $position[0];
		$popups[$i]->y = $position[1];
		$popups[$i]->position = 'FIXED';
	} else {
		
	}
	
	$popups[$i]->html = $popups[$i]->window == 'LAYER' || $this->getModule()->getConfig('auto_layer') == true ? $this->getPopupContext($popups[$i]->idx,'LAYER') : null;
}

$results->success = true;
$results->layer = new stdClass();
$results->layer->x = $this->getModule()->getConfig('layer_x');
$results->layer->y = $this->getModule()->getConfig('layer_y');
$results->window = new stdClass();
$results->window->x = $this->getModule()->getConfig('window_x');
$results->window->y = $this->getModule()->getConfig('window_y');
$results->popups = $popups;
?>