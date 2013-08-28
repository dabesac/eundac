<?php

class Reportes_AdvancenoteController extends Zend_Controller_Action {

    public function init() {
                            
    }

    public function indexAction() 
    {

    	$where['eid']="20154605046";
        $where['oid']="1";
        $where['facid']="1901";
        $where['rid']="RC";
        
        /*
        $escid= $this->sesion->escid;
        $director= $this->sesion->esdirector;
        $sedid=$this->sesion->sedid;
        $rid=$this->sesion->rid;
        $nomescuela=$this->sesion->nom_escuela;
        
        $this->view->rid=$rid;
        $this->view->director=$director;  
        if($facid=='TODO' and $rid=='RC' || $rid=='VA' || $rid=='PD')

        { 
           //echo "es registros central";
           $dbfacultad = new Admin_Model_DbTable_Facultad();
           $facultad = $dbfacultad->_getTodasFacultades($eid,$oid);
           //print_r($facultad);
           $this->view->facultad=$facultad;

           // $dbescuela = new Admin_Model_DbTable_Escuela();
           // $escuela = $dbescuela->_getEscuelasXFacultadxsede($eid,$oid,$facu);
        }
        else
        {
           if($director=='S' and $rid='DC')
               {

              $this->view->escid=$escid;
              $this->view->sedid=$sedid;
              $this->view->nomescuela=$nomescuela;
              }
              else
              {
                        if($rid=='RF')
                        {
                         // echo "es de registros de facultad";
                         // $dbescuela = new Admin_Model_DbTable_Escuela();
                         // $escuela = $dbescuela->_getEscuelasXFacultadxsede($eid,$oid,$facid,$sedid);
                         $es=new Admin_Model_DbTable_Escuela();
                                if ($sedid=="1901") {
                                    if($facid=='2'){
                                    $escuela = $es->_getEscuelasXFacultadxsedeSecundaria($eid,$oid,$facid,$sedid);
                                       }
                                    else{
                                    $escuela=$es->_getEscuelasXFacultadXSede($eid, $oid,  $facid,$sedid);      
                                       }
                                }else{
                                    $escuela=$es->_getEscuelaXSede($eid,$oid,$sedid);
                                }
                        }
                        else
                        {                       
                            if($rid=='DF')
                            { 
                                //echo "es decano ";
                                $dbescuela = new Admin_Model_DbTable_Escuela();
                                $escuela = $dbescuela->_getEscuelasXFacultadxsede($eid,$oid,$facid,$sedid); 
                            }
                            else
                            { 
                                if ($uid=='VA' || $rid=='PD')
                                {
                                    // $uid='0514403019';
                                    //echo "es el vicerector";
                                    $dbfacultad = new Admin_Model_DbTable_Facultad();
                                    $facultad = $dbfacultad->_getTodasFacultades($eid,$oid);
                                    //print_r($facultad);
                                    $this->view->facultad=$facultad;
                                }

                            }
                     
                        }

                        
              }
        }

        if($escid=='2ESTY'){
              $escid1="2ESCY";
              $db_esc = new Admin_Model_DbTable_Escuela();
              $escuelas1 = $db_esc->_getEscuelaXSedeXyana($eid,$oid,$sedid,$escid);
              $escuelas2 = $db_esc->_getEscuelaXSedeXyana($eid,$oid,$sedid,$escid1);
              $escuela = array_merge($escuelas1, $escuelas2);
            
             } 

        $this->view->escuelas=$escuela;*/

                            
    }

}