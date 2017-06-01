<?php
namespace phpkit\backend\models;
class SystemAdminMenuUser extends \phpkit\core\BaseModel {
	public function initialize() {
		parent::initialize();
	}

	//用户菜单
	public function getMenuListByUserId($UserId = "") {
		$model = new SystemAdminMenu();
		$list = $model->getMenuList();
		if (empty($UserId)) {
			return $list;
		} else {
			$data = $this->load($UserId);
		}
		return $list;
	}

}
