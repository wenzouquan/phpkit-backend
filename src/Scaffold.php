<?php
namespace phpkit\backend;
use phpkit\core\Phpkit as Phpkit;

class Scaffold {
	/**
	 * 显示后台模板
	 * @return
	 */
	function __construct($param = array()) {

		Phpkit::getDi();
		$viewDir = dirname(__FILE__) . '/Scaffold/';
		$this->view = Phpkit::getViews($viewDir);
		//模板渲染
		$this->assets = new \Phalcon\Assets\Manager();
		$this->view->phpStartTag = '<?php';
		$this->view->phpEndTag = '?>';

	}

	//生成CRUD
	function run($config = array()) {
		$config['table'] = \phpkit\helper\convertUnderline($config['table']);
		//var_dump($config);
		$dir = phpkitRoot . "/" . $config['appName'];
		$this->mk_dir($dir . "/app/views/" . $config['table']);
		$this->mk_dir($dir . "/app/controllers/");
		$config['controllersDir'] = $dir . "/app/controllers/";
		$config['viewsDir'] = $dir . "/app/views/" . $config['table'] . "/";
		$config['modelsDir'] = $dir . "/app/models/";
		$this->makeModel($config);
		$this->makeController($config);
		$this->makeViews($config);
		//var_dump($dir);
	}

	//生成Controller
	function makeController($config) {
		foreach ($config as $key => $value) {
			$this->view->$key = $value;
		}
		$content = $this->view->render('controller');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		$fileName = $config['controllersDir'] . $config['table'] . "Controller.php";
		\phpkit\helper\saveFile($fileName, $content, $config['overwrite']);

	}
	//生成views
	function makeViews($config) {
		foreach ($config as $key => $value) {
			$this->view->$key = $value;
		}
//		var_dump($config);
		//生成列表模板
		$content = $this->view->render('list');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		$fileName = $config['viewsDir'] . "index.phtml";
		$this->saveFile($fileName, $content, $config['overwrite']);
		//生成添加模板
		$content = $this->view->render('add');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		$fileName = $config['viewsDir'] . "add.phtml";
		\phpkit\helper\saveFile($fileName, $content, $config['overwrite']);
	}

	//生成model
	function makeModel($config) {
		foreach ($config as $key => $value) {
			$this->view->$key = $value;
		}
		$content = $this->view->render('model');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		$fileName = $config['modelsDir'] . $config['table'] . ".php";
		\phpkit\helper\saveFile($fileName, $content, $config['overwrite']);
	}

}
