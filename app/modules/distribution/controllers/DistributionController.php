<?php

class Distribution_DistributionController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (($login->rol['module']<>"docente") && ($login->infouser['teacher']['is_director']=="S")){
    	// 	$this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {
   		$distribution = new Distribution_Model_DbTable_Distribution();
   		$data['eid']=$this->sesion->eid;
   		$data['oid']=$this->sesion->oid;
   		$data['escid']=$this->sesion->escid;
   		$data['subid']=$this->sesion->subid;
   		$campos = array("eid","oid","escid","subid","perid","dateaccept","number","state","distid");
   		$rows_distribution =$distribution->_getFilter($data,$campos);
   		if ($rows_distribution) $this->view->ldistribution=$rows_distribution ;
    }
    
    public function newAction()
    {    	
    	$form = new Distribution_Form_Distribution();
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		if ($form->isValid($formData)) {
    			unset($formData['save']);
    			$formData['eid'] = base64_decode($formData['eid']);
    			$formData['oid'] = base64_decode($formData['oid']);
    			$formData['escid'] = base64_decode($formData['escid']);
    			$formData['subid'] = base64_decode($formData['subid']);
    			$formData['perid'] = base64_decode($formData['perid']);
    			$formData['register'] = $this->sesion->uid;
    			$distr = new Distribution_Model_DbTable_Distribution();
    			$formData['distid'] = time();
    			$distr->_save($formData);
    			$this->_helper->redirector('index','distribution','distribution');
    		}else{
    			$form->populate($formData);
    		}
    	}else{
    		$form->number->setValue($this->sesion->period->perid."-".time());
    	}
    	$this->view->form = $form;
    }
    
    public function editAction()
    {
    	$distid = base64_decode($this->_getParam("distid"));
    	$perid = base64_decode($this->_getParam("perid"));
    	$form = new Distribution_Form_Distribution();
    	$form->setAction("/distribution/distribution/edit/");
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		if ($form->isValid($formData)) {
    			unset($formData['save']);
    			$formData['eid'] = base64_decode($formData['eid']);
    			$formData['oid'] = base64_decode($formData['oid']);
    			$formData['escid'] = base64_decode($formData['escid']);
    			$formData['subid'] = base64_decode($formData['subid']);
    			$formData['perid'] = base64_decode($formData['perid']);
    			$formData['distid'] = trim($formData['distid']);
    			$pk =$formData;
    			$formData['modified'] = $this->sesion->uid;
    			$formData['updated'] = date('Y-m-d H:i:s');
    			$distr = new Distribution_Model_DbTable_Distribution();
    			$distr->_update($formData,$pk);
    			$this->_helper->redirector('index','distribution','distribution');
    		}else{
    			$form->populate($formData);
    		}
    	}else{
	    	 
	    	$data['eid']=$this->sesion->eid;
	    	$data['oid']=$this->sesion->oid;
	    	$data['escid']=$this->sesion->escid;
	    	$data['subid']=$this->sesion->subid;
	    	$data['distid']=$distid;
	    	$data['perid']=$perid;
	    	$distr_ = new Distribution_Model_DbTable_Distribution();
	    	$r = $distr_->_getOne($data);
	    	if ($r) {
	    		$r['perid'] = base64_encode($r['perid']);
	    		$r['eid'] = base64_encode($r['eid']);
	    		$r['oid'] = base64_encode($r['oid']);
	    		$r['escid'] = base64_encode($r['escid']);
	    		$r['subid'] = base64_encode($r['subid']);
	    		$form->populate($r);
	    	}else{
	    		$this->_helper->redirector('index','distribution','distribution');	
	    	}
    	}
    	$form->perid->setAttrib("readonly", "");
    	$this->view->form = $form;    	
    }

    public function deleteAction()
    {
    	$distid = base64_decode($this->_getParam("distid"));
    	$perid = base64_decode($this->_getParam("perid"));
    	$data['eid']=$this->sesion->eid;
    	$data['oid']=$this->sesion->oid;
    	$data['escid']=$this->sesion->escid;
    	$data['subid']=$this->sesion->subid;
    	$data['distid']=$distid;
    	$data['perid']=$perid;
    	$distr_ = new Distribution_Model_DbTable_Distribution();
    	$r = $distr_->_delete($data);
    	$this->_helper->redirector('index','distribution','distribution');
    }

    public function academicloadAction(){
        try {
            $distid = base64_decode($this->_getParam("distid"));
            $perid = base64_decode($this->_getParam("perid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function coursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->sedid=$sedid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['perid']=$perid;
            $order = array('curid  ASC','semid asc','courseid  ASC','turno asc');
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $courses=$percourses->_getFilter($where,$attrib=null,$order);
            if ($courses) {
                $tam=count($courses);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['subid']=$subid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['curid']=$courses[$i]['curid'];
                    $wherecourse['courseid']=$courses[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courses[$i]['name']=$dbcourse['name'];
                    $courses[$i]['type']=$dbcourse['type'];
                    $courses[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->courses=$courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function addcoursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->sedid=$sedid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['state']="A";
            $cur = new Api_Model_DbTable_Curricula();
            $datacur = $cur->_getFilter($where,$attrib=null,$orders=null);
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}

