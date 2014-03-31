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

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }

    }


    public function adminfoAction()
    {
        try{
            $this->_helper->layout()->disableLayout();

            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $escid=$this->sesion->escid;
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $subid = $this->sesion->subid;

            $data=array("eid"=>$eid,"pid"=>$pid);

            $dbperson=new Api_Model_DbTable_Person();
            $where=array("eid"=>$eid, "pid"=>$pid);
            $datos[3]=$dbperson->_getOne($where);
           
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
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbperson=new Api_Model_DbTable_Person();
            $where=array("eid"=>$eid, "pid"=>$pid);
            $person=$dbperson->_getOne($where);
            $person["year"]=substr($person["birthday"], 0, 4);
            $person["month"]=substr($person["birthday"], 5, 2);
            $person["day"]=substr($person["birthday"], 8, 2);
            //print_r($person);

            $form= new Profile_Form_Userinfo();
            $this->view->form=$form;
            $form->populate($person);

            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    trim($formdata['numdoc']);
                    $formdata["birthday"]=$formdata["year"]."-".$formdata["month"]."-".$formdata["day"];
                    unset($formdata['year']);
                    unset($formdata['month']);
                    unset($formdata['day']);
                    trim($formdata['sex']);
                    trim($formdata['civil']);
                    trim($formdata['mail_person']);
                    trim($formdata['mail_work']);
                    trim($formdata['phone']);
                    trim($formdata['cellular']);
                    //print_r($formdata);
                    print_r("Se Guardo con Exito");
                    $upduser=$dbperson->_update($formdata, $where);
                    //$this->_redirect("/profile/public/student");
                }
                else
                {
                     //$this->_redirect("/profile/public/student");
                }
            }

        }catch(exception $e){
            print "Error ".$e->getMessage();
        }
    }

    public function saveinfoAction(){
        $this->_helper->layout()->disableLayout();
        echo 'Joder';
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



}
