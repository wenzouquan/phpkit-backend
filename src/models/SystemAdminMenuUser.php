<?php
namespace phpkit\backend\models;
class SystemAdminMenuUser extends \phpkit\base\BaseModel {
	protected $MenuIds;
	 public function initialize() {
		parent::initialize();
	}

	public function setMenuIds($MenuIds = array()) {
		$this->MenuIds = is_array($MenuIds) ? implode(",", $MenuIds) : $MenuIds;
	}

	public function getMenuIds() {
		return $this->MenuIds ? explode(",", trim($this->MenuIds, ",")) : array();
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
		$MenuIds = (array) explode(",", $data->MenuIds);
		foreach ($list as $key1 => $values) {
			foreach ($values['scat'] as $key => $value) {
				if (!in_array($value['Id'], $MenuIds)) {
					unset($list[$key1]['scat'][$key]);
				}
			}
		}
		return $list;
	}

}
