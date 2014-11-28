<?php
 class Profile_ChangecurriculaController extends Zend_Controller_Action{

    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;

    }

 	public function indexAction(){
    $this->_helper->redirector('change');

 		
 	}


    public function changeAction(){
      try {
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $uid=base64_decode($this->_getParam('uid'));
        $pid=base64_decode($this->_getParam('pid'));

        $this->view->uid=$uid;
        $per= new Api_Model_DbTable_Users();
        $where['uid']=$uid;
        $where['eid']=$eid;
        $where['oid']=$oid;
        $dat=$per->_getUserXUid($where);
        $this->view->persona=$dat;
        $esc= new Api_Model_DbTable_Speciality();
        $where1['escid']=$dat[0]['escid'];
        $where1['eid']=$eid;
        $where1['oid']=$oid;
        $where1['subid']=$dat[0]['subid'];
        $datesc=$esc->_getOne($where1);
        $this->view->escuela=$datesc['name'];
        $al= new Api_Model_DbTable_Studentxcurricula();
        $where2['escid']=$dat[0]['escid'];
        $where2['eid']=$eid;
        $where2['oid']=$oid;
        $where2['subid']=$dat[0]['subid'];
        $where2['uid']=$uid;
        $where2['pid']=$pid;
        $cur=$al->_getOne($where2);
        $this->view->curricula=$cur['curid'];
        $curri= new Api_Model_DbTable_Curricula();
        $where3['escid']=$dat[0]['escid'];
        $where3['eid']=$eid;
        $where3['oid']=$oid;
        //$where3['state']='A';
        $todcur=$curri->_getFilter($where3);
        $this->view->curriactiva=$todcur;               
            
      } catch (Exception $e) {
        print "Error: get Horary".$e->getMessage();
      }
    }
    public function updateAction(){
      try {
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $subid=$this->sesion->subid;
        $uid=$this->_getParam('uid');
        $curid=$this->_getParam('curid');
        $per= new Api_Model_DbTable_Users();
        $where['uid']=$uid;
        $where['eid']=$eid;
        $where['oid']=$oid;
        $dat=$per->_getUserXUid($where);

        $where2['eid']=$eid;
        $where2['oid']=$oid;
        $where2['subid']=$subid;
        $where2['uid']=$uid;
        $where2['escid']=$dat[0]['escid'];
        $where2['pid']=$dat[0]['pid'];

        $data['curid']=$curid;
        
        $al= new Api_Model_DbTable_Studentxcurricula ();
        $existe=$al->_getsearch($where2);
        
        if($existe){
            if ($al->_update($data,$where2)) {
            $this->view->band=1;
            }

        }else{
            $where3['eid']=$eid;
            $where3['oid']=$oid;
            $where3['subid']=$subid;
            $where3['uid']=$uid;
            $where3['escid']=$dat[0]['escid'];
            $where3['pid']=$dat[0]['pid'];
            $where3['curid']=$curid;
            $where3['state']='A';

            if ($al->_save($where3)) {
            $this->view->band=1;
           }
        }

        
        }
       catch (Exception $e) {
        print "Error: ".$e->getMessage();
      }
    }

 	
 }
