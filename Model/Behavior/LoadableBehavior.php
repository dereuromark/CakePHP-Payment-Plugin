<?php
App::uses('ModelBehavior', 'Model');

class LoadableBehavior extends ModelBehavior {

	public $default = array(
		'amounts' => array(
			//'2' => '10',
			'15' => '15',
			'25' => '26',
			'50' => '52.50'
		),
		'default' => '25'
	);

	public $config = array();

	/**
	 * adjust configs like: $model->Behaviors-attach('GalleryStatistics', array('fields'=>array('xyz')))
	 */
	public function setup(Model $model, $config = array()) {
		$this->config[$model->alias] = $this->default;
		if ($configure = Configure::read('Loadable')) {
			$this->config[$model->alias] = array_merge($this->config[$model->alias], $configure);
		}
		$this->config[$model->alias] = array_merge($this->config[$model->alias], $config);

		$model->validate['charge_amount'] = array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
				'last' => true,
			),
			'inList' => array(
				'rule' => array('validateInList', array_keys($this->config[$model->alias]['amounts'])),
				'message' => 'valErrInvalidListElement',
			),
		);
		$this->model = $model;
	}

	public function validateInList(Model $model, $data, $list){
		$value = array_shift($data);
		return in_array($value, $list);
	}

	public function loadableAmounts() {
		return $this->config[$this->model->alias]['amounts'];
	}

	public function defaultAmount() {
		$default = $this->config[$this->model->alias]['default'];
		if (!empty($default)) {
			return $default;
		}
		$amounts = $this->loadableAmounts();
		$count = ceil(count($amounts) / 2);
		foreach ($amounts as $amount => $total) {
			$count--;
			if ($count <= 0 || count($amounts) <= 1) {
				return $amount;
			}
		}
	}

	public function finalAmount(Model $Model, $amount) {
		$amounts = $this->loadableAmounts();
		if (!isset($amounts[(string)intval($amount)])) {
			return false;
		}
		return $amounts[(string)intval($amount)];
	}

	public function loadableAmountsText(Model $Model, $currentAmount = null) {
		$res = array();
		foreach ($this->config[$this->model->alias]['amounts'] as $amount => $total) {
			if ($total > $amount) {
				$check = (float)$total;
			} else {
				$check = (float)$amount;
			}

			if ($currentAmount !== null && ($limit = (float)Configure::read('PrepaidAccount.limit')) > 0 && ($currentAmount + $check) > $limit) {
				continue;
			}
			$text = $amount . ' €';
			if ($total > $amount) {

				$text .= ' (+' . number_format((float)$total - (float)$amount, 2, ',', '.') . ' € Bonus)';
			}
			$res[$amount] = $text;
		}
		return $res;
	}

}
