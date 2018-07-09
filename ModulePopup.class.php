<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodule.kr)
 *
 * 홈페이지 내 팝업창과 관련된 전반적인 기능을 관리한다.
 * 
 * @file /modules/popup/ModulePopup.class.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 6. 21.
 */
class ModulePopup {
	/**
	 * iModule core 와 Module core 클래스
	 */
	private $IM;
	private $Module;
	
	/**
	 * DB 관련 변수정의
	 *
	 * @private object $DB DB접속객체
	 * @private string[] $table DB 테이블 별칭 및 원 테이블명을 정의하기 위한 변수
	 */
	private $DB;
	private $table;
	
	/**
	 * 언어셋을 정의한다.
	 * 
	 * @private object $lang 현재 사이트주소에서 설정된 언어셋
	 * @private object $oLang package.json 에 의해 정의된 기본 언어셋
	 */
	private $lang = null;
	private $oLang = null;
	
	/**
	 * class 선언
	 *
	 * @param iModule $IM iModule core class
	 * @param Module $Module Module core class
	 * @see /classes/iModule.class.php
	 * @see /classes/Module.class.php
	 */
	function __construct($IM,$Module) {
		/**
		 * iModule 및 Module 코어 선언
		 */
		$this->IM = $IM;
		$this->Module = $Module;
		
		/**
		 * 모듈에서 사용하는 DB 테이블 별칭 정의
		 * @see 모듈폴더의 package.json 의 databases 참고
		 */
		$this->table = new stdClass();
		$this->table->popup = 'popup_table';
		$this->table->admin = 'popup_admin_table';
		
		/**
		 * 팝업창을 띄우기 위한 자바스크립트를 로딩한다.
		 * 팝업모듈은 글로벌모듈이기 때문에 모듈클래스 선언부에서 선언해주어야 사이트 레이아웃에 반영된다.
		 */
		if ($this->getModule()->isInstalled() == true && defined('__IM_ADMIN__') == false && $IM->menu == 'index' && $IM->page == '') {
			$this->IM->addHeadResource('style',$this->getModule()->getDir().'/styles/style.css');
			$this->IM->addHeadResource('script',$this->getModule()->getDir().'/scripts/script.js');
			$this->getTemplet()->getHeader();
		}
	}
	
	/**
	 * 모듈 코어 클래스를 반환한다.
	 * 현재 모듈의 각종 설정값이나 모듈의 package.json 설정값을 모듈 코어 클래스를 통해 확인할 수 있다.
	 *
	 * @return Module $Module
	 */
	function getModule() {
		return $this->Module;
	}
	
	/**
	 * 모듈 설치시 정의된 DB코드를 사용하여 모듈에서 사용할 전용 DB클래스를 반환한다.
	 *
	 * @return DB $DB
	 */
	function db() {
		if ($this->DB == null || $this->DB->ping() === false) $this->DB = $this->IM->db($this->getModule()->getInstalled()->database);
		return $this->DB;
	}
	
	/**
	 * 모듈에서 사용중인 DB테이블 별칭을 이용하여 실제 DB테이블 명을 반환한다.
	 *
	 * @param string $table DB테이블 별칭
	 * @return string $table 실제 DB테이블 명
	 */
	function getTable($table) {
		return empty($this->table->$table) == true ? null : $this->table->$table;
	}
	
	/**
	 * URL 을 가져온다.
	 *
	 * @param string $view
	 * @param string $idx
	 */
	function getUrl($view=null,$idx=null) {
		$url = $this->IM->getUrl(null,null,false);
		$view = $view === null ? $this->IM->getView() : $view;
		$idx = $idx === null ? $this->IM->getIdx() : $idx;
		
		if ($view == null || $view == false) return $url;
		$url.= '/'.$view;
		
		if ($idx == null || $idx == false) return $url;
		return $url.'/'.$idx;
	}
	
	/**
	 * view 값을 가져온다.
	 *
	 * @param string $view
	 */
	function getView() {
		return $this->IM->getView();
	}
	
	/**
	 * idx 값을 가져온다.
	 *
	 * @param string $idx
	 */
	function getIdx() {
		return $this->IM->getIdx();
	}
	
	/**
	 * [코어] 사이트 외부에서 현재 모듈의 API를 호출하였을 경우, API 요청을 처리하기 위한 함수로 API 실행결과를 반환한다.
	 * 소스코드 관리를 편하게 하기 위해 각 요쳥별로 별도의 PHP 파일로 관리한다.
	 *
	 * @param string $protocol API 호출 프로토콜 (get, post, put, delete)
	 * @param string $api API명
	 * @param any $idx API 호출대상 고유값
	 * @param object $params API 호출시 전달된 파라메터
	 * @return object $datas API처리후 반환 데이터 (해당 데이터는 /api/index.php 를 통해 API호출자에게 전달된다.)
	 * @see /api/index.php
	 */
	function getApi($protocol,$api,$idx=null,$params=null) {
		$data = new stdClass();
		
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('beforeGetApi',$this->getModule()->getName(),$api,$values);
		
		/**
		 * 모듈의 api 폴더에 $api 에 해당하는 파일이 있을 경우 불러온다.
		 */
		if (is_file($this->getModule()->getPath().'/api/'.$api.'.'.$protocol.'.php') == true) {
			INCLUDE $this->getModule()->getPath().'/api/'.$api.'.'.$protocol.'.php';
		}
		
		unset($values);
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('afterGetApi',$this->getModule()->getName(),$api,$values,$data);
		
		return $data;
	}
	
	/**
	 * [사이트관리자] 모듈 설정패널을 구성한다.
	 *
	 * @return string $panel 설정패널 HTML
	 */
	function getConfigPanel() {
		/**
		 * 설정패널 PHP에서 iModule 코어클래스와 모듈코어클래스에 접근하기 위한 변수 선언
		 */
		$IM = $this->IM;
		$Module = $this->getModule();
		
		ob_start();
		INCLUDE $this->getModule()->getPath().'/admin/configs.php';
		$panel = ob_get_contents();
		ob_end_clean();
		
		return $panel;
	}
	
	/**
	 * [사이트관리자] 모듈 관리자패널 구성한다.
	 *
	 * @return string $panel 관리자패널 HTML
	 */
	function getAdminPanel() {
		/**
		 * 설정패널 PHP에서 iModule 코어클래스와 모듈코어클래스에 접근하기 위한 변수 선언
		 */
		$IM = $this->IM;
		$Module = $this;
		
		ob_start();
		INCLUDE $this->getModule()->getPath().'/admin/index.php';
		$panel = ob_get_contents();
		ob_end_clean();
		
		return $panel;
	}
	
	/**
	 * 언어셋파일에 정의된 코드를 이용하여 사이트에 설정된 언어별로 텍스트를 반환한다.
	 * 코드에 해당하는 문자열이 없을 경우 1차적으로 package.json 에 정의된 기본언어셋의 텍스트를 반환하고, 기본언어셋 텍스트도 없을 경우에는 코드를 그대로 반환한다.
	 *
	 * @param string $code 언어코드
	 * @param string $replacement 일치하는 언어코드가 없을 경우 반환될 메세지 (기본값 : null, $code 반환)
	 * @return string $language 실제 언어셋 텍스트
	 */
	function getText($code,$replacement=null) {
		if ($this->lang == null) {
			if (is_file($this->getModule()->getPath().'/languages/'.$this->IM->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->getModule()->getPath().'/languages/'.$this->IM->language.'.json'));
				if ($this->IM->language != $this->getModule()->getPackage()->language && is_file($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json') == true) {
					$this->oLang = json_decode(file_get_contents($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json'));
				}
			} elseif (is_file($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json'));
				$this->oLang = null;
			}
		}
		
		$returnString = null;
		$temp = explode('/',$code);
		
		$string = $this->lang;
		for ($i=0, $loop=count($temp);$i<$loop;$i++) {
			if (isset($string->{$temp[$i]}) == true) {
				$string = $string->{$temp[$i]};
			} else {
				$string = null;
				break;
			}
		}
		
		if ($string != null) {
			$returnString = $string;
		} elseif ($this->oLang != null) {
			if ($string == null && $this->oLang != null) {
				$string = $this->oLang;
				for ($i=0, $loop=count($temp);$i<$loop;$i++) {
					if (isset($string->{$temp[$i]}) == true) {
						$string = $string->{$temp[$i]};
					} else {
						$string = null;
						break;
					}
				}
			}
			
			if ($string != null) $returnString = $string;
		}
		
		$this->IM->fireEvent('afterGetText',$this->getModule()->getName(),$code,$returnString);
		
		/**
		 * 언어셋 텍스트가 없는경우 iModule 코어에서 불러온다.
		 */
		if ($returnString != null) return $returnString;
		elseif (in_array(reset($temp),array('text','button','action')) == true) return $this->IM->getText($code,$replacement);
		else return $replacement == null ? $code : $replacement;
	}
	
	/**
	 * 상황에 맞게 에러코드를 반환한다.
	 *
	 * @param string $code 에러코드
	 * @param object $value(옵션) 에러와 관련된 데이터
	 * @param boolean $isRawData(옵션) RAW 데이터 반환여부
	 * @return string $message 에러 메세지
	 */
	function getErrorText($code,$value=null,$isRawData=false) {
		$message = $this->getText('error/'.$code,$code);
		if ($message == $code) return $this->IM->getErrorText($code,$value,null,$isRawData);
		
		$description = null;
		switch ($code) {
			default :
				if (is_object($value) == false && $value) $description = $value;
		}
		
		$error = new stdClass();
		$error->message = $message;
		$error->description = $description;
		$error->type = 'BACK';
		
		if ($isRawData === true) return $error;
		else return $this->IM->getErrorText($error);
	}
	
	/**
	 * 템플릿 정보를 가져온다.
	 *
	 * @return string $package 템플릿 정보
	 */
	function getTemplet() {
		return $this->getModule()->getTemplet($this->getModule()->getConfig('templet'));
	}
	
	/**
	 * 모듈 외부컨테이너를 가져온다.
	 *
	 * @param string $container 컨테이너명
	 * @return string $html 컨텍스트 HTML
	 */
	function getContainer($container) {
		$popup = $this->db()->select($this->table->popup)->where('idx',$this->getView())->getOne();
		if ($popup == null) return $this->IM->printError('NOT_FOUND');
		
		switch ($container) {
			case 'window' :
				$this->IM->addHeadResource('script',$this->getModule()->getDir().'/scripts/script.js');
				$context = $this->getPopupContext($this->getView());
				
				$size = explode(',',$popup->size);
				$width = $size[0] + $this->getTemplet()->getPackage()->width;
				$height = $size[1] + $this->getTemplet()->getPackage()->height;
				
				$context.= PHP_EOL.'<script>Popup.resize('.$width.','.$height.');</script>';
				
				$this->IM->removeTemplet();
				$this->IM->setSiteTitle($popup->title);
				$footer = $this->getTemplet()->getFooter().$this->IM->getFooter();
				$header = $this->getTemplet()->getHeader();
				$header = $this->IM->getHeader().$header;
				
				return $header.PHP_EOL.$context.PHP_EOL.$footer;
		}
		
		return '';
	}
	
	/**
	 * 팝업 컨텍스트를 가져온다.
	 *
	 * @param string $idx 팝업고유번호
	 * @return string $html 컨텍스트 HTML
	 */
	function getPopupContext($idx) {
		$popup = $this->db()->select($this->table->popup)->where('idx',$idx)->getOne();
		if ($popup == null) return $this->IM->printError('NOT_FOUND');
		
		$size = explode(',',$popup->size);
		
		$header = PHP_EOL.'<div data-role="module" data-module="popup" data-idx="'.$idx.'">'.PHP_EOL;
		$footer = PHP_EOL.'</div>'.PHP_EOL;
		
		if ($popup->type == 'HTML') {
			$content = json_decode($popup->content);
			$content = $this->IM->getModule('wysiwyg')->decodeContent($content->text);
		} elseif ($popup->type == 'IMAGE') {
			$content = json_decode($popup->content);
			$image = $this->IM->getModule('attachment')->getFileInfo($content->image);
			
			if ($content->link) $content = '<a href="'.$content->link.'" target="_blank"><img src="'.$image->path.'"></a>';
			else $content = '<img src="'.$image->path.'">';
		} elseif ($popup->type == 'LINK') {
			$content = '<iframe src="'.$popup->content.'" scrolling="auto" frameborder="0" style="width:'.$size[0].'px; height:'.$size[1].'px;"></iframe>';
		} elseif ($popup->type == 'EXTERNAL') {
			$content = $this->getTemplet()->getExternal($popup->content);
		}
		
		$content = PHP_EOL.'<div data-type="'.$popup->type.'" style="width:'.$size[0].'px; height:'.$size[1].'px;">'.PHP_EOL.$content.PHP_EOL.'</div>'.PHP_EOL;
		
		return $this->getTemplet()->getContext('popup',get_defined_vars(),$header,$footer);
	}
	
	/**
	 * 현재 모듈에서 처리해야하는 요청이 들어왔을 경우 처리하여 결과를 반환한다.
	 * 소스코드 관리를 편하게 하기 위해 각 요쳥별로 별도의 PHP 파일로 관리한다.
	 * 작업코드가 '@' 로 시작할 경우 사이트관리자를 위한 작업으로 최고관리자 권한이 필요하다.
	 *
	 * @param string $action 작업코드
	 * @return object $results 수행결과
	 * @see /process/index.php
	 */
	function doProcess($action) {
		$results = new stdClass();
		
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('beforeDoProcess',$this->getModule()->getName(),$action,$values);
		
		/**
		 * 모듈의 process 폴더에 $action 에 해당하는 파일이 있을 경우 불러온다.
		 */
		if (is_file($this->getModule()->getPath().'/process/'.$action.'.php') == true) {
			INCLUDE $this->getModule()->getPath().'/process/'.$action.'.php';
		}
		
		unset($values);
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('afterDoProcess',$this->getModule()->getName(),$action,$values,$results);
		
		return $results;
	}
	
	/**
	 * 첨부파일을 동기화한다.
	 *
	 * @param string $action 동기화작업
	 * @param int $idx 파일 고유번호
	 */
	function syncAttachment($action,$idx) {
		/**
		 * 첨부파일 삭제
		 */
		if ($action == 'delete') {
//			$this->db()->delete($this->table->attachment)->where('idx',$idx)->execute();
		}
	}
	
	/**
	 * 모듈관리자인지 확인한다.
	 *
	 * @param int $midx 회원고유번호 (없을 경우 현재 로그인한 사용자)
	 * @return boolean $isAdmin
	 */
	function isAdmin($midx=null) {
		$midx = $midx == null ? $this->IM->getModule('member')->getLogged() : $midx;
		if ($this->IM->getModule('member')->isAdmin($midx) == true) return true;
		
		return $this->db()->select($this->table->admin)->where('midx',$midx)->has();
	}
}
?>