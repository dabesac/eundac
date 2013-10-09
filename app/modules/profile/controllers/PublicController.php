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
    public function studentmaininfoAction()
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

    public function studentinfoAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            
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

//DatosFamiliares--------------------------------------------
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
            $this->_helper->layout()->disableLayout();

            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $form=new Profile_Form_Family();
            $this->view->form=$form;

            $dbfamily=new Api_Model_DbTable_Family();
            $dbrelation=new Api_Model_DbTable_Relationship();
            $where=array("eid"=>$eid, "pid"=>$pid);
            $attrib=array("type");
            $relation=$dbrelation->_getFilter($where, $attrib);
            $pa=0;
            $ma=0;
            foreach ($relation as $rel) {
                if($rel["type"]=="PA"){
                    $pa=1;
                }
                if($rel["type"]=="MA"){
                    $ma=1;
                }
            }
            if ($pa==0) {
                $form->type->addMultiOption("PA","Padre");
            }
            if ($ma==0) {
                $form->type->addMultiOption("MA","Madre");
            }
            $form->type->addMultiOption("HE","Hermano/a");
            $form->type->addMultiOption("HI","Hijo/a");


            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if ($form->isValid($formdata)) {

                    $type=$formdata["type"];
                    $assignee=$formdata["assignee"];
                    unset($formdata["save"]);
                    unset($formdata["type"]);
                    unset($formdata["assignee"]);
                    $formdata["birthday"]=$formdata["year"]."-".$formdata["month"]."-".$formdata["day"];
                    unset($formdata["year"]);
                    unset($formdata["month"]);
                    unset($formdata["day"]);
                    if($formdata["type"]=="PA"){
                        $formdata["sex"]="M";
                    }elseif($formdata["type"]=="MA"){
                        $formdata["sex"]="F";
                    }
                    $formdata["eid"]=$eid;
                    trim($formdata["numdoc"]);
                    if($formdata["live"]=="N")
                        {
                            $formdata["ocupacy"]="_";
                            $formdata["phone"]="_";
                            $formdata["address"]="_";
                        }

                    $save=$dbfamily->_save($formdata);
                    print_r("Se Guardo con Exito");
                    
                    //print_r($save);

                    $relationdata=array("eid"=>$eid, "pid"=>$pid , "famid"=>$save,"type"=>$type,"assignee"=>$assignee);
                    //print_r($relationdata);
                    $saver=$dbrelation->_save($relationdata);

                }
            }

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }
//---------------------------------------------------------------------





//Datos Academicos-----------------------------------------------------
    public function studentacademicAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $data=array("eid"=>$eid, "pid"=>$pid);

            $dbacadata=new Api_Model_DbTable_Academicrecord();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $acadata=$dbacadata->_getFilter($where);
            //print_r($acadata);

            $this->view->data=$data;
            $this->view->acadata=$acadata;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsaveacademicAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbacademic=new Api_Model_DbTable_Academicrecord();

            $form=new Profile_Form_Academic();
            $this->view->form=$form;

            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if($form->isValid($formdata)){
                    unset($formdata["save"]);
                    $formdata["eid"]=$eid;
                    $formdata["pid"]=$pid;
                    trim($formdata["location"]);
                    trim($formdata["type"]);
                    trim($formdata["institution"]);
                    trim($formdata["year_end"]);
                    trim($formdata["title"]);
                    $academic=$dbacademic->_save($formdata);
                    print_r("Se Guardo con Exito");
                }
            }
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentremoveacademicAction(){
        try{
            $eid=$this->_getParam("eid");
            $pid=$this->_getParam("pid");
            $acid=$this->_getParam("acid");

            $dbacademic=new Api_Model_DbTable_Academicrecord();
            $where=array("eid"=>$eid, "pid"=>$pid, "acid"=>$acid);

            if($dbacademic->_delete($where)){
                $this->_redirect("/profile/public/student");
            }else{
                echo "Error al Eliminar";
            }
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }
//-----------------------------------------------------------------



//Datos Estadisticos-------------------------------------------------

    public function studentstatisticAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;

            $dbsta=new Api_Model_DbTable_Statistics();
            $where=array("eid"=>$eid, "oid"=>$oid, "uid"=>$uid, "pid"=>$pid, "escid"=>$escid, "subid"=>$subid);
            $sta=$dbsta->_getOne($where);
            $this->view->statistic=$sta;
            //print_r($sta);

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsavestatisticAction()
    {
        try{
            $this->_helper->layout()->disableLayout();

            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;

            $dbsta=new Api_Model_DbTable_Statistics();
            //$where=array("eid"=>$eid, "oid"=>$oid, "uid"=>$uid, "pid"=>$pid, "escid"=>$escid, "subid"=>$subid);
            //$sta=$dbsta->_getOne($where);

            $form=new Profile_Form_Statistic();
            $this->view->form=$form;
            //$form->populate($sta);
            
            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if ($form->isValid($formdata)) {
                    $formdata["eid"]=$eid;
                    $formdata["oid"]=$oid;
                    $formdata["uid"]=$uid;
                    $formdata["pid"]=$pid;
                    $formdata["escid"]=$escid;
                    $formdata["subid"]=$subid;
                    $formdata["state"]="T";
                    if($formdata["dependen_ud"]=="N"){
                        $formdata["num_dep_ud"]=0;
                    }
                    $save=$dbsta->_save($formdata);
                    print_r("Se Guardo con Exito");
                }
            }
        }catch(exception $e){
            print "Error ! ".$e->getMessage();
        }

    }
//------------------------------------------------------------------



//Datos Laborales-------------------------------------------

    public function studentlaboralAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $data=array("eid"=>$eid, "pid"=>$pid);

            $dblaboral=new Api_Model_DbTable_Jobs();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $laboral=$dblaboral->_getFilter($where);
            //print_r($laboral);

            $this->view->data=$data;
            $this->view->laboral=$laboral;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsavelaboralAction()
    {
        try{
            $this->_helper->layout->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbjob=new Api_Model_DbTable_Jobs();

            $form=new Profile_Form_Laboral();
            $this->view->form=$form;

            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if($form->isValid($formdata)){
                    unset($formdata["save"]);
                    $formdata["eid"]=$eid;
                    $formdata["pid"]=$pid;
                    trim($formdata["company"]);
                    trim($formdata["industry"]);
                    trim($formdata["salary"]);
                    trim($formdata["condition"]);

                    $job=$dbjob->_save($formdata);
                    print_r("Se Guardo con Exito");
                }
            }

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentremovelaboralAction(){
        try{
            $eid=$this->_getParam("eid");
            $pid=$this->_getParam("pid");
            $lid=$this->_getParam("lid");

            $dblaboral=new Api_Model_DbTable_Jobs();
            $where=array("eid"=>$eid, "pid"=>$pid, "lid"=>$lid);

            if($dblaboral->_delete($where)){
            }else{
                echo "Error al Eliminar";
            }
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }
//------------------------------------------------------------------------



//Intereses--------------------------------------------------------------

    public function studentinterestAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $data=array("eid"=>$eid, "pid"=>$pid);

            $dbinteres=new Api_Model_DbTable_Interes();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $interes=$dbinteres->_getFilter($where);
            //print_r($interes);
            $this->view->interes=$interes;
            $this->view->data=$data;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsaveinterestAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $dbinterest=new Api_Model_DbTable_Interes();

            $form=new Profile_Form_Interest();
            $this->view->form=$form;
            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if($form->isValid($formdata)){
                    unset($formdata["save"]);
                    $formdata["eid"]=$eid;
                    $formdata["pid"]=$pid;
                    trim($formdata["discipline"]);
                    trim($formdata["title"]);
                    trim($formdata["club"]);
                    $interest=$dbinterest->_save($formdata);
                    print_r("Se Guardo con Exito");
                }
            }
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentremoveinterestAction()
    {
        try{
            $eid=$this->_getParam("eid");
            $pid=$this->_getParam("pid");
            $iid=$this->_getParam("iid");

            $dbinterest=new Api_Model_DbTable_Interes();
            $where=array("eid"=>$eid, "pid"=>$pid, "iid"=>$iid);

            if($dbinterest->_delete($where)){

            }else{
                echo "Error al Eliminar";
            }
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }
//-------------------------------------------------------------------




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
            $perid=$this->sesion->period->perid;
            $rid=$this->sesion->rid;

            $data=array("pid"=>$pid, "uid"=>$uid, "escid"=>$escid, "subid"=>$subid, "perid"=>$perid, "rid"=>$rid);

            $dbcuract=new Api_Model_DbTable_Registrationxcourse();
            $dbtyperate=new Api_Model_DbTable_PeriodsCourses();

            $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid, "perid"=>$perid);
            $attrib=array("courseid", "turno","curid","promedio1","promedio2","nota4_i","nota9_i","nota4_ii","nota9_ii","notafinal");
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
            $this->view->data=$data;
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
                $attrib=array("courseid","notafinal","perid","turno");
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
            //print_r($courlle);
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
