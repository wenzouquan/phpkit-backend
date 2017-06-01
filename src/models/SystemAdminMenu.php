<?php
namespace phpkit\backend\models;
class SystemAdminMenu extends \phpkit\core\BaseModel {

	protected $GroupName;
	protected $GroupList;
	public function initialize() {
		parent::initialize();
	}

	//所有菜单
	public function getMenuList() {
		$lists = $this->order("OrderBy")->select()->toArray();
		$groupList = array();
		$this->GroupList = $this->getGroupList();
		foreach ($this->GroupList as $key => $value) {
			$groupList[$value['Id']] = $value;
		}
		foreach ($lists as $key => $value) {
			$value['ActiveMenu'] = $value['ActiveMenu'] ? $value['ActiveMenu'] : $value['Code'];
			$value['Url'] = strpos($value['Url'], "?") ? ($value['Url'] . "&activemenu=" . $value['ActiveMenu']) : ($value['Url'] . "?activemenu=" . $value['ActiveMenu']);
			$groupList[$value['Pid']]['scat'][] = $value;
		}
		return $groupList;

	}

	public function getGroupName() {
		$model = new SystemAdminMenuGroup();
		$res = $model->load($this->Pid);
		return $res->Name;
	}

	public function getGroupList() {
		$model = new SystemAdminMenuGroup();
		$res = $model->order("OrderBy")->select();
		return $res->toArray();
	}
}
