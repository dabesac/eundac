<?php

class Profile_PublicController extends Zend_Controller_Action {

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

    public function studentAction()
    {
        try{
        	$eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $fullname=$this->sesion->infouser['fullname'];
            $dateborn=$this->sesion->infouser['birthday'];

            $datos[0]=array("eid"=>$eid,"oid"=>$oid,"fullname"=>$fullname, "uid"=>$uid, "pid"=>$pid, "birthday"=>$dateborn, "escid"=>$escid, "subid"=>$subid);

            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid);
            //print_r($where);
            $dbfacesp=new Api_Model_DbTable_Speciality();
            $datos[1]=$dbfacesp->_getFacspeciality($where);
            $this->view->datos=$datos;

    	}catch(exception $e){
    		print "Error : ".$e->getMessage();
    	}
    }

    public function studentinfoAction()
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
            print "Error: ".$e->getMessage();
        }
    }

    public function studenteditinfoAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbperson=new Api_Model_DbTable_Person();
            $where=array("eid"=>$eid, "pid"=>$pid);
            $person=$dbperson->_getOne($where);
            //print_r($person);

            $form= new Profile_Form_Userinfo();
            $this->view->form=$form;
            $form->populate($person);

            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['save']);
                    trim($formdata['numdoc']);
                    trim($formdata['birthday']);
                    trim($formdata['sex']);
                    trim($formdata['civil']);
                    trim($formdata['mail_person']);
                    trim($formdata['mail_work']);
                    trim($formdata['phone']);
                    trim($formdata['cellular']);
                    //print_r($formdata);
                    $upduser=$dbperson->_update($formdata, $where);
                    $this->_redirect("/profile/public/student");
                }
                else
                {
                    echo "Ingrese Nuevamente";
                }
            }

        }catch(exception $e){
            print "Error ".$e->getMessage();
        }
    }

    public function studentfamilyAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbfam=new Api_Model_DbTable_Relationship();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $famrel=$dbfam->_getFilter($where);
            $c=0;
            foreach ($famrel as $f) {
                $where=array("eid"=>$eid, "famid"=>$f['famid']);
                $famdata[$c]=$dbfam->_getInfoFamiliars($where);
                $c++;
            }
            //print_r($famdata);
            $this->view->famrel=$famrel;
            $this->view->famdata=$famdata;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsavefamilyAction(){
        try{
            //$this->_helper->layout()->disableLayout();

            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $form=new Profile_Form_Family();
            $this->view->form=$form;

            $dbfamily=new Api_Model_DbTable_Family();
            $dbrelation=new Api_Model_DbTable_Relationship();

            // $relationdata=array("eid"=>$eid, "pid"=>$pid ,"type"=>trim($formdata["type"]),"assignee"=>trim($formdata["assignee"]));
            // print_r($firstdata);
            // $save=$dbrelation->_save($firstdata);

            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if ($form->isValid($formdata)) {

                    $type=trim($formdata["type"]);
                    $assignee=trim($formdata["assignee"]);
                    unset($formdata["save"]);
                    unset($formdata["type"]);
                    unset($formdata["assignee"]);
                    $formdata["eid"]=$eid;
                    trim($formdata["lastname"]);
                    trim($formdata["firtsname"]);
                    trim($formdata["live"]);
                    trim($formdata["sex"]);
                    trim($formdata["typedoc"]);
                    trim($formdata["numdoc"]);
                    trim($formdata["ocupacy"]);
                    trim($formdata["birthday"]);
                    trim($formdata["health"]);
                    trim($formdata["phone"]);
                    trim($formdata["address"]);

                    $save=$dbfamily->_save($formdata);

                    $where=array("eid"=>$eid, "numdoc"=>trim($formdata["numdoc"]));
                    $attrib=array("famid");
                    $famid=$dbfamily->_getFilter($where, $attrib);
                    
                    $relationdata=array("eid"=>$eid, "pid"=>$pid , "famid"=>$famid[0]['famid'],"type"=>$type,"assignee"=>$assignee);
                    //print_r($relationdata);
                    $saver=$dbrelation->_save($relationdata);



                }
            }

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentacademicAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbacadata=new Api_Model_DbTable_Academicrecord();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $acadata=$dbacadata->_getFilter($where);
            //print_r($acadata);
            $this->view->acadata=$acadata;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studenteditacademicAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $form=new Profile_Form_Academic();
            $this->view->form=$form;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentstatisticAction()
    {
        try{
            $this->_helper->layout()->disableLayout();

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentlaboralAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dblaboral=new Api_Model_DbTable_Jobs();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $laboral=$dblaboral->_getFilter($where);
            print_r($laboral);
            $this->view->laboral=$laboral;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studenteditlaboralAction()
    {
        try{
            $this->_helper->layout->disableLayout();

            $form=new Profile_Form_Laboral();
            $this->view->form=$form;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentinterestAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbinteres=new Api_Model_DbTable_Interes();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $interes=$dbinteres->_getFilter($where);
            //print_r($interes);
            $this->view->interes=$interes;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsaveinterestAction()
    {
        try{
           $form=new Profile_Form_Interest();
           $this->view->form=$form;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }

    }

    public function studentsigncurrentAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid=$this->sesion->next;

            

            $dbcuract=new Api_Model_DbTable_Registrationxcourse();
            $dbtyperate=new Api_Model_DbTable_PeriodsCourses();

            //$where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid, "perid"=>$perid);
            //print_r($this->sesion);
            $curact=$dbcuract->_getFilter($where, $attrib);
            //print_r($curact);
            $nc=0;
            foreach ($curact as $cur) {
                $where=array("eid"=>$eid, "oid"=>$oid, "perid"=>$perid, "courseid"=>$cur['courseid'], "turno"=>$cur['turno'], "curid"=>$cur['curid']);
                $attrib=array("type_rate");
                //print_r($where);
                $typerate[$nc]=$dbtyperate->_getFilter($where,$attrib);
                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cur['courseid']);
                $attrib=array("name");
                $name[$nc]=$dbcuract->_getInfoCourse($where,$attrib);
                $nc++;
            }
            //print_r($name);
            $this->view->typerate=$typerate;
            $this->view->name=$name;
            $this->view->curact=$curact;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsignpercurAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
         
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;            
            $perid="13B";
            //print_r($this->sesion);

            $dbcur=new Api_Model_DbTable_Studentxcurricula();
            $dbcourxcur=new Api_Model_DbTable_Course();
            $dbcourlle=new Api_Model_DbTable_Registrationxcourse();


            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "uid"=>$uid, "pid"=>$pid);
            //print_r($where);
            $cur=$dbcur->_getOne($where);
            //print_r($cur);
            $courpercur=$dbcourxcur->_getCoursesXCurriculaXShool($eid,$oid,$cur['curid'],$escid);
            $c=0;
            foreach ($courpercur as $cour) {
                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cour['courseid'], "curid"=>$cur['curid'],"pid"=>$pid,"uid"=>$uid);
                $attrib=array("courseid","notafinal","perid");
                //print_r($where);
                $courlle[$c]=$dbcourlle->_getFilter($where, $attrib);
                $c++;
            }
            //print_r($courpercur);
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid,"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
            $attrib=array("courseid","state");
            $courlleact=$dbcourlle->_getFilter($where,$attrib);
            //print_r($courlleact);

            $this->view->courpercur=$courpercur;
            $this->view->courlleact=$courlleact;
            $this->view->courlle=$courlle;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsignrealizedAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;

            $dbsignr=new Api_Model_DbTable_Registrationxcourse();
            $dbnamper=new Api_Model_DbTable_Periods();

            $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid);
            $attrib=array("perid","courseid");
            $order=array("perid");
            $signr=$dbsignr->_getFilter($where, $attrib, $order);
            //print_r($signr);
            $per="0";
            $c=0;

            $attrib=array("name");
            $attribcour=array("courseid");
            foreach ($signr as $sperid) {
                if($sperid['perid']<>$per){
                    $where=array("eid"=>$eid, "oid"=>$oid, "perid"=>$sperid['perid']);
                    $namper[$c]=$dbnamper->_getFilter($where,$attrib);


                    $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid, "escid"=>$escid, "perid"=>$sperid['perid']);
                    //print_r($where);
                    $courxper=$dbsignr->_getFilter($where,$attribcour);
                    $x=0;
                    foreach ($courxper as $cour) {
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cour['courseid']);
                        //print_r($where);
                        $courname[$c][$x]=$dbsignr->_getInfoCourse($where, $attrib);
                        $x++;
                    }

                    $per=$sperid['perid'];
                    $c++;
                }
            }
            //print_r($courname);

            $this->view->courname=$courname;
            $this->view->namper=$namper;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

}
