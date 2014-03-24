<?php

class Admin_NewsController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
       
    }
    public function indexAction()
    {

    }
    
    public function listnewsAction(){
    	$this->_helper->layout()->disableLayout();

		//DataBases
		$newDb     = new Api_Model_DbTable_News();
		$newxRolDb = new Api_Model_DbTable_NewsRol();
		$rolDb     = new Api_Model_DbTable_Rol();
		//_______________________________________________
		
    	$eid = $this->sesion->eid;
		$oid = $this->sesion->oid;

		$order = 'created DESC';
		$allNews = $newDb->_getAll($where = null, $order);
		$this->view->allNews = $allNews;

		$lastNewid = $allNews[0]['newid'];
		$this->view->lastNewid = $lastNewid;

		$c = 0;
		foreach ($allNews as $new) {
			$where = array(	'eid'   => $eid,
							'oid'   => $oid,
							'newid' => $new['newid'] );

			$attrib = array('rid');

			$rol = $newxRolDb->_getFilter($where, $attrib);
			if ($rol) {
				$where = array(	'eid' => $eid,
								'oid' => $oid , 
								'rid' => $rol[0]['rid'] );
				$attrib = array('name', 'rid');
				$nameRol = $rolDb->_getFilter($where, $attrib);
				$namesRol[$c]['name'] = $nameRol[0]['name'];
				$namesRol[$c]['rid'] = $nameRol[0]['rid'];
			}else{
				$namesRol[$c]['name'] = "Todos";
			}
			$c++;
		}

		$this->view->namesRol = $namesRol;
    }

    public function newAction(){
    	$this->_helper->layout()->disableLayout();

    	$newDb = new Api_Model_DbTable_News();
    	$newxRolDb = new Api_Model_DbTable_NewsRol();
    	$formNew = new Admin_Form_New();
    	$this->view->formNew = $formNew;

    	$eid = $this->sesion->eid;
    	$oid = $this->sesion->oid;
    	
    	$newid = $this->_getParam('newid');
    	if ($newid) {
    		$where = array(	'newid' => $newid );
    		$new = $newDb->_getOne($where);
    		$where = array(	'eid' => $eid,
    						'oid' => $oid,
    						'newid' => $newid );
    		$newxRol = $newxRolDb->_getOne($where);
    		if ($newxRol) {
    			$new['rid'] = $newxRol['rid'];
    			$this->view->rid = $new['rid'];
    		}else{
    			$new['rid'] = 'Todos';
    		}
    		$formNew->populate($new);
    		$this->view->newid = $newid;
    		$this->view->imgNew = $new['img'];
    		$this->view->whySubmit = 'update';
    	}else{
    		$this->view->whySubmit = 'new';
    	}

    }

    public function savenewAction(){
    	$this->_helper->layout()->disableLayout();

    	$eid = $this->sesion->eid;
    	$oid = $this->sesion->oid;

    	$newDb = new Api_Model_DbTable_News();
    	$newRolDb = new Api_Model_DbTable_NewsRol();
    	$formNew = new Admin_Form_New();
    	if ($this->getRequest()->isPost()) {
           	$formData = $this->getRequest()->getPost();
            if ($formNew->isValid($formData)) {
	           	if ($formData['whySubmit'] == 'new') {
	            	if ($_FILES['img']['name']) {
	        			$rid = $formData['rid'];
		            	unset($formData['MAX_FILE_SIZE']);
		            	unset($formData['rid']);
		            	unset($formData['whySubmit']);
		            	unset($formData['newid']);
		            	unset($formData['stateRol']);
		            	$newid = $newDb->_save($formData);
		            	if ($newid) {
			            		$upload = new Zend_File_Transfer_Adapter_Http();
				                $filterRename = new Zend_Filter_File_Rename(array(
			                						'target' => '/srv/www/eundac/html/imgsNews/New'.$newid.'_'.$rid.'_'.$formData['type'],
			                					 	'overwrite' => false));

				                $upload->addFilter($filterRename);
				                $nombre_fichero = '/srv/www/eundac/html/imgsNews/New'.$newid.'_'.$rid.'_'.$formData['type'];
				                if (file_exists($nombre_fichero)) {
				                    unlink($nombre_fichero);
				                }
				                if ($upload->receive()) {
				                	$pk = array('newid' => $newid);
				                	$dataImg = array('img' => 'New'.$newid.'_'.$rid.'_'.$formData['type']);
				                	$newDb->_update($dataImg, $pk);
				                }else{
				                    echo "fallaArchivo";
				                }

		            		if ($rid != 'Todos') {
		            			$data = array(	'eid'   => $eid,
												'oid'   => $oid,
												'rid'   => $rid,
												'newid' => $newid );
		            			if ($newRolDb->_save($data)) {
		            				echo 'true';	
		            			}
		            		}else{
		            			echo 'true';
		            		}
	            		}
            		}else{
            			echo "Invalido";
            		}
	           	}else{
	           		$rid = $formData['rid'];
	           		$stateRol = $formData['stateRol'];
	           		$stateNewid = $formData['newid'];
	           		$pkNew    = array('newid' => $stateNewid);
	           		$pkNewRol = array(	'eid'   => $eid,
										'oid'   => $oid,
										'rid'   => $stateRol,
										'newid' => $stateNewid );
	            	unset($formData['MAX_FILE_SIZE']);
	            	unset($formData['rid']);
	            	unset($formData['whySubmit']);
	            	unset($formData['newid']);
	            	unset($formData['stateRol']);
	            	$newDb->_update($formData, $pkNew);

	           		if ($_FILES['img']['name']) {
	            		$upload = new Zend_File_Transfer_Adapter_Http();
		                $filterRename = new Zend_Filter_File_Rename(array(
	                						'target' => '/srv/www/eundac/html/imgsNews/New'.$formData['newid'].'_'.$rid.'_'.$formData['type'],
	                					 	'overwrite' => false));

		                $upload->addFilter($filterRename);
		                $nombre_fichero = '/srv/www/eundac/html/imgsNews/New'.$formData['newid'].'_'.$rid.'_'.$formData['type'];
		                if (file_exists($nombre_fichero)) {
		                    unlink($nombre_fichero);
		                }
		                if ($upload->receive()) {
		                	$pk = array('newid' => $newid);
		                	$dataImg = array('img' => 'New'.$formData['newid'].'_'.$rid.'_'.$formData['type']);
		                	$newDb->_update($dataImg);
		                }else{
		                    echo "fallaArchivo";
		                }
            		}
            		if ($rid != 'Todos' and $stateRol) {
            			$data = array('rid' => $rid );
            			if ($newRolDb->_update($data,  $pkNewRol)) {
            				echo 'true';	
            			}
            		}elseif ($rid != 'Todos' and !$stateRol) {
            			$data = array(	'eid'   => $eid,
										'oid'   => $oid,
										'rid'   => $rid,
										'newid' => $stateNewid );
	        			if ($newRolDb->_save($data)) {
	        				echo 'true';	
	        			}
            		}elseif ($rid == 'Todos' and $stateRol) {
            			if ($newRolDb->_delete($pkNewRol)) {
            				echo 'true';	
            			}
            		}else {
            			echo "true";
            		}
	           	}
        	}else{
        		echo "Invalido";
        	}
    	}
    }

    public function deleteAction(){
    	$this->_helper->layout->disableLayout();
    	$newDb = new Api_Model_DbTable_News();
    	$newxRolDb = new Api_Model_DbTable_NewsRol();

    	$newid = $this->_getParam('newid');
    	$rid = $this->_getParam('rid');

    	$eid = $this->sesion->eid;
    	$oid = $this->sesion->oid;

    	$pkNew = array('newid' => $newid);
    	if ($newDb->_delete($pkNew)) {
    		if ($rid) {
		    	$pkNewRol = array(	'eid' => $eid,
		    						'oid' => $oid,
		    						'newid' => $newid,
		    						'rid' => $rid );
		    	if ($newxRolDb->_delete($pkNewRol)) {
		    		echo "true";
		    	}
    		}else{
    			echo "true";
    		}
    	}
    }
}