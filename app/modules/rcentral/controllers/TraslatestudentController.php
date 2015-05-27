<?php
class Rcentral_TraslatestudentController extends Zend_Controller_Action
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
    }

    public function searchAction(){
        try {
            $this->_helper->layout()->disableLayout();    
            $cred=null;
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uid = $this->_getParam('uid');

            $user = new Api_Model_DbTable_Users();
            $datauser = $user->_getUserXUid($where=array('eid' => $eid, 'oid' => $oid, 'uid' => $uid));

            if ($datauser) {
                $where = array('eid' => $eid, 'oid' => $oid, 'subid' => $datauser[0]['subid']);
                $sub = new Api_Model_DbTable_Subsidiary();
                $datasub = $sub->_getOne($where);
                $datauser[0]['namesubid'] = $datasub['name'];

                $where=array('eid' => $eid, 'oid' => $oid, 
                            'subid' => $datauser[0]['subid'], 'escid' => $datauser[0]['escid']);
                $esp = new Api_Model_DbTable_Speciality();
                $dataesp = $esp->_getOne($where);
                $datauser[0]['nameescid'] = $dataesp['name'];

                $where=array(
                    'eid' => $eid, 'oid' => $oid, 'subid' => $datauser[0]['subid'], 'escid' => $datauser[0]['escid'], 'uid' => $datauser[0]['uid'], 'pid' => $datauser[0]['pid']);
                $tras = new Api_Model_DbTable_Traslatestudent();
                $datatras = $tras->_getOne($where);
                if (!$datatras) {
                    $dataallesp = $esp->_getFilter($where=array('eid' => $eid, 'oid' => $oid, 'state' => 'A'),$attrib=null,$orders=null);
                    $this->view->escuelas = $dataallesp;
                    $where = array(
                        'eid' => $eid, 'oid' => $oid, 'subid' => $datauser[0]['subid'],
                        'escid' => $datauser[0]['escid'], 'uid' => $datauser[0]['uid'], 
                        'pid' => $datauser[0]['pid'], 'state' => 'M');
                    $reg = new Api_Model_DbTable_Registrationxcourse();
                    $datareg = $reg->_getFilter($where,$attrib=null,$orders=null);
                    if ($datareg) {
                        $cour = new Api_Model_DbTable_Course();
                        foreach ($datareg as $register) {
                            if ($register['notafinal']>10) {
                                $where = array(
                                    'eid' => $eid, 'oid' => $oid, 'subid' => $datauser[0]['subid'],
                                    'escid' => $datauser[0]['escid'], 'curid' => $register['curid'],
                                    'courseid' => $register['courseid']);
                                $datacourse = $cour->_getOne($where);
                                $cred += $datacourse['credits'];
                            }
                        }
                        if ($cred<15) $this->view->error = 2;
                    }else $this->view->error = 2;
                }else {
                    $where=array('eid' => $eid, 'oid' => $oid, 
                            'subid' => $datatras['d_subid'], 'escid' => $datatras['d_escid']);
                    $dataespp = $esp->_getOne($where);
                    $datatras['name'] = $dataespp['name'];
                    $this->view->traslado = $datatras;
                    $this->view->error = 1;
                }
            }
            $this->view->user = $datauser[0];
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function changeAction(){
        try {
            $this->_helper->layout()->disableLayout();    
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uid = $this->_getParam('uid');
            $pid = $this->_getParam('pid');
            $escid = $this->_getParam('escid');
            $subid = $this->_getParam('subid');

            $user = new Api_Model_DbTable_Users();
            $datauser = $user->_getUserXUid($where=array('eid' => $eid, 'oid' => $oid, 'uid' => $uid));
            $pk = array(
                'eid' => $eid, 'oid' => $oid, 'uid' => $uid, 'pid' => $pid, 
                'escid' => $datauser[0]['escid'], 'subid' => $datauser[0]['subid']);
            $user->_update($data = array('state' => 'I'),$pk);
            
            $tmp = new Api_Model_DbTable_Tmpgeneratedcode();
            $escidtmp = $tmp->_getOne($where=array('escid' => $escid));
            $codtmp = substr($uid, 0,3);
            $codtmp2 = substr($uid, 7);
            $data = array(
                'eid' => $eid, 'oid' => $oid, 'uid' => $codtmp.$escidtmp['code'].'7'.$codtmp2, 
                'pid' => $pid, 'escid' => $escid, 'subid' => $subid, 'rid' => $datauser[0]['rid'],
                'password' => md5($codtmp.$escidtmp['code'].'7'.$codtmp2), 'state' => 'A',
                'register' => $this->sesion->uid, 'created' => date('Y-m-d'));
           $user->_save($data);

        $curri= new Api_Model_DbTable_Curricula();
        $where3['escid']=$data['escid'];
        $where3['eid']=$data['eid'];
        $where3['oid']=$data['oid'];
        //$where3['state']='A';
        $todcur=$curri->_getFilter($where3);
        $where4['eid']=$eid;
        $where4['oid']=$oid;
        $where4['escid']= $data['escid'];
        $where4['subid']=$data['subid'];
        $where4['uid']=$uid;
        $where4['pid']=$pid;
        $g=$todcur[0]['curid'];
        $datacur['curid']=$g;
        $al= new Api_Model_DbTable_Studentxcurricula ();
        $existe=$al->_getsearch($where4);  
        if($existe){
            if ($al->_update($datacur,$where4)) {
            echo "todo listo :-)";
            }
        }else{
            $where5['eid']=$data['eid'];
            $where5['oid']=$data['oid'];
            $where5['escid']=$data['escid'];
            $where5['subid']=$data['subid'];
            $where5['uid']=$data['uid'];
            $where5['pid']=$data['pid'];
            $where5['curid']=$g;
            $where5['state']='A';

            if ($al->_save($where5)) {
            echo "todo listo :-)";
           }
        }
       $data = array(
                'eid' => $eid, 'oid' => $oid, 'uid' => $uid, 'pid' => $pid, 
                'escid' => $datauser[0]['escid'], 'subid' => $datauser[0]['subid'],
                'd_escid' => $escid, 'd_subid' => $subid);
            $tras = new Api_Model_DbTable_Traslatestudent();
            $tras->_save($data);
            $this->view->msg = 1;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}