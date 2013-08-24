<?php

class Register_ListcurrentnotesController extends Zend_Controller_Action {

    public function init()
    {
		/*$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;*/
      	$this->eid="20154605046";
      	$this->oid="1";
      	$this->escid="4SI";
      	$this->subid="1901";
      	$this->pid="0924403185";
      	$this->uid="0924403185";
    }

    public function indexAction()
    {
    	try{
    		$eid=$this->eid;
    		$oid=$this->oid;
    		$escid=$this->escid;
    		$subid=$this->subid;
    		$pid=$this->pid;
    		$uid=$this->uid;
    		$perid="13B";

    		$dbcoursescp=new Api_Model_DbTable_Registrationxcourse();

    		$where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
    		/*$coursescp=$dbcoursescp->_getFilter($where);
    		print_r($coursescp);*/

    		$coursesid=array("0"=>"13101","1"=>"13102","2"=>"13103","3"=>"13104");
    		$curid="13A4SI";
    		$notes[0]=array("nota1_i"=>"13","nota2_i"=>"17","promedio1"=>"15");
    		$notes[1]=array("nota1_i"=>"12","nota2_i"=>"9","nota3_i"=>"14","nota4_i"=>"10","nota5_i"=>"13","nota6_i"=>"19","nota7_i"=>"13","nota8_i"=>"19","nota9_i"=>"13","promedio1"=>"19","nota1_ii"=>"12","nota2_ii"=>"09","nota3_ii"=>"14","nota4_ii"=>"11","nota5_ii"=>"15","nota6_ii"=>"16","nota7_ii"=>"16","nota8_ii"=>"16","nota9_ii"=>"12","promedio2"=>"15","notafinal"=>"14");
    		$notes[2]=array("nota1_i"=>"12","nota2_i"=>"09","nota3_i"=>"12","nota4_i"=>"11","promedio1"=>"11");
    		$notes[3]=array("nota1_i"=>"18","nota2_i"=>"11","nota3_i"=>"12","nota4_i"=>"13","nota5_i"=>"15","promedio1"=>"14");
            $notes[4]=array("nota1_i"=>"12","nota2_i"=>"9","nota3_i"=>"14","nota4_i"=>"10","nota6_i"=>"19","nota7_i"=>"13","nota8_i"=>"19","nota9_i"=>"13","nota1_ii"=>"12","nota2_ii"=>"09","nota3_ii"=>"14","nota4_ii"=>"11","nota6_ii"=>"16","nota7_ii"=>"11","nota8_ii"=>"15","nota9_ii"=>"12","notafinal"=>"17");

    		$nc=0;
    		$c=0;
    		$nm=0;
    		foreach ($coursesid as $coid) {
				$where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"curid"=>$curid,"courseid"=>$coid);
    			//print_r($where);
    			$courses[$nc]=$dbcoursescp->_getInfoCourse($where);
                //print_r($courses);
    			foreach ($notes[$nc] as $not) {
	    			//print_r($notes[$nc]);
	    			$c++;
	    		}
	    		if($c>$nm){
	    			$nm=$c;
                }
                $c=0;
                $nc++;
            }
            $courses[$nc][0]['courseid']="COM001";
            $courses[$nc][0]['type']="C";
            $courses[$nc][0]['name']="Competencia";
            //print_r($nm);
    		$notes['nm']=$nm;
    		//print_r($courses);
    		
    		$this->view->notes=$notes;
    		//print_r($notes);
    		$this->view->courses=$courses;

    	}catch(Exception $ex){
            print "Error: Cargar ".$ex->getMessage();

    	}
    }
}
