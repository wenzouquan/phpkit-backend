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
		
		$config['controllerName'] = str_replace("_", "-", $config['table']);
		$config['table']=$config['modelName']?$config['modelName']:$config['table'];
		$config['table'] = \phpkit\helper\convertUnderline($config['table']);
		$config['renderDir']=$config['frame']==2?'tp5/':'';
		//var_dump($config);
		$dir =  $config['appName'];//生成到哪个目录

		if($config['frame']==2){//tp5
			\phpkit\helper\mk_dir($dir . "/view/" . lcfirst($config['table']));
			\phpkit\helper\mk_dir($dir . "/controller/");
			$config['controllerName'] = $dir."/controller/" . $config['table'] . ".php";
			$config['viewsDir'] = $dir . "/view/" . lcfirst($config['table']) . "/";
			$config['modelsDir'] = $dir . "/model/";
			$config['viewsIndexName'] = $config['viewsDir'] . "index.html";
			$config['viewsAddName'] = $config['viewsDir'] . "add.html";
		}else{
			\phpkit\helper\mk_dir($dir . "/views/" . $config['table']);
			\phpkit\helper\mk_dir($dir . "/controllers/");
			//$config['controllerName'] = $config['table'] ;
			$config['controllerFileName'] = $dir ."/controllers/" . $config['table'] . "Controller.php";
			$config['viewsDir'] = $dir . "/views/" . $config['table'] . "/";
			$config['modelsDir'] = $dir . "/models/";
			$config['viewsIndexName'] = $config['viewsDir'] . "index.phtml";
			$config['viewsAddName'] = $config['viewsDir'] . "add.phtml";
		}
		//$config['overwrite']=1;
		var_dump($config);
		
		$this->makeModel($config);
		$this->makeController($config);
		$this->makeViews($config);
		echo 'success:'.'/'.$config['table'].'/index';
		//var_dump($dir);
	}

	//生成Controller
	function makeController($config) {
		foreach ($config as $key => $value) {
			$this->view->$key = $value;
		}
		$content = $this->view->render($config['renderDir'].'controller');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		//$fileName = $config['controllersDir'] . $config['table'] . "Controller.php";
		//var_dump($config['controllerName']);exit();
		\phpkit\helper\saveFile($config['controllerFileName'], $content, $config['overwrite']);

	}
	//生成views
	function makeViews($config) {
		foreach ($config as $key => $value) {
			$this->view->$key = $value;
		}
//		var_dump($config);
		//生成列表模板
		$content = $this->view->render($config['renderDir'].'list');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		//$fileName = $config['viewsDir'] . "index.phtml";
		\phpkit\helper\saveFile($config['viewsIndexName'], $content, $config['overwrite']);
		//生成添加模板
		$content = $this->view->render($config['renderDir'].'add');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		//$fileName = $config['viewsDir'] . "add.phtml";
		\phpkit\helper\saveFile($config['viewsAddName'], $content, $config['overwrite']);
	}

	//生成model
	function makeModel($config) {
		foreach ($config as $key => $value) {
			$this->view->$key = $value;
		}
		$content = $this->view->render($config['renderDir'].'model');
		$content = str_replace("<php>", $this->view->phpStartTag, $content);
		$content = str_replace("</php>", $this->view->phpEndTag, $content);
		$fileName = $config['modelsDir'] . $config['table'] . ".php";
		\phpkit\helper\saveFile($fileName, $content, $config['overwrite']);
	}

}
