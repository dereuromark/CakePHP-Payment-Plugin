<?php
App::uses('PaymentAppController', 'Payment.Controller');

class DiscountsController extends PaymentAppController {

	public $paginate = array('order' => array('Discount.created' => 'DESC'));

/****************************************************************************************
 * USER functions
 ****************************************************************************************/

	public function index() {
		$this->Discount->recursive = 0;
		$discounts = $this->paginate();
		$this->set(compact('discounts'));
	}

	public function view($id = null) {
		if (empty($id) || !($discount = $this->Discount->find('first', array('conditions' => array('Discount.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('discount'));
	}

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	/**
	 * later index/overview?
	 */
	public function admin_test() {
		if (!empty($this->request->data['DiscountCode'])) {
			# redeem
			if ($res = $this->Discount->redeem($this->request->data['DiscountCode']['code'])) {
				$this->Common->flashMessage(__('Done'), 'success');
			} elseif ($res === null) {
				$this->Common->flashMessage(__('Already redeemed'), 'warning');
			} else {
				$this->Common->flashMessage(__('Error'), 'error');
			}
			$this->redirect(array('action' => 'test'));

		} elseif (!empty($this->request->data['Discount'])) {
			$value = $this->request->data['Discount']['amount'];
			# check
			if ($check = $this->Discount->check($this->request->data['Discount']['code'], $value)) {
				$new = Discount::calculate($value, $check['Discount']);
				$this->Common->flashMessage(__('Wert von ' . $value . ' auf ' . $new . ' gesenkt'), 'success');
			} else {
				$this->Common->flashMessage(__('Error'), 'error');
			}

		}

		if (empty($this->request->data)) {
			$this->request->data['Discount']['amount'] = 10;
		}
	}

	public function admin_index() {
		$this->Discount->recursive = 0;
		$discounts = $this->paginate();
		$this->set(compact('discounts'));
	}

	public function admin_view($id = null) {
		if (empty($id) || !($discount = $this->Discount->find('first', array('contain' => array('DiscountCode'), 'conditions' => array('Discount.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('discount'));
	}

	public function admin_add() {
		if ($this->Common->isPosted()) {
			$this->Discount->create();
			$this->Discount->Behaviors->unload('Tools.Jsonable');
			$this->Discount->Behaviors->load('Tools.Jsonable', array('fields' => 'details', 'input' => 'param'));

			if ($this->Discount->save($this->request->data)) {
				$var = $this->request->data['Discount']['name'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		} else {
			$this->request->data['Discount']['factor'] = 0;
			$this->request->data['Discount']['amount'] = 0.0;
			$this->request->data['Discount']['min'] = 0;
			$this->request->data['Discount']['validity_days'] = 0;
		}
	}

	public function admin_edit($id = null) {
		$this->Discount->Behaviors->unload('Tools.Jsonable');
		$this->Discount->Behaviors->load('Tools.Jsonable', array('fields' => 'details', 'input' => 'param', 'output' => 'param'));

		if (empty($id) || !($discount = $this->Discount->find('first', array('conditions' => array('Discount.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->Discount->save($this->request->data)) {
				$var = $this->request->data['Discount']['name'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $discount;
		}
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($discount = $this->Discount->find('first', array('conditions' => array('Discount.id' => $id), 'fields' => array('id', 'name'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$var = $discount['Discount']['name'];

		if ($this->Discount->delete($id)) {
			$this->Common->flashMessage(__('record del %s done', h($var)), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Common->flashMessage(__('record del %s not done exception', h($var)), 'error');
		$this->Common->autoRedirect(array('action' => 'index'));
	}

/****************************************************************************************
 * protected/interal functions
 ****************************************************************************************/

/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}
