<?php
class Admin_OpendistributionController extends Zend_Controller_Action{
	public function init(){
                $sesion  = Zend_Auth::getInstance();
                if(!$sesion->hasIdentity() ){
                        $this->_helper->redirector('index',"index",'default');
                }
                $login = $sesion->getStorage()->read();
                $this->sesion = $login;
        }
        public function indexAction(){
		
                $eid = $this->sesion->eid;
                $oid = $this->sesion->oid;
                $where=array('eid'=>$eid,'oid'=>$oid);
                $dbfaculty=new Api_Model_DbTable_Faculty();
                $dataf=$dbfaculty->_getAll($where);
                /*foreach ($dataf as $c => $data) {
                        $facultades[$c]["facid"]=$data["facid"];
                        $facultades[$c]["name"]=$data["name"];
                }
                print_r($facultades);exit();*/
                $this->view->dataf=$dataf;
                
	}
        public function lperiodoAction(){
                try {
                        $this->_helper->layout()->disableLayout();
                        $eid=$this->sesion->eid;
                        $oid=$this->sesion->oid;
                        $anio=$this->_getParam('anio');
                        //$anio = $this->getParam('anio');
                        $anio = substr($anio,-2);
                        $periodosDb = new Api_Model_DbTable_Periods();
                        $where = array('eid'=>$eid, 'oid'=>$oid, 'year'=>$anio);
                        //print_r($where);
                        $periods = $periodosDb->_getPeriodsxYears($where);
                        $this->view->periods = $periods;

                } catch (Exception $e) {
                        print 'Error '.$e->getMessage();
                }
        }
        public function viewAction(){
                try {
                      $this->_helper->layout()->disableLayout();
                
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;

                $perid = $this->_getParam("perid");
                $facid = $this-> _getParam("facid");
                
                #######################################################

                $distri= new Distribution_Model_DbTable_Distribution();
            $dbescuela= new Api_Model_DbTable_Speciality();
            $ldistribution=new Distribution_Model_DbTable_logObsrvationDistribution();
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
            $i=0;
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
            // print_r($dis);
            // exit();
            $this->view->dis=$dis;  
                } catch (Exception $e) {
                        
                }
                

        }
}