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
	public function convertUnderline($str, $ucfirst = true) {
		$str = explode('_', $str);
		foreach ($str as $key => $val) {
			$str[$key] = ucfirst($val);
		}

		if (!$ucfirst) {
			$str[0] = strtolower($str[0]);
		}
		return implode('', $str);
	}

	/***********递归生成目录**********/
	public function mk_dir($path) {
		//第1种情况，该目录已经存在
		if (is_dir($path)) {
			return;
		}
		//第2种情况，父目录存在，本身不存在
		if (is_dir(dirname($path))) {
			if (!is_writable(dirname($path))) {
				throw new \Exception("Permission denied in" . dirname($path), 1);
			}
			mkdir($path, 0777);
		}
		//第3种情况，父目录不存在
		if (!is_dir(dirname($path))) {
			$this->mk_dir(dirname($path)); //创建父目录
			mkdir($path, 0777);
		}
	}
	//生成文件
	/**
	 * 7      * 保存文件
	 * 8      *
	 * 9      * @param string $fileName 文件名（含相对路径）
	 * 10      * @param string $text 文件内容
	 * 11      * @return boolean
	 * 12      */
	function saveFile($fileName, $text, $overwrite = 0) {
		if (!$fileName || !$text) {
			return false;
		}
		if (!is_writable(dirname($fileName))) {
			throw new \Exception("Permission denied in" . dirname($fileName), 1);
		}
		if (is_file($fileName) && $overwrite === 0) {
			throw new \Exception($fileName . " exist", 1);

		}
		if ($fp = fopen($fileName, "w")) {
			if (@fwrite($fp, $text)) {
				fclose($fp);
				return true;
			} else {
				fclose($fp);
				return false;
			}
		}
		return false;
	}

	//生成CRUD
	function run($config = array()) {
		$config['table'] = $this->convertUnderline($config['table']);
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
		$this->saveFile($fileName, $content, $config['overwrite']);

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
		$this->saveFile($fileName, $content, $config['overwrite']);
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
		$this->saveFile($fileName, $content, $config['overwrite']);
	}

}
