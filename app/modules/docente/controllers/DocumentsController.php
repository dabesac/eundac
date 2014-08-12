<?php

class Docente_DocumentsController extends Zend_Controller_Action {

   public function init()
   {
      $sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      if (!$login->rol['module']=="docente"){
        $this->_helper->redirector('index','index','default');
      }
      $this->sesion = $login;   
   }
    
   public function indexAction(){
      $server = new Eundac_Connect_openerp();

      $fullName = $this->sesion->infouser['fullname'];
      $pid      = $this->sesion->pid;
      $oid      = $this->sesion->pid;
      $escid    = $this->sesion->escid;

      $dataDocente['fullName'] = $fullName;
      $dataDocente['pid'] = $pid;
      $dataDocente['uid'] = $uid;

      $day   = date('d');
      $month = date('m');
      $year  = date('Y');
      switch ($month) {
         case 1:
            $nameMonth = 'Enero';
            break;
         case 2:
            $nameMonth = 'Febrero';
            break;
         case 3:
            $nameMonth = 'Marzo';
            break;
         case 4:
            $nameMonth = 'Abril';
            break;
         case 5:
            $nameMonth = 'Mayo';
            break;
         case 6:
            $nameMonth = 'Junio';
            break;
         case 7:
            $nameMonth = 'Julio';
            break;
         case 8:
            $nameMonth = 'Agosto';
            break;
         case 9:
            $nameMonth = 'Septiembre';
            break;
         case 10:
            $nameMonth = 'Octubre';
            break;
         case 11:
            $nameMonth = 'Noviembre';
            break;
         case 12:
            $nameMonth = 'Diciembre';
            break;
         default:
            $nameMonth = ':o!! Un mes que no existe';
            break;
      }

      $dataDocente['fecha'] = $day.' de '.$nameMonth.' del '.$year;
      $this->view->dataDocente = $dataDocente;

      //Buscar tipo de documentos en el ERP para esta escuela...
      $query = array(
                     array('column'   => 'of_id',
                           'operator' => '=',
                           'value'    =>  $escid,
                           'type'     => 'string' )
                    );
      $idDepartment = $server->search('hr_department', $query);
      /*$attributes = array('id');
      $department = $server->read($idDepartment, $attributes, 'hr.department');*/
      print_r($query);
   }
}      