<?php
namespace phpkit\backend\models;
class SystemStoreUser extends \phpkit\base\BaseModel {
	protected $pasttimeDate;
	protected $RoleId;
	protected $StoreInfo;
	public $callbackForRole; //保存成功之后保存权限

	public function initialize() {
		parent::initialize();
	}
	public function getPasttimeDate() {
		return date("Y-m-d", $this->pasttime);
	}

	public function getStoreInfo() {
		$model = new SystemStore();
		$store = $model->load($this->store_id);
		$data = array();
		if ($store) {
			$data = $store->toArray();
		}
		return $data;
	}

	public function getRoleId() {
		$id = $this->id; 
		$model = new \phpkit\auth\models\AddonAuthUser();
		$roles = $model->load($id);
		if ($roles) {
			return $roles->RoleId;
		}
	}

	public function afterSave() {
		parent::afterSave();
		//保存权限
		$data = $this->callbackForRole->fire();
	}

	public function setRoleId($RoleId) {
		$this->callbackForRole = new \phpkit\callback\Callback();
		if (!empty($this->id)) {
			$this->callbackForRole->fire();
		}
		$_this = $this;
		$this->callbackForRole->add(function ($params) use ($_this, $RoleId) {
			$model = new \phpkit\auth\models\AddonAuthUser();
			$model->UserId = $_this->id;
			$model->RoleId = $RoleId;
			$bool = $model->save();
			if ($bool == false) {
				foreach ($model->getMessages() as $message) {
					$messages[] = $message;
				}
				if (is_array($messages)) {
					$msg = implode(",", $messages);
				}
				return $msg;
			}
		});
	}

}
