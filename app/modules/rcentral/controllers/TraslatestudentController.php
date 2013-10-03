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
                    $where = array(
                        'eid' => $eid, 'oid' => $oid, 'subid' => $datauser[0]['subid'],
                        'escid' => $datauser[0]['escid'], 'uid' => $datauser[0]['uid'], 
                        'pid' => $datauser[0]['pid'], 'state' => 'M');
                    $reg = new Api_Model_DbTable_Registrationxcourse();
                    $datareg = $reg->_getFilter($where,$attrib=null,$orders=null);
                    if ($datareg) {
                        $cour = new Api_Model_DbTable_Course();
                        $c=0;
                        foreach ($datareg as $register) {
                            if ($register['notafinal']>10) {
                                echo $c++;
                                // $where = array(
                                //     'eid' => $eid, 'oid' => $oid, 'subid' => $datauser[0]['subid'],
                                //     'escid' => $datauser[0]['escid'], 'curid' => $register['curid'],
                                //     'courseid' => $register['courseid']);
                                // $datacourse = $cour->_getOne($where);
                                // $cred += $datacourse['credits'];
                                // if ($cred>15) break();
                            }
                        }
                    }else $this->view->error = 2;
                }else {

                    $this->view->error = 1;
                }
            }
            $this->view->user = $datauser[0];
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}