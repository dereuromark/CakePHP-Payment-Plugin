<?php
App::uses('PaymentAppController', 'Payment.Controller');
class PaymentMethodsController extends PaymentAppController {

	public $paginate = array('order' => array('PaymentMethod.sort' => 'DESC', 'PaymentMethod.name' => 'ASC'));

	public function beforeFilter() {
		parent::beforeFilter();
	}

/****************************************************************************************
 * USER functions
 ****************************************************************************************/

	public function index() {
		$paymentMethods = $this->paginate();
		$this->set(compact('paymentMethods'));
	}

	public function propose() {
		if ($this->Common->isPosted()) {
			$this->request->data['PaymentMethod']['alias'] = mb_strtolower($this->request->data['PaymentMethod']['name']);
			$this->PaymentMethod->Behaviors->load('Tools.Slugged', array('unique' => true, 'label' => 'alias', 'slugField' => 'alias', 'mode' => 'ascii', 'separator' => '_'));
			$this->PaymentMethod->create();
			$this->PaymentMethod->validate['rel_rate']['range']['rule'] = array('range', -100, 100);
			$this->PaymentMethod->validate['rel_rate']['range']['message'] = 'Please enter a number between -99.00 (-99%) and 99.00 (990%)';

			$this->PaymentMethod->set($this->request->data);
			if ($this->PaymentMethod->validates()) {
				$this->PaymentMethod->data['PaymentMethod']['rel_rate'] /= 100;
				$this->PaymentMethod->save(null, false);

				$var = $this->request->data['PaymentMethod']['name'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
	}

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	public function admin_index() {
		$paymentMethods = $this->paginate();
		$this->set(compact('paymentMethods'));
	}

	public function admin_view($id = null) {
		if (empty($id) || !($paymentMethod = $this->PaymentMethod->find('first', array('conditions' => array('PaymentMethod.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('paymentMethod'));
	}

	public function admin_add() {
		if ($this->Common->isPosted()) {
			if (empty($this->request->data['PaymentMethod']['alias'])) {
				$this->request->data['PaymentMethod']['alias'] = mb_strtolower($this->request->data['PaymentMethod']['name']);
			} else {
				$this->request->data['PaymentMethod']['alias'] = mb_strtolower($this->request->data['PaymentMethod']['alias']);
			}
			$this->PaymentMethod->Behaviors->load('Tools.Slugged', array('unique' => true, 'label' => 'alias', 'slugField' => 'alias', 'mode' => 'ascii', 'separator' => '_'));
			$this->PaymentMethod->create();
			if ($this->PaymentMethod->save($this->request->data)) {
				$var = $this->request->data['PaymentMethod']['name'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		} else {

			$this->request->data['PaymentMethod']['sort'] = 0;
			//$this->request->data['PaymentMethod']['set_rate'] = 0;
			//$this->request->data['PaymentMethod']['rel_rate'] = 0;
			$this->request->data['PaymentMethod']['active'] = 1;
		}
	}

	public function admin_edit($id = null) {
		$this->PaymentMethod->Behaviors->unload('Tools.DecimalInput');
		$this->PaymentMethod->Behaviors->load('Tools.DecimalInput', array('output' => true));

		if (empty($id) || !($paymentMethod = $this->PaymentMethod->find('first', array('conditions' => array('PaymentMethod.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if (empty($this->request->data['PaymentMethod']['alias'])) {
				$this->request->data['PaymentMethod']['alias'] = mb_strtolower($this->request->data['PaymentMethod']['name']);
			} else {
				$this->request->data['PaymentMethod']['alias'] = mb_strtolower($this->request->data['PaymentMethod']['alias']);
			}
			$this->PaymentMethod->Behaviors->load('Tools.Slugged', array('unique' => true, 'label' => 'alias', 'slugField' => 'alias', 'overwrite' => true, 'mode' => 'ascii', 'separator' => '_'));
			if ($this->PaymentMethod->save($this->request->data)) {
				$var = $this->request->data['PaymentMethod']['name'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $paymentMethod;
		}
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($paymentMethod = $this->PaymentMethod->find('first', array('conditions' => array('PaymentMethod.id' => $id), 'fields' => array('id', 'name'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->PaymentMethod->delete($id)) {
			$var = $paymentMethod['PaymentMethod']['name'];
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
