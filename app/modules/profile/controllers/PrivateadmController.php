<?php

class Profile_PrivateadmController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
            
        //$this->sesion->perid="13A";
    }

    public function indexAction()
    {

    }

    public function admAction()
    {
        try{
            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $escid=$this->sesion->escid;
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $rid=$this->sesion->rid;         
 
            $dbperson=new Api_Model_DbTable_Person();
            $where=array("eid"=>$eid, "pid"=>$pid);
            $attrib=array("last_name0","last_name1","first_name","pid");
            $person=$dbperson->_getFilter($where,$attrib);
            $person['uid']=$uid;
            $person['eid']=$eid;
            $person['oid']=$oid;
            //print_r($person);
            
            $dbfacesp=new Api_Model_DbTable_Speciality();
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid);
            $facesp=$dbfacesp->_getFacspeciality($where);

            //print_r($facesp);

            $this->view->facesp=$facesp;
            $this->view->person=$person;
            $this->view->rid=$rid;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }

    }


    public function adminfoAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            
            //DataBases
            $dbperson=new Api_Model_DbTable_Person();
            $country_dDb = new Api_Model_DbTable_CountryDistrict();
            $country_pDb = new Api_Model_DbTable_CountryProvince();
            $country_sDb = new Api_Model_DbTable_CountryState();
            $countryDb   = new Api_Model_DbTable_Country();

            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $escid=$this->sesion->escid;
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $subid = $this->sesion->subid;

            $data=array("eid"=>$eid,"pid"=>$pid);

            $where=array("eid"=>$eid, "pid"=>$pid);
            $datos[3]=$dbperson->_getOne($where);

            if($datos[3]['location']) {
                //Distrito
                $where = array( 'disid' => $datos[3]['location'] );
                $distrito = $country_dDb->_getOne($where);
                $datosUbicacion['distrito'] = $distrito['name_d'];

                //Provincia
                $where = array( 'proid' => $distrito['proid'] );
                $provincia = $country_pDb->_getOne($where);
                $datosUbicacion['provincia'] = $provincia['name_p'];

                //Departamento
                $where = array( 'cosid' => $provincia['cosid'] );
                $departamento = $country_sDb->_getOne($where);
                $datosUbicacion['departamento'] = $departamento['name_s'];

                //Departamento
                $where = array( 'coid' => $departamento['coid'] );
                $pais = $countryDb->_getOne($where);
                $datosUbicacion['pais'] = $pais['name_c'];

                $this->view->datosUbicacion = $datosUbicacion;
            }
           
            $dbdetingreso=new Api_Model_DbTable_Studentsignin();
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "pid"=>$pid, "uid"=>$uid);
            $datos[4]=$dbdetingreso->_getOne($where);

            //print_r($datos);

            $this->view->data=$data;
            $this->view->datos=$datos;
        }catch(exception $e){
            print "Error! ".$e->getMessage();
        }
    }


    public function admeditinfoAction(){
        try{
            $this->_helper->layout()->disableLayout();

            //DataBases
            $dbperson=new Api_Model_DbTable_Person();
            $country_dDb = new Api_Model_DbTable_CountryDistrict();
            $country_pDb = new Api_Model_DbTable_CountryProvince();
            $country_sDb = new Api_Model_DbTable_CountryState();

            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dataPerson = array('eid' => $eid,
                                'pid' => $pid );

            $this->view->dataPerson = $dataPerson;

            $where=array("eid"=>$eid, "pid"=>$pid);
            $person=$dbperson->_getOne($where);
            $person["year"]=substr($person["birthday"], 0, 4);
            $person["month"]=substr($person["birthday"], 5, 2);
            $person["day"]=substr($person["birthday"], 8, 2);
            //print_r($person);
            if ($person['location']) {
                //Distrito
                $where = array( 'disid' => $person['location'] );
                $country_d = $country_dDb->_getOne($where);

                //Provincia
                $where = array( 'proid' => $country_d['proid'] );
                $country_p = $country_pDb->_getOne($where);

                //Departamento
                $where = array( 'cosid' => $country_p['cosid'] );
                $country_s = $country_sDb->_getOne($where);

                $person['country_d'] = $person['location'];
                $person['country_p'] = $country_d['proid'];
                $person['country_s'] = $country_p['cosid'];
                $person['country']   = $country_s['coid'];
            }

            $form= new Profile_Form_Userinfo();
            $this->view->form=$form;
            $form->populate($person);

        }catch(exception $e){
            print "Error ".$e->getMessage();
        }
    }

    public function saveinfoAction(){
        $this->_helper->layout()->disableLayout();

        $personDb = new Api_Model_DbTable_Person();
        $form= new Profile_Form_Userinfo();

        $formData = $this->getRequest()->getPost();

        if ($form->isValid($formData)){ 
            $pk = array('eid' => $formData['eid'],
                        'pid' => $formData['pid'] );

            $formData['birthday'] = $formData['year'].'-'.$formData['month'].'-'.$formData['day'];

            $formData['location'] = $formData['country_d'];

            unset(  $formData['eid'], $formData['pid'], $formData['submit'],
                    $formData['country'], $formData['country_s'], $formData['country_p'], $formData['country_d'],
                    $formData['year'], $formData['month'], $formData['day'] );

            if ($personDb->_update($formData, $pk)) {
                echo 1;
            }else{
                echo 2;
            }
        }else{
            echo 0;
        }
    }
    


    public function accessAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $pid=$this->sesion->pid;
            $this->view->pid=$pid;

        }catch(exception $e){
            print "Error ".$e->getMessage();
        }
    }


    public function admmaininfoAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            //print_r($this->sesion);
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $fullname=$this->sesion->infouser['fullname'];
            $dateborn=$this->sesion->infouser['birthday'];

            $datos[0]=array("fullname"=>$fullname, "uid"=>$uid, "pid"=>$pid, "birthday"=>$dateborn);
            
            $dbperson=new Api_Model_DbTable_Person();
            $where=array("eid"=>$eid, "pid"=>$pid);
            $datos[3]=$dbperson->_getOne($where);
           
            $dbdetingreso=new Api_Model_DbTable_Studentsignin();
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "pid"=>$pid, "uid"=>$uid);
            $datos[4]=$dbdetingreso->_getOne($where);
            //print_r($datos);

            $this->view->datos=$datos;
        }catch(exception $e){
            print "Error :".$e->getMessage();
        }
    }

    public function coursescurrentAction(){
        try {
            $this->_helper->layout()->disableLayout();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;
            $perid = $this->sesion->period->perid;

            $where = array('eid'=>$eid, 'oid'=>$oid, 'pid'=>$pid, 'uid'=>$uid, 'perid'=>$perid);

            $coursesxTeacherDb = new Api_Model_DbTable_Coursexteacher();
            $coursesDb = new Api_Model_DbTable_Course();
            $schoolDb = new Api_Model_DbTable_Speciality();

            $courses = $coursesxTeacherDb->_getFilter($where);

            $c = 0;
            $esc = 0;
            $esp = 0;
            $escid = '_';
            foreach ($courses as $course) {
                $attrib = array('name');
                $where = array('eid'=>$eid, 'oid'=>$oid, 'curid'=>$course['curid'], 'courseid'=>$course['courseid']);
                $coursesName[$c] = $coursesDb->_getFilter($where, $attrib);
                $c++;
                if ($escid <> $course['escid']) {
                    $attrib = array('name', 'parent');
                    $escid = $course['escid'];
                    $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid);
                    $schoolName[$esc] = $schoolDb->_getFilter($where, $attrib);
                    $facultyName[$esc] = $schoolDb->_getFacspeciality($where);
                    if ($schoolName[$esc][0]['parent']) {
                        $specialityName[$esp] = $schoolName[$esc][0]['name'];
                        $esp++;
                        $attrib = array('name');
                        $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$schoolName[$esc][0]['parent']);
                        $schoolName[$esc] = $schoolDb->_getFilter($where, $attrib);
                    }else{
                        $schoolName[$esc] = $schoolName[$esc][0]['name'];
                    }
                    $esc++;
                }
            }

            $this->view->courses = $courses;
            $this->view->facultyName = $facultyName;
            $this->view->schoolName = $schoolName;
            $this->view->specialityName = $specialityName;
            $this->view->coursesName = $coursesName;

        } catch (Exception $e) {
            print 'Error'.$e->getMessage();
        }
    }

    public function changepasswordAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
        } catch (Exception $e) {
            print 'Error'.$e->getMessage();
        }
    }
    public function savepasswordAction(){
        try {
                $this->_helper->layout()->disableLayout();

                $formData = $this->getRequest()->getPost();
                $eid = $this->sesion->eid;
                $oid = $this->sesion->oid;
                $uid = $this->sesion->uid;
                $pid = $this->sesion->pid;
                $escid = $this->sesion->escid;
                $subid = $this->sesion->subid;
                //codifica
                $oldpass = md5($formData['oldpass']);
                $newpass = md5($formData['newpass']);
                $repnewpass = md5($formData['repnewpass']);
                //verifica campos vacios
            if ( $formData['oldpass'] <> "" &&  $formData['newpass'] <> "" && $formData['repnewpass'] <>""){
                    $where = array(
                        'eid'=>$eid,
                        'oid'=>$oid,
                        'uid'=>$uid,
                        'pid'=>$pid,
                        'escid'=>$escid,
                        'password'=>$oldpass
                );
                $tb_user = new Api_Model_DbTable_Users();
            
                if (is_array($tb_user->_getFilter($where))) {
                    if($newpass===$repnewpass){
                        $data['password']=$newpass;
                        $data['change_password']="T";
                        $pk['eid']=$eid;
                        $pk['oid']=$oid;
                        $pk['uid']=$uid;
                        $pk['pid']=$pid;
                        $pk['escid']=$escid;
                        $pk['subid']=$subid;
                        
                        $bdu = new Api_Model_DbTable_Users();
                        $veri=$bdu->_update($data,$pk);
                        if ($veri) {
                            print_r(4);
                        }
                     }else{
                        print_r(3);
                     }
                } else {
                print_r(2);
                }          
            }else{
            print_r(1);
            }
            
        } catch (Exception $e) {
            print 'Error'.$e->getMessage();   
        }
    }
    
    public function phonebookAction(){
        try{
            $this->_helper->layout()->disableLayout();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $where = array('eid'=>$eid, 'oid'=>$oid);
            $infoteacher= new Api_Model_DbTable_Infoteacher();
            $datadirectores = $infoteacher->_getDirectores($where);
            $i=0;
            foreach ($datadirectores as $data) {
                $escid = $data['escid'];
                $subid = $data['subid'];
                $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid, 'subid'=>$subid);
                $speciality = new Api_Model_DbTable_Speciality();
                $dataspeciality[$i] = $speciality->_getOne($where);
                $wheres = array('eid'=>$eid, 'oid'=>$oid, 'pid'=>$data['pid']);
                $person = new Api_Model_DbTable_Person();
                $dataperson[$i] = $person->_getOne($wheres);
                $i++;
            }
            $this->view->directores=$datadirectores;
            $this->view->persona=$dataperson;
            $this->view->speciality=$dataspeciality;
            $this->view->mensaje='pool pelador';

        } catch(Exeption $e){
            print 'Error '.$e->getMessage();
        }        
    }
}
