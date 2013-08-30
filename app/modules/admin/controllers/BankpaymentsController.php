<?php

class Admin_BankpaymentsController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		//$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="admin"){
    		//$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    
    }
    
    public function indexAction()
    {
   	    
    }

    public function listAction()
    {
        $this->_helper->layout()->disableLayout();
        $oid=$this->sesion->oid;
        $eid=$this->sesion->eid;
        $this->view->eid=$eid;
        $this->view->oid=$oid;
        $fini=$this->_getParam("fini");
        $ffin=$this->_getParam("ffin");
        $uid=$this->_getParam("usuario");
        $f_ini= split("-",$fini);
        $f_fin= split("-",$ffin);
        $fini=$f_ini[2]."-".$f_ini[1]."-".$f_ini[0];
        $ffin=$f_fin[2]."-".$f_fin[1]."-".$f_fin[0];
        $dat=new Api_Model_DbTable_Bankreceipts();
        if ($uid=="") {
            $drec=$dat->_getBankreceiptsBetween2Dates($fini,$ffin);

        }else{
            $drec=$dat->_getBankreceiptsBetween2DatesXuid($fini,$ffin,$uid);
         }
        if ($drec) {
            $this->view->drecibo=$drec;
        }else{
            $this->view->nodata="1";
        }

    }

    public function assignAction()
    {
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $perid=$this->_getParam('perid');
        $uid=$this->_getParam('uid');
        $operation=$this->_getParam('num_operacion');
        $data=new Api_Model_DbTable_Bankreceipts();
        $where['operation']=$operation;
        $where['code_student']=$uid;
        $where['perid']=$perid;
        $datrec=$data->_getFilter($where);
        $this->view->datarec=$datrec;
        $perso=new Api_Model_DbTable_Users();
        $dato['eid']=$eid;
        $dato['oid']=$oid;
        $dato['uid']=$uid;
        $dataper=$perso->_getUserXUid($dato);
         // $nombres=$dataper[0]['nombrecompleto'];
        $this->view->nomalum=$dataper;
        $perid=substr($perid, 0,2);
        $dperi=new Api_Model_DbTable_Periods();
        $datp['eid']=$eid;
        $datp['oid']=$oid;
        $datp['year']=$perid;
        $lper1=$dperi->_getPeriodsxYears($datp);
        $datp1['eid']=$eid;
        $datp1['oid']=$oid;
        $datp1['year']=$perid + 1;
        $lper2=$dperi->_getPeriodsxYears($datp1);
        $lper = array_merge($lper1, $lper2);
        $this->view->lper=$lper;

    }

    public function verifyAction()
    {
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $uid=$this->_getParam('uid');
        $perid=$this->_getParam('perid');
        $pmat=new Api_Model_DbTable_Payments();
        $dato['eid']=$eid;
        $dato['oid']=$oid;
        $dato['uid']=$uid;
        $dato['perid']=$perid;

        $pagmat=$pmat->_getFilter($dato);
        if ($pagmat) {
            $usu=new Api_Model_DbTable_Users();
            $dato1['eid']=$eid;
            $dato1['oid']=$oid;
            $dato1['uid']=$uid;
            $datusu=$usu->_getUserXUid($dato1);
            $this->view->datusu=$datusu;
        }
    }

    public function assignedAction()
    {
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $auid=$this->_getParam('auid');
        $aperid=$this->_getParam('aperid');
        $cod_mat=$this->_getParam('cod_mat');
        $perid=$this->_getParam('perid');
        $num_ope=$this->_getParam('num_ope');
        $data=new Api_Model_DbTable_Bankreceipts();
        $dato2['code_student']=$cod_mat;
        $dato2['operation']=$num_ope;
        $dato2['perid']=$perid;
        $datrec=$data->_getFilter($dato2);

        $pmat=new Api_Model_DbTable_Payments();
        $dato1['eid']=$eid;
        $dato1['oid']=$oid;
        $dato1['uid']=$auid;
        $dato1['perid']=$aperid;

        $pagmat=$pmat->_getFilter($dato1);
        
       //print_r($datrec);
        if($pagmat){
        //     
            $dato['uid']=$pagmat[0]['uid'];
            $dato['escid']=$pagmat[0]['escid'];
            $dato['pid']=$pagmat[0]['pid'];
            $dato['subid']=$pagmat[0]['subid'];
            $dato['oid']=$pagmat[0]['oid'];
            $dato['eid']=$pagmat[0]['eid'];
            $dato['perid']=$pagmat[0]['perid'];
            $dato['code_student']=$datrec[0]['code_student'];
            $dato['operation']=$datrec[0]['operation'];
            $dato['pcid']=$datrec[0]['concept'];
            $dato['date_payment']=$datrec[0]['payment_date'];
            $dato['amount']=$datrec[0]['amount'];
            $dato['register']=$this->sesion->uid;;
            // print_r($datrec);
            $reg=new Api_Model_DbTable_PaymentsDetail();
            if ($reg->_save($dato)) {
                $recibo['processed']="S";
                $recibo['code_rect']=$auid;
                $str['operation']=$num_ope;
                $str['code_student']=$cod_mat;
                if ($data->_update($recibo,$str)) {
                    ?>
                    <script type="text/javascript">
                        alert("Se asigno el pago!!!");

                    </script>

                    <?php

                }
            }
        }else{
                ?>
                <script type="text/javascript">
                    alert("El Alumno no se encuentra registrado, debe ingresar al sistema para realizar su pre-matricula.");
                    return false;
                </script>
                <?php
         }
    }

    public function removeAction(){
        $this->_helper->layout()->disableLayout();
        $uid = $this->_getParam('uid');
        $operation = $this->_getParam('num_operacion');
        $pag = new Api_Model_DbTable_PaymentsDetail();
        $pk['uid']=$uid;
        $pk['operation']=$operation;
        if ($pag->_delete($pk)) {
            $recban= new Api_Model_DbTable_Bankreceipts();
            $str['operation']=$operation ;
            $str['code_student']=$uid;
            $datos['processed']="N";
            if ($recban->_update($datos,$str)) { 
                $this->_helper->redirector("index");
            }
        }
    }

   

}