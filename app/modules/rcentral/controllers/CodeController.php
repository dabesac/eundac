<?php
class Rcentral_CodeController extends Zend_Controller_Action
{

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


	public function indexAction(){
		try {
            $fm = new Admin_Form_Personnew();
            $this->view->fm=$fm;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}


    public function listuserAction(){
        try {

            $this->_helper->layout()->disableLayout();    
            $pid=$this->_getParam("pid");
            $eid = $this->sesion->eid;        
            $oid = $this->sesion->oid;

            $rid = $this->sesion->rid;      
            if ($rid=='RC')
            {
            $listar = new Api_Model_DbTable_Users();
            $list= $listar->_getUsers($eid,$oid,$pid,$uid,$nom);
            $this->view->paginator = $list;
            $this->view->name=$list[0]['nombrecompleto'];
            $this->view->dni=$list[0]['pid']; 
            $fm =new Rcentral_Form_Gcode();  
            $this->view->fm=$fm;           
            }            
        } catch (Exception $ex) {
            print "Error filtrar rol : ".$ex->getMessage();
        }
    }

public function lschoolAction(){
    try{
        $this->_helper->layout()->disableLayout();
        $where['subid'] = $this->_getParam("subid");
        $where['eid'] = $this->sesion->eid;        
        $where['oid'] = $this->sesion->oid;
        // $where['state'] = 'A';    
        $db_esc = new Api_Model_DbTable_Speciality();
        $escuelas = $db_esc->_getFilter($where);        
        $this->view->escuelas = $escuelas;         
    }catch (Exception $ex){
        print "Error : ".$ex->getMessage();
    }
}



    public function generatecodeAction() 
    {
    $formData = $this->getRequest()->getPost();
    $pid=$formData['pid'];
    $resolucion=$formData['resolucion'];
    $fecha=$formData['fingreso'];
    $splitanio=split('/',$fecha );
    $anio=substr($splitanio[0], 2, 2);
    $proceso=$formData['idproc'];
    $modalidad=$formData['idmod'];
    $orden=$formData['orden'];
    if (strlen($orden)=='1')
    {
      $orde='0'.$orden;
    }
    else
    {
      $orde=$orden;      
    }
    $where['escid']=$formData['escid'];
    $eid = $this->sesion->eid;        
    $oid = $this->sesion->oid;
    $codigoescuela = new Api_Model_DbTable_Codespeciality ();
    $codigolo = $codigoescuela->_getOne($where);
    $codigoesc=$codigolo['code'];
    $mod_codigo=$anio.$proceso.$codigoesc.$modalidad.$orde;
    $mod_suma = 0;
    $mod_len = strlen($mod_codigo);
    for($i=1;$i<=9;$i++)
    {
      $mod_suma = $mod_suma+(((int)substr($mod_codigo, $i-1, 1))*($i+1));
      $e=(((int)substr($mod_codigo, $i-1, 1))*($i+1));
    }
    $modulo = $mod_suma%11;
    if ($modulo>9)
    {
        $modulo = 0;
    }
    $alea=(string)($modulo);
    $codigo=$mod_codigo.$alea;
    $data['eid']=$this->sesion->eid;
    $data['oid']=$this->sesion->oid;
    $data['pid']=$formData['pid'];
    $data['escid']=$formData['escid'];
    $data['subid']=$formData['subid'];
    $data['rid']='AL';
    $data['uid']=$codigo;
    $data['password']=$codigo;
    $data['register']=$this->sesion->uid;
    $data['state']='A';
    $data['comments']=$resolucion;
    // print_r($data);
    $persona = new Api_Model_DbTable_Users();
    $datos=$persona->_save($data);
    $this->_helper->_redirector("list","code","rcentral",array('pid' => $formData['pid'] ));   
    }

    public function listAction() 
    {
    try {
             
            $pid=$this->_getParam("pid");
            $eid = $this->sesion->eid;        
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;      
            if ($rid=='RC')
            {
            $listar = new Api_Model_DbTable_Users();
            $list= $listar->_getUsers($eid,$oid,$pid,$uid,$nom);
            $this->view->paginator = $list;
            $this->view->nombre=$list[0]['nombrecompleto'];
            $this->view->dni=$list[0]['pid']; 
            $fm =new Rcentral_Form_Gcode();  
            $this->view->fm=$fm;           
            }        

        } catch (Exception $ex) {
            print "Error: ".$ex->getMessage();
        }
    }

}