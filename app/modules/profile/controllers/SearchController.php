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


    public function cambiocurriculaAction(){
      try {

          
            

      } catch (Exception $e) {
        print "Error: get Horary".$e->getMessage();
      }
    }

 	
 }
