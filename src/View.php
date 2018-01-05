<?php
namespace phpkit\backend;
use phpkit\core\Phpkit as Phpkit;

class View {
	/**
	 * 显示后台模板
	 * @return
	 */
	function __construct($param = array()) {
		$this->di = $param['phpkitApp']?$param['phpkitApp']->getDi(): Phpkit::getDi();
		$viewDir = dirname(__FILE__) . '/views/';
		$this->view =$param['phpkitApp']?$param['phpkitApp']->view : Phpkit::getViews($viewDir);
		//模板渲染
		$this->assets = new \Phalcon\Assets\Manager();

	}

	public function display($content) {
		$controllerName = Phpkit::getDi()->getDispatcher()->getControllerName();
		//系统菜单
		$model = new models\SystemAdminMenuUser();
		$memuModel = new models\SystemAdminMenu();
		$session = $this->di->getSession();
		$adminUserInfo = $session->get('adminUserInfo');
		$memuList = $model->getMenuListByUserId($adminUserInfo['id']);
		$ActiveMenu = $this->di->getRequest()->getQuery('activemenu');
		if (empty($ActiveMenu)) {
			$ActiveMenu = $controllerName;
		}
		$ActiveMenuData = $memuModel->where("Code='$ActiveMenu'")->load();
		//var_dump($ActiveMenuData->toArray());
		foreach ($memuList as $key => $values) {
			if ($values['Id'] == $ActiveMenuData->Pid) {
				$values['active'] = 'active open';
			}
			foreach ($values['scat'] as $key2 => $value) {
				if ($value['Id'] == $ActiveMenuData->Id) {
					$value['active'] = 'active';
				}
				$values['scat'][$key2] = $value;
			}
			$memuList[$key] = $values;
		}
		$this->view->memuList = $memuList;
		$this->view->content = $content;
		$setting = $this->di->getConfig()->get("setting");
		$this->view->asstesUrl = $setting['asstesUrl'];
		//var_dump($this->view);
		echo $this->view->getRender('Layout',"base");
		$this->view->disable();
	}

	public function login() {
		echo $this->view->render('login');
	}

}
