<?php 

class Rcentral_PeriodsController extends Zend_Controller_Action {

    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="rcentral"){
            $this->_helper->redirector('index','index','default');
        }
        $this->sesion = $login;
      }


    public function indexAction() {
       // $this->_helper->redirector("Listar");
    }


    public function listarAction() {
        $this->_helper->layout()->disableLayout();
        $where['eid']= $this->sesion->eid;
        $where['oid']= $this->sesion->oid;
        $anio=$this->_getParam('anio');            
        $where['year'] = substr($anio, 2, 3);
        $periodos = new Api_Model_DbTable_Periods();
        $lper=$periodos->_getPeriodsxYears($where);
        //print_r($lper); 
        $this->view->lper = $lper; 
    }

    public function modificarAction() {
        $this->_helper->layout()->disableLayout();
        $where['eid']= $this->sesion->eid;
        $where['oid']= $this->sesion->oid;
        $uid= $this->sesion->uid;
        $where['perid']=$this->_getParam('perid');
        $periodo = new Api_Model_DbTable_Periods();
        $peri=$periodo->_getFilter($where);
        // print_r($peri);
        $estado=$peri[0]['state'];
        $this->view->estado = $estado; 
        
        $form=new Rcentral_Form_Periods();
        $form->populate($peri[0]);
        $this->view->form=$form;
      	if ($this->getRequest()->isPost())
       	{
        	$frmdata=$this->getRequest()->getPost();
        	unset($frmdata['guardar']);
  
          $frmdata['eid']=$where['eid'];
          $frmdata['oid']=$where['oid'];
          $frmdata['updated']=date("Y-m-d h:m:s");
          $frmdata['modified']=$uid;
          $frmdata['register']=$uid;
       
            $str=array();
            $str="eid='".$where['eid']."' and oid='".$where['oid']."' and perid='".$where['perid']."'";
            $dbper=new Api_Model_DbTable_Periods();
            if($per=$dbper->_update($frmdata,$str))
              { 
             ?>
                      <script>
                      alert('se modifico el registro');
                       </script>
              <?php 
              }          
      }
    }
    public function nuevoAction() {
        $this->_helper->layout()->disableLayout();
        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $uid= $this->sesion->uid;

        $form=new Rcentral_Form_Periods();
        $this->view->form=$form;

        if ($this->getRequest()->isPost()){
            $frmdata=$this->getRequest()->getPost();
            if ($form->isValid($frmdata)) {
                unset($frmdata['guardar']);      
                $frmdata['eid']=$eid;
                $frmdata['oid']=$oid;
                $frmdata['updated']=date("Y-m-d h:m:s");
                $frmdata['modified']=$uid;
                $frmdata['register']=$uid;
                $frmdata['state']='T';

                $dbper=new Api_Model_DbTable_Periods();
                if($per=$dbper->_save($frmdata)){   ?>
                <script>
                        alert('Se agregó un nuevo periodo');
                  </script>
                <?php 
                } 
                else{
                    $form->populate($frmdata);
                }
            }
        }
    }


public function eliminarAction()
{
  try 
  {
      $this->_helper->layout()->disableLayout();

      $where['perid'] =$this->_getParam("perid");  
      $where['eid']=$this->sesion->eid;
      $where['oid']=$this->sesion->oid;
      $usuario = new Api_Model_DbTable_Periods();
     
      if($usuario->_delete($where))
         { ?> <script>
               alert('se elimino el periodo');
                </script>
            <?php $this->_helper->redirector('index',"periods",'rcentral');
         } 
      else
        { ?> <script>
                alert('No se puede eliminar este periodo por que tiene ');
             </script>
      <?php }
  }
  catch (Exception $ex)
  {

  }
}

}