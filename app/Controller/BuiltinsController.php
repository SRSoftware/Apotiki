<?php
App::uses('AppController', 'Controller');
/**
 * Builtins Controller
 *
 * @property Builtin $Builtin
*/
class BuiltinsController extends AppController {

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->Builtin->recursive = 0;
		$this->set('builtins', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->Builtin->id = $id;
		if (!$this->Builtin->exists()) {
			throw new NotFoundException(__('Invalid builtin'));
		}
		$this->set('builtin', $this->Builtin->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		$data=$this->Session->read('data');
		if (isset($data['place_id'])) { // check, whether we already know, where to take the item from
			$this->Session->delete('data');
			$this->Session->delete('return');
			print("data for builtin:\n");
			print_r($data); die();			
			
			
			$this->Builtin->create();
			if ($this->Builtin->save($this->request->data)) {
				$this->Session->setFlash(__('The builtin has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The builtin could not be saved. Please, try again.'));
			}
		} else { // if we don't know, yet, where the item is taken from: 
			if ($this->request->is('post')) { // if we already chose the items: forward to the place selection
				$this->Session->write('data',$this->request->data['Builtin']);
				$this->Session->write('return',array('controller'=>'builtins','action'=>'add'));
				$this->redirect(array('controller'=>'item_places','action'=>'select'));
				die();
			} // ask for items to combine
			$items = $this->Builtin->Item->find('list');
			$includedItems = $this->Builtin->IncludedItem->find('list');
			$this->set(compact('items', 'includedItems'));
		}
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->Builtin->id = $id;
		if (!$this->Builtin->exists()) {
			throw new NotFoundException(__('Invalid builtin'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Builtin->save($this->request->data)) {
				$this->Session->setFlash(__('The builtin has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The builtin could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Builtin->read(null, $id);
		}
		$items = $this->Builtin->Item->find('list');
		$includedItems = $this->Builtin->IncludedItem->find('list');
		$this->set(compact('items', 'includedItems'));
	}

	/**
	 * delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Builtin->id = $id;
		if (!$this->Builtin->exists()) {
			throw new NotFoundException(__('Invalid builtin'));
		}
		if ($this->Builtin->delete()) {
			$this->Session->setFlash(__('Builtin deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Builtin was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
