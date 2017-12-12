<?php
namespace phpkit\backend;
use phpkit\core\Phpkit as Phpkit;

class View {
	/**
	 * 显示后台模板
	 * @return
	 */
	function __construct($param = array()) {

		Phpkit::getDi();
		$viewDir = dirname(__FILE__) . '/views/';
		$this->view = Phpkit::getViews($viewDir);	
		//模板渲染
		$this->assets = new \Phalcon\Assets\Manager();
		//配置信息
		$this->setting = Phpkit::getDi()->getConfig()->get('setting');
	}

	public function display($content) {
		$controllerName = Phpkit::getDi()->getDispatcher()->getControllerName();
		//系统菜单
		$model = new models\SystemAdminMenuUser();
		$memuModel = new models\SystemAdminMenu();
		$session = Phpkit::getDi()->getSession();
		$adminUserInfo = $session->get('adminUserInfo');
		$memuList = $model->getMenuListByUserId($adminUserInfo['id']);
		$ActiveMenu = Phpkit::getDi()->getRequest()->getQuery('activemenu');
		if (empty($ActiveMenu)) {
			$ActiveMenu = $controllerName;
		}
		//var_dump($ActiveMenu);
		$ActiveMenuData = $memuModel->where("Code='$ActiveMenu'")->load();
		//var_dump($ActiveMenuData->toArray());
		foreach ($memuList as $key => $values) {
			if ($values['Id'] == $ActiveMenuData->Pid) {
				$values['active'] = 'active open';
			}
			if(is_array($values['scat'])){
				foreach ($values['scat'] as $key2 => $value) {
					if ($value['Id'] == $ActiveMenuData->Id) {
						$value['active'] = 'active';
					}
					$value['Url'] = $this->getUrl($value['Url']);
					$values['scat'][$key2] = $value;
				}
			}
			$memuList[$key] = $values;
		}
		$this->view->memuList = $memuList;
		$this->view->content = $content;
		$this->view->adminUserInfo = $adminUserInfo;
		$setting = Phpkit::getDi()->getConfig()->get("setting");
		$this->view->asstesUrl = $setting['asstesUrl'];
		echo $this->view->render('index');

	}

	function getUrl($url){
		$adminUrls = $this->setting['adminUrl'];
         foreach($adminUrls as $k=>$v){
         	 $url = str_replace($k, $v, $url);
         }
         if(strpos($url, "http")!==0){
         	$url=$adminUrls['default'].$url;
         }
         return $url;
	}

	public function login() {
		echo $this->view->render('login');
	}

}
