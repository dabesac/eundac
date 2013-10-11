<?php

class Report_ConsolidatedController extends Zend_Controller_Action {

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
        try{
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $rid= $this->sesion->rid;
            $this->view->rid=$rid;
            $facid= $this->sesion->facid;
            $sedid=$this->sesion->sedid;
            $escid=$this->sesion->escid; 
            $esdirector=$this->sesion->esdirector; 
            $this->view->esdirector = $esdirector;

             if ($rid=='RF')
             {
                //     $es = new Admin_Model_DbTable_Escuela();
                //    if ($sedid=="1901") {
                //       if($facid=='2'){
                //        $lescuelas = $es->_getEscuelasXFacultadxsedeSecundaria($eid,$oid,$facid,$sedid);
                //           }
                //       else{
                //        $lescuelas=$es->_getEscuelasXFacultadXSede($eid, $oid,  $facid,$sedid);   
                //       }
                //   }else{
                //     $lescuelas=$es->_getEscuelaXSede($eid,$oid,$sedid);
                //      }
                // $this->view->lescuelas=$lescuelas;
             }

             if ($rid=='DC' && $esdirector=='S')
            {
               // $db_esc = new Admin_Model_DbTable_Periodoscursos();
               //  $lescuelas= $db_esc->_getEscuelasXActual($eid,$oid,$facid,$escid,$sedid);
               //  $this->view->lescuelas=$lescuelas;
            }
            
            if ($rid=='RC' || $rid=='VA' || $rid=='PD')
            {
                // $db_esc = new Admin_Model_DbTable_Escuela();
                // $this->view->lescuelas= $db_esc->_getEscuelasXOrganizacion($eid,$oid);
            } 

            if($escid=='2ESTY'){
              // $escid1="2ESCY";
              // $db_esc = new Admin_Model_DbTable_Escuela();
              // $escuelas1 = $db_esc->_getEscuelaXSedeXyana($eid,$oid,$sedid,$escid);
              // $escuelas2 = $db_esc->_getEscuelaXSedeXyana($eid,$oid,$sedid,$escid1);
              // $escuela = array_merge($escuelas1, $escuelas2);
              // $this->view->lescuelas=$escuela;
             } 
        }catch(Exception $ex){
              print $ex->getMessage();
        }
    } 
}