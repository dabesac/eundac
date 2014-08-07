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

   public function sendAction(){
      //DataBases
      $userDb   = new Api_Model_DbTable_Users();
      $personDb = new Api_Model_DbTable_Person();

      $this->_helper->layout()->disableLayout();
      /*$eid = $this->sesion->eid;
      $oid = $this->sesion->oid;

      $formData = $this->getRequest()->getPost();
      if (  $formData['selectFaculty'] != '' 
            and $formData['selectDestinatarios'] != '' 
            and $formData['tituloCorreo'] != ''
            and $formData['cuerpoCorreo'] != '') {
         if ($formData['selectSpecilaty']) {
            $search = 'escid';
            $escid  = base64_decode($formData['selectSpecilaty']);
         }elseif ($formData['selectSchool']) {
            $search = 'escid';
            $escid  = base64_decode($formData['selectSchool']);
         }else{
            $escid  = base64_decode($formData['selectFaculty']);
            $cantCF = strlen($escid);
            $search = 'left(escid, '.$cantCF.')';
         }

         if ($escid == 'TODO') {
            $where = array('eid'   => $eid,
                           'oid'   => $oid,
                           'rid'   => $formData['selectDestinatarios'],
                           'state' => 'A' );
         }else{
            $where = array('eid'   => $eid,
                           'oid'   => $oid,
                           $search => $escid,
                           'rid'   => $formData['selectDestinatarios'],
                           'state' => 'A' );
         }

         $attrib = array('pid');
         $preDataUsers = $userDb->_getFilter($where, $attrib);
         $cPersons = 0;
         foreach ($preDataUsers as $user) {
            $where = array('eid' => $eid,
                           'pid' => $user['pid'] );
            $attrib = array('last_name0', 'last_name1', 'first_name', 'mail_person');

            $preDataPerson = $personDb->_getFilter($where, $attrib);
            if ($preDataPerson[0]['mail_person']) {
               $mail = $preDataPerson[0]['mail_person'];
               $tamCorreo = strlen($mail);
               $isMail = 'no';
               for ($i=0; $i < $tamCorreo; $i++) { 
                  if ($mail[$i] == '@') {
                     $isMail = 'yes';
                  }
               }
               if ($isMail == 'yes') {
                  $dataPerson[$cPersons]['fullName'] = $preDataPerson[0]['last_name0'].' '.$preDataPerson[0]['last_name1'].' '.$preDataPerson[0]['first_name'];
                  $dataPerson[$cPersons]['mail']     = $preDataPerson[0]['mail_person'];
                  $cPersons++;
               }
            }
         }

         //Enviar Correo :D
         print_r($formData);
         /*$mail->addTo('poolrojasm@gmail.com', 'Pol');
         $mail->addTo('sanliscorazon@gmail.com', 'Sandro');
         foreach ($dataPerson as $person) {
            $mail->addTo($person['mail'], $person['fullName']);
         }*/
         /*if ($mail->send()) {
            //$result = array('success' => 1);
         }else{
            //$result = array('success' => 0);
         }
         print(json_encode($result));
      }*/
      $config = array(  'ssl'      => 'tls', 
                        'port'     => 587, 
                        'auth'     => 'login', 
                        'username' => 'informatica@undac.edu.pe', 
                        'password' => '1nf0rm4t1c4000123' );

      $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config); 
      Zend_Mail::setDefaultTransport($mailTransport);
 
      $mail = new Zend_Mail();
      $mail->setBodyText('This is the text of the mail.');
      $mail->addTo('stayner93@gmail.com', 'Liam Klyneker');
      $mail->setSubject('TestSubject');
      if ($mail->send()) {
         print_r(1);
      }else{
         print_r(0);
      };
   }
}