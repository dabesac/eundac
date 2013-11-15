<?php

class Distribution_PrintdistributionController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (($login->infouser['teacher']['is_director']<>"S")){
    	// 	$this->_helper->redirector('index','error','default');
    	// }
    	$this->sesion = $login;
    }

    public function indexAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $distid = base64_decode($this->_getParam("distid"));
            $perid = base64_decode($this->_getParam("perid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $t = $this->_getParam("t");

            $this->view->perid = $perid;
            $this->view->distid = $distid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->t = $t;            


            if(substr($escid,0,3)=='2ES' and ($escid<>'2ESTY')){
               $this->_redirect("/distribution/printdistribution/printsecond/t/$t/distid/".base64_encode($distid).
                                "/perid/".base64_encode($perid)."/subid/".base64_encode($subid).
                                "/escid/".base64_encode($escid));
            }else{
                $pk=array('eid' => $eid, 'oid' => $oid, 'escid' => $escid,
                        'subid' => $subid, 'distid' => $distid, 'perid' => $perid);
                $dist = new Distribution_Model_DbTable_Distribution();
                $datadist = $dist->_getOne($pk);

                if ($datadist){
                    $this->view->distribution = $datadist;
                    $where=array('eid' => $eid, 'oid' => $oid, 'escid' => $escid,
                            'subid' => $subid, 'rid' => 'DC', 'state' => 'A');
                    $user = new Api_Model_DbTable_Users();
                    $datauser = $user->_getUserXRidXEscidAll($where);

                    $tam=count($datauser);
                    $whereinfo=array('eid' => $eid, 'oid' => $oid,
                                 'escid' => $escid, 'subid' => $subid);
                    $info= new Api_Model_DbTable_UserInfoTeacher();
                    for ($i=0; $i < $tam; $i++) {
                        $whereinfo['pid']=$datauser[$i]['pid'];
                        $whereinfo['uid']=$datauser[$i]['uid'];
                        $datainfo=$info->_getOne($whereinfo);
                        $datauser[$i]['category']=$datainfo['category'];
                        $datauser[$i]['condision']=$datainfo['condision'];
                        $datauser[$i]['dedication']=$datainfo['dedication'];
                        $datauser[$i]['charge']=$datainfo['charge'];
                        $datauser[$i]['contract']=$datainfo['contract'];
                        $datauser[$i]['is_director']=$datainfo['is_director'];
                    }
                    $this->view->teachers = $datauser;

                    $where['rid']='JP';
                    $datauserJP = $user->_getUserXRidXEscidAll($where);
                    if ($datauserJP) {
                        $tam=count($datauserJP);
                        $whereinfo=array('eid' => $eid, 'oid' => $oid,
                                 'escid' => $escid, 'subid' => $subid);
                        for ($i=0; $i < $tam; $i++) {
                            $whereinfo['pid']=$datauserJP[$i]['pid'];
                            $whereinfo['uid']=$datauserJP[$i]['uid'];
                            $datainfo=$info->_getOne($whereinfo);
                            $datauserJP[$i]['category']=$datainfo['category'];
                            $datauserJP[$i]['condision']=$datainfo['condision'];
                            $datauserJP[$i]['dedication']=$datainfo['dedication'];
                            $datauserJP[$i]['charge']=$datainfo['charge'];
                            $datauserJP[$i]['contract']=$datainfo['contract'];
                        }
                        $this->view->practiceteachers = $datauserJP;
                    }

                    $whereteach=array('eid' => $eid, 'oid' => $oid, 
                                'escid' => $escid, 'perid' => $perid);
                    $distteacher = new Api_Model_DbTable_Coursexteacher();
                    $dataallteacher = $distteacher->_getAllTeacherXPeriodXEscid($whereteach);
                    $this->view->allteachers = $dataallteacher;

                    $whereteach['distid']=$distid;
                    $datateachers=$distteacher->_getFilter($whereteach,$attrib=null,$orders=null);                    
                    $this->view->datateachers=$datateachers;
                }
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function printsecondAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $distid = base64_decode($this->_getParam("distid"));
            $perid = base64_decode($this->_getParam("perid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
             
            $t = $this->_getParam("t");

           

            $this->view->perid = $perid;
            $this->view->distid = $distid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->t = $t;            
                     
            

            $whereesp = array('eid' => $eid, 'oid' => $oid,
                        'escid' => $escid, 'subid' => $subid);
            $espe = new Api_Model_DbTable_Speciality();
            $dataespe = $espe->_getOne($whereesp);
            $this->view->speciality=$dataespe;

            $whereteach=array('eid' => $eid, 'oid' => $oid, 
                                'escid' => $escid, 'perid' => $perid);
            $distteacher = new Api_Model_DbTable_Coursexteacher();
            $dataallteacher = $distteacher->_getAllTeacherXPeriodXEscid($whereteach);
            $users = new Api_Model_DbTable_Users();
            $infotea = new Api_Model_DbTable_UserInfoTeacher();
            if ($dataallteacher) {
                $tam = count($dataallteacher);
                $whereuser = array('eid' => $eid, 'oid' => $oid);
                $whereinfotea = array('eid' => $eid, 'oid' => $oid);
                for ($i=0; $i < $tam; $i++) { 
                    $whereuser['uid'] = $dataallteacher[$i]['uid'];
                    $usu = $users->_getUserXUid($whereuser);
                    $dataallteacher[$i]['escidorigen'] = $usu[0]['escid'];
                    $dataallteacher[$i]['subidorigen'] = $usu[0]['subid'];
                    $dataallteacher[$i]['fullname'] = $usu[0]['last_name0']." ".$usu[0]['last_name1'].", ".$usu[0]['first_name'];

                    $whereinfotea['escid'] = $dataallteacher[$i]['escidorigen'];
                    $whereinfotea['subid'] = $dataallteacher[$i]['subidorigen'];
                    $whereinfotea['uid'] = $dataallteacher[$i]['uid'];
                    $whereinfotea['pid'] = $dataallteacher[$i]['pid'];
                    $datainfotea = $infotea->_getOne($whereinfotea);
                    $dataallteacher[$i]['condision'] = $datainfotea['condision'];
                    $dataallteacher[$i]['dedication'] = $datainfotea['dedication'];
                    $dataallteacher[$i]['category'] = $datainfotea['category'];
                }
            }
            $this->view->allteachers = $dataallteacher;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}