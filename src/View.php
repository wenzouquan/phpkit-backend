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
		echo $this->view->render('index');
	}

	public function login() {
		echo $this->view->render('login');
	}

}
