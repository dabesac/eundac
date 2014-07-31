<?php

class SendmailController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

	public function indexAction(){
      //dataBases
      $facultyDb = new Api_Model_DbTable_Faculty();

      $eid = $this->sesion->eid;
      $oid = $this->sesion->oid;

      $where = array('eid'   => $eid, 
                     'oid'   => $oid, 
                     'state' => 'A');
      $dataFaculties = $facultyDb->_getFilter($where);
      $this->view->dataFaculties = $dataFaculties;
	}

   public function listschoolsAction(){
      $this->_helper->layout()->disableLayout();

      //DataBases
      $schoolDb = new Api_Model_DbTable_Speciality();

      $eid = $this->sesion->eid;
      $oid = $this->sesion->oid;

      $facid = base64_decode($this->_getParam('code'));

      $dataSchools = array();
      if ($facid != "TODO") {
         $where = array('eid'   => $eid,
                        'oid'   => $oid,
                        'facid' => $facid,
                        'state' => 'A' );
         $preDataSchools = $schoolDb->_getFilter($where);
         foreach ($preDataSchools as $c => $school) {
            if ($school['parent'] == '') {
               $dataSchools[$c]['escid'] = $school['escid'];
               $dataSchools[$c]['name']  = $school['name'];
            }
         }
      }else{
         $dataSchools = 'all';
      }
      $this->view->dataSchools = $dataSchools;
   }

   public function listspecialitiesAction(){
      $this->_helper->layout()->disableLayout();

      //dataBase
      $specialityDb = new Api_Model_DbTable_Speciality();

      $escid = base64_decode($this->_getParam('code'));

      $eid = $this->sesion->eid;
      $oid = $this->sesion->oid;

      $where = array('eid'    => $eid,
                     'oid'    => $oid,
                     'parent' => $escid,
                     'state'  => 'A' );

      $dataSpeciality = $specialityDb->_getFilter($where);

      $this->view->dataSpeciality = $dataSpeciality;
   }

   public function enviarAction(){
      $mail = new Zend_Mail();
      $mail->setBodyText('My Nice Test Text');
      $mail->setBodyHtml('My Nice <b>Test</b> Text');
      $mail->setFrom('somebody@example.com', 'Some Sender');
      $mail->addTo('somebody_else@example.com', 'Some Recipient');
      $mail->setSubject('TestSubject');
      $mail->send();
   }
}