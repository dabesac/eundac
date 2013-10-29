<?php
 class Profile_SearchController extends Zend_Controller_Action{

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

      $facid=$this->sesion->faculty->facid;
      $this->view->facid=$facid;
 			$eid=$this->sesion->eid;
 			$oid=$this->sesion->oid;
 			$escid=$this->sesion->escid;
 			$uid=$this->sesion->uid;
 			$pid=$this->sesion->pid;
 			$subid=$this->sesion->subid;
 			$perid=$this->sesion->period->perid;


            $rid = $this->_getParam("rid");
            $nombre=$this->_getParam("nombre");
			
            $p1 = substr($nombre, 0, 8);

            if (is_numeric($p1))
              {
               //echo "Es un número";
               //echo $nombre;
               $uid=$nombre;

                   if ($uid<>'')
                   {
                    $where['uid'] = $uid;
                    $where['rid'] = $rid;
                    $where['eid'] = $this->sesion->eid;
                    $where['oid'] = $this->sesion->oid;

                    //print_r($where);exit;
                   
                    $bdu = new Api_Model_DbTable_Users();
                    $data = $bdu->_getUserXRidXUid($where);
                    //print_r($data);
                    $this->view->data=$data;
                   
                   }
              }
              else  
              {
              //echo "Es una Cadena"; 
                // $tnombre =split(" ",$nombre);
                // $ape_pat=$tnombre[0];
                // $ape_mat=$tnombre[1];
                    $where['eid'] = $this->sesion->eid;
                    $where['oid'] = $this->sesion->oid;
                    $where['rid'] = $rid;
                    $where['nom'] = $nombre;
                    $where['nom'] = trim(strtoupper($nombre));
                    $where['nom'] = mb_strtoupper($where['nom'],'UTF-8');

                    $bdu = new Api_Model_DbTable_Users();
                    $da = $bdu->_getUsuarioXNombre($where);

                    $this->view->data=$da;
     
              }
          

 		} catch (Exception $e) {
 			print "Error: get Horary".$e->getMessage();
 		}
 	}


   public function studentsignpercurAction()
    {
        try{
            //$this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid; 

            $uid =$this->_getParam('uid');
            $pid =$this->_getParam('pid');
            $subid =$this->_getParam('subid');
            $escid =$this->_getParam('escid');

            //Datos del Buscado
            $dbperson=new Api_Model_DbTable_Users();
            $dbfacesp=new Api_Model_DbTable_Speciality();
            $where=array("eid"=>$eid, "oid"=>$oid,"uid"=>$uid,"pid"=>$pid,"escid"=>$escid,"subid"=>$subid);
            $person=$dbperson->_getInfoUser($where);
            
            $where=array("eid"=>$eid, "oid"=>$oid,"escid"=>$escid);
            $person["facesp"]=$dbfacesp->_getFacspeciality($where);
            $this->view->person=$person;
            
            $perid=$this->sesion->period->perid;

            $dbcur=new Api_Model_DbTable_Studentxcurricula();
            $dbcourxcur=new Api_Model_DbTable_Course();
            $dbcourlle=new Api_Model_DbTable_Registrationxcourse();


            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "uid"=>$uid, "pid"=>$pid);
            $cur=$dbcur->_getOne($where);
            $courpercur=$dbcourxcur->_getCoursesXCurriculaXShool($eid,$oid,$cur['curid'],$escid);
            $c=0;
            $courlle=array();
            if ($courpercur){
	            foreach ($courpercur as $cour) {
	                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cour['courseid'], "curid"=>$cur['curid'],"pid"=>$pid,"uid"=>$uid);
	                $attrib=array("courseid","notafinal","perid");
	                $courlle[$c]=$dbcourlle->_getFilter($where, $attrib);
	                $c++;
	            }
            }
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid,"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
            $attrib=array("courseid","state");
            $courlleact=$dbcourlle->_getFilter($where,$attrib);

            $this->view->courpercur=$courpercur;
            $this->view->courlleact=$courlleact;
            $this->view->courlle=$courlle;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

 	
 }
