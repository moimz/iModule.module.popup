<?php
/**
 * 이 파일은 iModule 팝업모듈의 일부입니다. (https://www.imodules.io)
 *
 * 팝업 관리자를 저장한다.
 *
 * @file /modules/popup/process/@saveAdmin.php
 * @author Eunseop Lim (eslim@naddle.net)1
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 7. 17.
 */
if (defined('__IM__') == false) exit;

$midx = Param('midx');
$domain = Param('domain');

$this->db()->replace($this->table->admin,array('domain'=>$domain,'midx'=>$midx))->execute();
$results->success = true;
?>