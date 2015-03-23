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

        $anio = date('Y');
        $this->view->anio=$anio;
        $perid=$this->sesion->period->perid;
        $this->view->perid=$perid;

    }

    public function getperiodsAction(){
        try {
            $this->_helper->layout()->disableLayout();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;

            $anio = $this->_getParam('anio');
            $anio = substr($anio, -2);

            $periodsDb = new Api_Model_DbTable_Periods();
            $where = array('eid'=>$eid, 'oid'=>$oid, 'year'=>$anio);
            //print_r($where);
            $periods = $periodsDb->_getPeriodsxYears($where);
            $this->view->periods = $periods;

            $this->view->peridAct=$this->sesion->period->perid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function viewAction(){
    	try{
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = base64_decode($this->_getParam('perid'));
            $facid=$this->_getParam('facid');
            $borrador=$this->_getParam('borrador');

            $distri= new Distribution_Model_DbTable_Distribution();
            $dbescuela= new Api_Model_DbTable_Speciality();
            $ldistribution=new Distribution_Model_DbTable_logObsrvationDistribution();
            if ($facid=="TODO") {
                $where=array("eid"=>$eid, "oid"=>$oid,"perid"=>$perid);
                $order=array('state','escid');
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
                    $orders=array('state');
                    $datadis=$distri->_getFilter($where,$attrib=null,$orders);
                    if ($datadis[0]) {
                        $dis[$i]=$datadis[0];
                        $dis[$i]['name']=$datae['name'];
                        $i++;
                    }
                }
            }
            if ($borrador=="B") {
                $this->view->valor=1;
            }
            $i=0;
            $dataord=$dis;
            foreach ($dataord as $c => $data) {
                switch ($data['state']) {
                    case 'A':
                        $dis[$c]['order']=1;
                        break;
                    case 'O':
                        $dis[$c]['order']=2;
                        break;
                    case 'C':
                        $dis[$c]['order']=3;
                        break;
                    case 'I':
                        $dis[$c]['order']=3;
                        break;
                    case 'B':
                        $dis[$c]['order']=4;
                        break;
                    default:
                        $dis[$c]['order']=0;
                        break;
                }
            }

            foreach ($dis as $co => $datas) {
                $position[$co]= $datas['order'];
            }
//            array_multisort($position, SORT_ASC, $dis);
            foreach ($dis as $disi) {
                $wher=array('eid'=>$disi['eid'],'oid'=>$disi['oid'],'escid'=>$disi['escid'],'subid'=>$disi['subid'],'distid'=>$disi['distid'],
                        'perid'=>$disi['perid']);
                $dataobs=$ldistribution->_getUltimateObservation($wher);
                $dataobsc=$ldistribution->_getUltimateObservationstatec($wher);
                if ($dataobsc) {
                    // $dis[$i]['state']=$dataobs[0]['state'];
                    $dis[$i]['observationver']=$dataobsc[0]['observation'];
                    $dis[$i]['comments']=$dataobsc[0]['comments'];
                }
                if ($dataobs) {
                    $dis[$i]['observation']=$dataobs[0]['observation'];
                }
                $i++;
            }

            $this->view->dis=$dis;
            $this->view->facid=$facid;
        }catch(Exception $ex){
            print "Error: Cargar ".$ex->getMessage();

        }
    }

    public function distributionAction(){
        $this->_helper->layout()->disablelayout();
    	$eid=$this->sesion->eid;
    	$oid=$this->sesion->oid;
    	$perid = base64_decode($this->_getParam("perid"));
    	$escid = base64_decode($this->_getParam("escid"));
    	$distid = base64_decode($this->_getParam("distid"));
    	$subid = base64_decode($this->_getParam("subid"));
    	$obs = base64_decode($this->_getParam("observation"));

        $formData['eid']=$eid;
        $formData['oid']=$oid;
        $formData['escid']=$escid;
        $formData['perid']=$perid;
        $formData['distid']=$distid;
        $formData['subid']=$subid;
        $json = array('status' => false);

        if ($obs) {
            $ldistribution=new Distribution_Model_DbTable_logObsrvationDistribution();
            $formData['state']='A';
            $formData['observation']=$obs;
            $formData['register']=$this->sesion->uid;
            if ($ldistribution->_save($formData)) {
                $distr = new Distribution_Model_DbTable_Distribution();
                $pk=array('eid'=>$eid,'oid'=>$oid,'distid'=>$distid,'escid'=>$escid,'subid'=>$subid,'perid'=>$perid);
                $data=array('state'=>'O');
                $distr->_update($data,$pk);
                $json = array('status'=>true);
            }
        }
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->data = $json;
    }

    public function closedistributionAction(){
        try {
            // Dbs
            $dbdistribution=new Distribution_Model_DbTable_Distribution();
            $ldistribution=new Distribution_Model_DbTable_logObsrvationDistribution();

            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $state=base64_decode($this->_getParam("state"));
            $escid=base64_decode($this->_getParam("escid"));
            $subid=base64_decode($this->_getParam("subid"));
            $distid=base64_decode($this->_getParam("distid"));
            $perid=base64_decode($this->_getParam("perid"));

            $pk=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'distid'=>$distid,
                            'perid'=>$perid);
            $dataobs=$ldistribution->_getUltimateObservation($pk);
            if ($state=="C") {
                $data=array('state'=>"C");
            }
            $dbdistribution->_update($data,$pk);

            if ($dataobs) {
                $pk['logobdistrid']=$dataobs[0]['logobdistrid'];
                $ldistribution->_update($data,$pk);
            }


        } catch (Exception $e) {
            print "Error:".$e->getMessage();
        }

    }
}
