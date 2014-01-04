<?php
App::uses('PaymentAppController', 'Payment.Controller');

class DiscountCodesController extends PaymentAppController {

	public $paginate = array('order' => array('DiscountCode.created' => 'DESC'));

	public $helpers = array('Payment.Discount');

/****************************************************************************************
 * USER functions
 ****************************************************************************************/

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	public function admin_index() {
		$this->DiscountCode->recursive = 0;
		$discountCodes = $this->paginate();
		$this->set(compact('discountCodes'));
	}

	public function admin_view($id = null) {
		$this->DiscountCode->recursive = 0;
		if (empty($id) || !($discountCode = $this->DiscountCode->find('first', array('conditions' => array('DiscountCode.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('discountCode'));
	}

	public function admin_add($did = null) {
		if ($this->Common->isPosted()) {
			$this->DiscountCode->create();
			if ($this->DiscountCode->save($this->request->data)) {
				$var = $this->request->data['DiscountCode']['name'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		} else {
			$this->request->data['DiscountCode']['code'] = $this->DiscountCode->generateCode();
			if ($did) {
				$this->request->data['DiscountCode']['discount_id'] = $did;
				$discount = $this->DiscountCode->Discount->find('first', array('conditions' => array('id' => $did)));
				if (!empty($discount['Discount']['validity_days'])) {
					$this->request->data['DiscountCode']['validity_days'] = $discount['Discount']['validity_days'];
				}
			}
		}
		$discounts = $this->DiscountCode->Discount->find('list');
		$this->set(compact('discounts'));
	}

	public function admin_add_multiple($did = null) {
		if (empty($did) || !($discount = $this->DiscountCode->Discount->find('first', array('conditions' => array('Discount.id' => $did))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('controller' => 'discounts', 'action' => 'index'));
		}

		if ($this->Common->isPosted()) {
			$this->DiscountCode->set($this->request->data);
			if ($this->DiscountCode->validates()) {
				$codes = array();
				$errors = 0;
				for ($i = 0; $i < (int) $this->request->data['DiscountCode']['quantity']; $i++) {
					$this->DiscountCode->create();
					$data = array(
						'discount_id' => $did,
						'code' => $this->DiscountCode->generateCode()
					);
					if (!$this->DiscountCode->save($data)) {
						$i--;
						$errors++;
					} else {
						$codes[] = $this->DiscountCode->id;
					}
					if ($errors > 100) {
						throw new InternalErrorException('Batch processing of new discount codes not working');
					}
				}
				if ($errors > 0) {
					$this->Common->flashMessage('Duplikate verhindert', 'warning');
				}
				if (!empty($codes)) {
					$this->Session->write('DiscountCode.batch', $codes);
					$this->Common->flashMessage(__('%s codes generated', count($codes)), 'success');
					$this->Common->postRedirect(array('action' => 'add_multiple', $did));
				}

			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}

		if ($batch = $this->Session->read('DiscountCode.batch')) {
			$codes = $this->DiscountCode->find('all', array('conditions' => array('DiscountCode.id' => $batch)));
			$this->set(compact('codes'));
		}

		$this->set(compact('discount'));
	}

	public function admin_add_reset($did = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		$this->Session->delete('DiscountCode.batch');
		$this->Common->flashMessage(__('Reset successful'), 'success');
		$this->Common->postRedirect(array('action' => 'add_multiple', $did));
	}

	public function admin_edit($id = null) {
		if (empty($id) || !($discountCode = $this->DiscountCode->find('first', array('conditions' => array('DiscountCode.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->DiscountCode->save($this->request->data)) {
				$var = $this->request->data['DiscountCode']['name'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $discountCode;
		}
		$discounts = $this->DiscountCode->Discount->find('list');
		$this->set(compact('discounts'));
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($discountCode = $this->DiscountCode->find('first', array('conditions' => array('DiscountCode.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}

		if ($this->DiscountCode->delete($id)) {
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
