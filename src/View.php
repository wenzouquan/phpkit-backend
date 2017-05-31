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
		$this->view->content = $content;
		echo $this->view->render('index');
	}

}
