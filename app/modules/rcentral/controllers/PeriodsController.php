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
     //   $this->_helper->redirector("Listar");
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
        
        // $f_ini_clases=$peri[0]['class_start_date'];
        // $tfiniclases =split("-",$f_ini_clases);
        // $f_ini_clases = $tfiniclases[2]."/".$tfiniclases[1]."/".$tfiniclases[0];
        // $peri['f_ini_clases']=$f_ini_clases;
        // $f_fin_clases=$peri['f_fin_clases'];
        // $tffinclases =split("-",$f_fin_clases);
        // $f_fin_clases = $tffinclases[2]."/".$tffinclases[1]."/".$tffinclases[0];
        // $peri['f_fin_clases']=$f_fin_clases;
        // $f_ini_mat=$peri['f_ini_mat'];
        // $tfinimat =split("-",$f_ini_mat);
        // $f_ini_mat = $tfinimat[2]."/".$tfinimat[1]."/".$tfinimat[0];
        // $peri['f_ini_mat']=$f_ini_mat;
        // $f_fin_mat=$peri['f_fin_mat'];
        // $tffinmat =split("-",$f_fin_mat);
        // $f_fin_mat =$tffinmat[2]."/".$tffinmat[1]."/".$tffinmat[0];
        // $peri['f_fin_mat']=$f_fin_mat;
        // $f_ipp_not=$peri['f_ipp_not'];
        // $tfippnot =split("-",$f_ipp_not);
        // $f_ipp_not =$tfippnot[2]."/".$tfippnot[1]."/".$tfippnot[0];
        // $peri['f_ipp_not']=$f_ipp_not;
        // $f_fpp_not=$peri['f_fpp_not'];
        // $tffppnot =split("-",$f_fpp_not);
        // $f_fpp_not =$tffppnot[2]."/".$tffppnot[1]."/".$tffppnot[0];
        // $peri['f_fpp_not']=$f_fpp_not;
        // $f_isp_not=$peri['f_isp_not'];
        // $tfispnot =split("-",$f_isp_not);
        // $f_isp_not =$tfispnot[2]."/".$tfispnot[1]."/".$tfispnot[0];
        // $peri['f_isp_not']=$f_isp_not;
        // $f_fsp_not=$peri['f_fsp_not'];
        // $tffspnot =split("-",$f_fsp_not);
        // $f_fsp_not =$tffspnot[2]."/".$tffspnot[1]."/".$tffspnot[0];
        // $peri['f_fsp_not']=$f_fsp_not;
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
      if ($this->getRequest()->isPost())
       {
        $frmdata=$this->getRequest()->getPost();
        
          unset($frmdata['guardar']);      
          $frmdata['eid']=$eid;
          $frmdata['oid']=$oid;
          $frmdata['updated']=date("Y-m-d h:m:s");
          $frmdata['modified']=$uid;
          $frmdata['register']=$uid;
          $frmdata['state']='B';
      
      
          $dbper=new Api_Model_DbTable_Periods();
          if($per=$dbper->_save($frmdata))
               { 
            ?><script>
                    alert('se agrego un nuevo periodo');
              </script>
            <?php 
            } 
                else{
        $form->populate($frmdata);
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
         { ?>
                    <script>
                    alert('se elimino el periodo');
                    </script>
            <?php $this->_helper->redirector('index',"periods",'rcentral');
                

              } 
              else
            {
              ?>
               <script>
                    alert('No se puede eliminar este periodo por que tiene ');
                </script>
            <?php 
                 
            }
  }
  catch (Exception $ex)
  {

  }
}


}