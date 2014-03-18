<?php

class Pedagogia_DistributionController extends Zend_Controller_Action {

    public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

    public function indexAction()
    {	
    	//echo "hfh";
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $where=array('eid'=>$eid,'oid'=>$oid);
        $dbfaculty=new Api_Model_DbTable_Faculty();
        $dataf=$dbfaculty->_getAll($where);
        $this->view->dataf=$dataf;
    }

    public function viewAction(){	
    	try{            
            $this->_helper->layout()->disablelayout();
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$perid=$this->_getParam('perid');
            $facid=$this->_getParam('facid');
            $distri= new Distribution_Model_DbTable_Distribution();
            $dbescuela= new Api_Model_DbTable_Speciality();
            if ($facid=="TODO") {
                $where=array("eid"=>$eid, "oid"=>$oid,"perid"=>$perid);
                $order=array('escid');
                $datadis=$distri->_getFilter($where,$attrib=null,$order);
                $dis=$datadis;
                unset($where['perid']);
                $i=0;
                foreach ($datadis as $datadis) {
                    $where['escid']=$datadis['escid'];
                    $where['subid']=$datadis['subid'];
                    $dataes=$dbescuela->_getOne($where);
                    $dis[$i]['name']=$dataes['name'];
                    $i++;
                }
            }
            else{
                $where1=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
                $order=array('name');
                $datae=$dbescuela->_getFilter($where1,$attrib=null,$order);
                $len=count($datae);
                $dis=array();
                $i=0;
                foreach ($datae as $datae) {
                    $escid=$datae['escid'];
                    $where=array("eid"=>$eid, "oid"=>$oid,"escid"=>$escid,"perid"=>$perid);
                    $datadis=$distri->_getFilter($where);
                    if ($datadis[0]) {
                        $dis[$i]=$datadis[0];
                        $dis[$i]['name']=$datae['name'];
                        $i++;                   
                    }
                }
            }
            // print_r($dis);exit();
            $this->view->dis=$dis;
    	}catch(Exception $ex){
            print "Error: Cargar ".$ex->getMessage();

    	}
    }

    public function distributionAction()
    {
        $this->_helper->layout()->disablelayout();
    	$eid=$this->sesion->eid;
    	$oid=$this->sesion->oid;
    	$perid = $this->_getParam("perid");
    	$escid = $this->_getParam("escid");
    	$distid = $this->_getParam("distid");
    	$subid = $this->_getParam("subid");
    	$obs = $this->_getParam("observation");
    	//$nombre = $this->_getParam("nombre");
    	
    	$distribution= new Distribution_Model_DbTable_Distribution();
    	$where=array("eid"=>$eid, "oid"=>$oid,"perid"=>$perid,"escid"=>$escid,"subid"=>$subid,"distid"=>$distid);
    	$dist=$distribution->_getOne($where);

    	//print_r($dist);
        $state=$dist['state'];
        $formData['eid']=$eid;
        $formData['oid']=$oid;
        $formData['escid']=$escid;
        $formData['perid']=$perid;
        $formData['distid']=$distid;
        $formData['subid']=$subid;
        $pk =$formData;

        if ($dist['comments']=='' && $state<>"O") {
            $formData['comments']=$state;
        }

        if ($obs!="''" && $obs!="") {
            $formData['state']='O';
            $formData['observation']=$obs;
        }
        else{
            $formData['state']=$dist['comments'];
            $formData['comments']=null;
            $formData['observation']=null;
        }

    	print_r($formData);exit();
    	$distr = new Distribution_Model_DbTable_Distribution();
    	$distr->_update($formData,$pk);

    }

    public function closedistributionAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $state=base64_decode($this->_getParam("state"));
            $escid=base64_decode($this->_getParam("escid"));
            $subid=base64_decode($this->_getParam("subid"));
            $distid=base64_decode($this->_getParam("distid"));
            $perid=base64_decode($this->_getParam("perid"));
            $comment=base64_decode($this->_getParam("comment"));
            $dbdistribution=new Distribution_Model_DbTable_Distribution();
            $pk=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'distid'=>$distid,
                            'perid'=>$perid);
            if ($state=="C") {
                $data=array('state'=>"C",'comments'=>null);
            }

            $dbdistribution->_update($data,$pk);
        
        } catch (Exception $e) {
            print "Error:".$e->getMessage();
        }

    }
}
