<?php 
class Admin_UserController extends Zend_Controller_Action{

    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }
    
    public function indexAction(){
        try {
            $fm=new Admin_Form_Buscar();
            $this->view->fm=$fm;
        } catch (Exception $e) {
            print "Error: User".$e->getMessage();
        }
    }
    public function getuserAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->_getParam('pid');
            $datauser[0] = array();
            if ($pid) {
                $where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
                $dbuser=new Api_Model_DbTable_Users();
                $datauser=$dbuser->_getUserXPid($where);
                if ($datauser==false) {
                    $where=array('eid'=>$eid,'pid'=>$pid);
                    $dbper=new Api_Model_DbTable_Person();
                    $datauser[0]=$dbper->_getOne($where);
                }
            }
            $name = $this->_getParam('name');
            if($name){
                $eid = $this->sesion->eid;
                $oid = $this->sesion->oid;
                $name = trim(strtoupper($name));
                $name = mb_strtoupper($name,'UTF-8');
                $dbuser=new Api_Model_DbTable_Users();
                $datauser=$dbuser->_getUserXnameXsinRolAll($name,$eid,$oid);
                if ($datauser==false) {
                    $eid = $this->sesion->eid;
                    $name = trim(strtoupper($name));
                    $name = mb_strtoupper($name,'UTF-8');
                    $bdp=new Api_Model_DbTable_Person();
                    $datauser[0]=$bdp->_getPersonxname($name,$eid);                
                }
            }
            $uid = $this->_getParam('uid');
            if ($uid) {
                $where['uid'] = $uid;
                $where['eid'] = $this->sesion->eid;
                $where['oid'] = $this->sesion->oid;
                $bduser = new Api_Model_DbTable_Users();
                $datauser = $bduser->_getUserXUid($where);                          
            }
            $c=0;
            $whered=array('eid'=>$eid,'oid'=>$oid);
            $wheres=array('eid'=>$eid,'oid'=>$oid);
            
            if ($datauser[0]) {
                foreach ($datauser as $info) {
                    $rid=$info['rid'];
                    $subid=$info['subid'];
                    $escid=$info['escid'];
                    $wheres['subid']=$subid;
                    $wheres['escid']=$escid;
                    $whered['rid']=$rid;
                    $dbrol=new Api_Model_DbTable_Rol();
                    $inforol[$c]= $dbrol->_getOne($whered);
                    $dbesc= new Api_Model_DbTable_Speciality();
                    $infoesc[$c]= $dbesc->_getOne($wheres);
                    $c++;
                }
                $this->view->datauser=$datauser;
            $this->view->infoesc=$infoesc;
            $this->view->inforol=$inforol; 
            $rol=$this->sesion->rid;
            $this->view->rol=$rol;
            }  
        } catch (Exception $e) {
            print "Error: get User".$e->getMessage();
        }
    }

    public function newAction(){
        try {
            $fm= new Admin_Form_Buscar();
            $this->view->fm=$fm;
            
        } catch (Exception $e) {
            print "Error: User new".$e->getMessage();
        }
    }

    public function getusernewAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->_getParam('pid');
            if ($pid) {
                $where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
                $dbuser=new Api_Model_DbTable_Users();
                $datauser=$dbuser->_getUserXPid($where);
                if ($datauser==false) {
                    $where=array('eid'=>$eid,'pid'=>$pid);
                    $dbper=new Api_Model_DbTable_Person();
                    $datauser[0]=$dbper->_getOne($where);
                }
                $c=0;
                $whered=array('eid'=>$eid,'oid'=>$oid);
                $wheres=array('eid'=>$eid,'oid'=>$oid);
                foreach ($datauser as $info) {
                    $rid=$info['rid'];
                    $subid=$info['subid'];
                    $escid=$info['escid'];
                    $wheres['subid']=$subid;
                    $wheres['escid']=$escid;
                    $whered['rid']=$rid;
                    $dbrol=new Api_Model_DbTable_Rol();
                    $inforol[$c]= $dbrol->_getOne($whered);
                    $dbesc= new Api_Model_DbTable_Speciality();
                    $infoesc[$c]= $dbesc->_getOne($wheres);
                    //$info[$c]=$inforol[$c]['rid'];
                    $c++;
                }
                // print_r($datauser);
                $this->view->infoesc=$infoesc;
                $this->view->inforol=$inforol;
                $this->view->datauser=$datauser;
            }           
        } catch (Exception $e) {
            print "Error: get Person".$e->getMessage();
        }
    }
    public function newuserAction(){
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=base64_decode($this->_getParam('pid'));
            $this->view->pid=$pid;
            $fm= new Admin_Form_Usernew();
            $this->view->fm=$fm;
            $escid=new Zend_Form_Element_Select('escid');
            $escid->removeDecorator('Label')->removeDecorator('HtmlTag');
            $escid->setAttrib('class','form-control');
            $escid->setRequired(true)->addErrorMessage('Este campo es requerido');
            $escid->setAttrib('title','Seleccione una escuela');
            $escid->addMultiOption("",'- Seleccione una Sede -');
            $this->view->escid=$escid;
            $register=$this->sesion->uid;
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($fm->isValid($frmdata))
                {                    
                    unset($frmdata['Guardar']);
                    $rid=$frmdata['rid'];
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;                 
                    $where=array('eid'=>$eid,'oid'=>$oid,'rid'=>$rid);
                    $dbrol=new Api_Model_DbTable_Rol();
                    $datarol=$dbrol->_getOne($where);
                    $prefix=$datarol['prefix'];
                    $frmdata['uid']=$frmdata['pid'].$prefix;                
                    $frmdata['created']=date('Y-m-d h:m:s');
                    $frmdata['register']=$register;
                    $frmdata['password']=md5($frmdata['uid']);                  
                    $reg_= new Api_Model_DbTable_Users();
                    if ($reg_->_save($frmdata)) {
                        if ($frmdata['rid']=="DC" || $frmdata['rid']=="JP") {
                            $data=array('eid'=>$frmdata['eid'],'oid'=>$frmdata['oid'],'uid'=>$frmdata['uid'],
                                'pid'=>$frmdata['pid'],'escid'=>$frmdata['escid'],'subid'=>$frmdata['subid'],
                                'is_director'=>"N");
                            $dbinfoteacher=new Api_Model_DbTable_Infoteacher();
                            $dbinfoteacher->_save($data);
                        }
                        $this->_redirect("/admin/user/new/");  
                    }
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }
    }

    public function filterspecialityAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $subid = $this->_getParam('subid');
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid);
            $attrib=array('escid','name','state');
            $dbesc = new Api_Model_DbTable_Speciality();
            $data = $dbesc->_getFilter($where);
            $this->view->data = $data;         
        }catch (Exception $ex){
            print "Error : get Filter".$ex->getMessage();
        }
    }
    public function saveupdateAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $frmdata=$this->getRequest()->getPost();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $teacherDb = new Api_Model_DbTable_UserInfoTeacher();
            //cantidad de usuarios con este dni y con estado A
            //verificar si tiene user DC Activos!
            $pid=$frmdata['pidDoc'];
            if ($frmdata['uidDoc']=== $frmdata['uidDir'] and $frmdata['pidDoc'] === $frmdata['pidDir']) {
                print_r("¡EL DIRECTOR Y DOCENTE SON EL MISMO!");exit();
            }else{
            $where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
            $dbuser=new Api_Model_DbTable_Users();
            $datauser=$dbuser->_getUserXPid($where);
            $tam=count($datauser);
            $band1=false;
            
                if ($tam == 1) {
                    $band1=true;
                }else{
                    $cont=0;
                    for ($i=0; $i <$tam ; $i++) { 
                        if ($datauser[$i]["rid"]=="DC" and $datauser[$i]["state"]=="A") {
                            $cont++;
                        }
                    }
                    if ($cont==1) {
                        $band1=true;
                    }
                }
                if ($band1==true) {
                    if ($frmdata['uidDir']=="" and $frmdata['pidDir']=="") {
                        $pk['eid'] = $eid;
                        $pk['oid'] = $oid;
                        $pk['uid'] = $frmdata['uidDoc'];
                        $pk['pid'] = $frmdata['pidDoc'];
                        $pk['escid'] = $frmdata['escid'];
                        $pk['subid'] = $frmdata['subid'];
                        $data['is_director'] = "S";
                        if ($teacherDb->_update($data,$pk)) {
                            print_r("SE ASIGNO NUEVO DIRECTOR");
                        }
                    } else {
                        $band=false;
                        //borrar director
                        $pk['eid'] = $eid;
                        $pk['oid'] = $oid;
                        $pk['subid'] = $frmdata['subid'];
                        $pk['escid'] = $frmdata['escid'];
                        $pk['uid'] = $frmdata['uidDir'];
                        $pk['pid'] = $frmdata['pidDir'];
                        $data['is_director'] = "N";
                        if ($teacherDb->_update($data,$pk)) {
                            $band=true;   
                            print_r("SE ELIMINO DIRECTOR ");
                        }
                        if ($band==true) {
                            $pk['eid'] = $eid;
                            $pk['oid'] = $oid;
                            $pk['subid'] = $frmdata['subid'];
                            $pk['escid'] = $frmdata['escid'];
                            $pk['uid'] = $frmdata['uidDoc'];
                            $pk['pid'] = $frmdata['pidDoc'];
                            $data['is_director'] = "S";
                            if ($teacherDb->_update($data,$pk)) {
                                print_r("Y ASIGNO AL DOCENTE ACTUAL");
                            }
                        }
                    }
            }else{
                print_r("ESTA PERSONA TIENE DOS USUARIOS ACTIVOS");
            }
        }
        } catch (Exception $e) {
            print "Error: update User".$e->getMessage();
        }
    }

    public function updateuserAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid = base64_decode($this->_getParam('uid'));
            $pid = base64_decode($this->_getParam('pid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $rid = base64_decode($this->_getParam('rid'));
            $where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'rid'=>$rid);
            $attrib=array('uid','escid','subid','pid','rid','state','comments');
            $dbuser = new Api_Model_DbTable_Users();
            $fm= new Admin_Form_Usernew();
            $data = $dbuser->_getFilter($where,$attrib);
            $rid = $data[0]['rid'];
            $user=$data[0];
            $this->view->user=$user;
                //director de escuela
                $pk['eid']=$eid;
                $pk['oid']=$oid;
                $pk['escid']=$data[0]['escid'];
                $pk['is_director']='S';
                $teacherDb=new Api_Model_DbTable_Infoteacher();
                $director=$teacherDb->_getPrincipal($pk);
                //busca escuela
                $where['oid']=$oid;
                $where['eid']=$eid;
                $where['escid']=$director['escid'];
                $where['subid']=$director['subid'];
                $specialityDb = new Api_Model_DbTable_Speciality();
                $speciality = $specialityDb->_getOne($where);
                $this->view->speciality=$speciality;
                //busca datos del director
                $where['oid']=$oid;
                $where['eid']=$eid;
                $where['escid']=$director['escid'];
                $where['subid']=$director['subid'];
                $where['uid']=$director['uid'];
                $where['pid']=$director['pid'];
                $directorData=$dbuser->_getInfoUser($where);
                $this->view->director=$directorData;
                //acaba director
            $this->view->rid=$rid;
            $fm->populate($data[0]);
            $fm->rid->setAttrib('disable',true);
            $this->view->fm=$fm;
        } catch (Exception $e) {
            print "Error: update User".$ex->getMessage();
        }
    }
    public function updatestateAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $pk['eid']=$this->sesion->eid;
            $pk['oid']=$this->sesion->oid;
            $pk['pid']=base64_decode($this->_getParam('pid'));
            $pk['escid']=base64_decode($this->_getParam('escid'));
            $pk['subid']=base64_decode($this->_getParam('subid'));
            $pk['uid']=base64_decode($this->_getParam('uid'));
            $data['state']=$this->_getParam('estado');
            $data['comments']=$this->_getParam('ndoc');
            $reg_= new Api_Model_DbTable_Users();
            if($reg_->_update($data,$pk)){
                print_r("1");
            }
        } catch (Exception $e) {
            print "Error: update User".$ex->getMessage();
        }
    }

    public function changepasswordAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=base64_decode($this->_getParam('uid'));
            $pid=base64_decode($this->_getParam('pid'));
            $rid=base64_decode($this->_getParam('rid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $pass1=$this->_getParam('pas1');
            $pass2=$this->_getParam('pas2');
            //codofica
            $newpass = md5($pass1);
            $repnewpass = md5($pass2);
        //verifica campos vacios
            if ($pass1 <>"" && $pass2 <>""){
                     if($newpass===$repnewpass){
                                $data['password']=$newpass;
                                $data['change_password']="T";
                                $pk['eid']=$eid;
                                $pk['oid']=$oid;
                                $pk['uid']=$uid;
                                $pk['pid']=$pid;
                                $pk['escid']=$escid;
                                $pk['subid']=$subid;
                                $bdu = new Api_Model_DbTable_Users();
                                $veri=$bdu->_update($data,$pk);
                                if ($veri) {
                                    print_r("<span class='label label-success'>¡ SE CAMBIO LA CONTRASEÑA !</span>");
                                }else{
                                    print_r("<span class='label label-danger'>¡ERROR! : faltan datos</span>");
                                }
                     }else{
                        print_r("<span class='label label-danger'>¡ERROR! : Las contraseñas no coinsiden</span>");
                     }
            }else{
                print_r("<span class='label label-danger'>¡ERROR! : la contraseña no puede ser vacia</span>");
            }

        } catch (Exception $e) {
            print "Error: update User".$ex->getMessage();
        }
    }
}    
