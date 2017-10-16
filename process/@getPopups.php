<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodule.kr)
 *
 * 팝업목록을 불러온다.
 * 
 * @file /modules/popup/process/@getPopups.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160903
 */
if (defined('__IM__') == false) exit;

$start = Request('start');
$limit = Request('limit');
$sort = Request('sort');
$dir = Request('dir');

$lists = $this->db()->select($this->table->popup);
$total = $lists->copy()->count();

$lists = $lists->limit($start,$limit)->orderBy($sort,$dir)->get();

$results->success = true;
$results->lists = $lists;
$results->total = $total;
?>