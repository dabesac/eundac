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

    public function validatefullprofileAction(){
        $this->_helper->layout()->disableLayout();
        //DataBases 
        $interestDb     = new Api_Model_DbTable_Interes();
        $relationshipDb = new Api_Model_DbTable_Relationship();
        $academicDb     = new Api_Model_DbTable_Academicrecord();
        $statisticDb    = new Api_Model_DbTable_Statistics();

        $eid = $this->_getParam('eid');
        $oid = $this->_getParam('oid');
        $pid = $this->_getParam('pid');

        $fullProfile = 'yes';

        $where = array( 'eid' => $eid,
                        'pid' => $pid);
        $interest = $interestDb->_getFilter($where);
        if (!$interest) {
            $fullProfile = 'no';
        }

        $family = $relationshipDb->_getFilter($where);
        if (!$family) {
            $fullProfile = 'no';
        }

        $academic = $academicDb->_getFilter($where);
        if (!$academic) {
            $fullProfile = 'no';
        }

        $where = array( 'eid' => $eid,
                        'oid' => $oid,
                        'pid' => $pid);
        $statistic = $statisticDb->_getFilter($where);
        if (!$statistic) {
            $fullProfile = 'no';
        }

        if ($fullProfile == 'yes') {
            $this->sesion->fullProfile->success = 'yes';
        }else{
            $this->sesion->fullProfile->success = 'no';
        }
    }

    public function countrystateAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $coid=$this->_getParam('coid');
            $where=array('coid'=>$coid);

            $dbcountry_s=new Api_Model_DbTable_CountryState();
            $datas=$dbcountry_s->_getAllxCountry($where);
            $this->view->datas=$datas;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function countryprovinceAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $cosid=$this->_getParam('cosid');
            $where=array('cosid'=>$cosid);

            $dbcountry_p=new Api_Model_DbTable_CountryProvince();
            $datap=$dbcountry_p->_getAllxState($where);
            $this->view->datap=$datap;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function countrydistrictAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $proid=$this->_getParam('proid');
            $where=array('proid'=>$proid);

            $dbcountry_d=new Api_Model_DbTable_CountryDistrict();
            $datad=$dbcountry_d->_getAllxProvince($where);
            $this->view->datad=$datad;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function studentAction()
    {
        try{
            $this->view->success = $this->sesion->fullProfile->success;

        	$eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $rid = $this->sesion->rid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid = $this->sesion->period->perid;
            $fullname=$this->sesion->infouser['fullname'];
            $dateborn=$this->sesion->infouser['birthday'];
            $this->view->avatar_default=$this->sesion->infouser['sex'];
            $data=array("eid"=>$eid,"oid"=>$oid,"pid"=>$pid,"uid"=>$uid,"escid"=>$escid,"subid"=>$subid, "perid"=>$perid,"rid"=>$rid);

            $datos[0]=array("eid"=>$eid,"oid"=>$oid,"fullname"=>$fullname, "uid"=>$uid, "pid"=>$pid, "birthday"=>$dateborn, "escid"=>$escid, "subid"=>$subid);

            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid);
            //print_r($where);
            $dbfacesp=new Api_Model_DbTable_Speciality();
            $datos[1]=$dbfacesp->_getFacspeciality($where);
            $this->view->datos=$datos;
            $this->view->data = $data;

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

            $wheres=array('disid'=>$datos[3]['location']);
            $bdubigeo=new Api_Model_DbTable_CountryDistrict();
            $datos[2]=$bdubigeo->_infoUbigeo($wheres);

            $dbdetingreso=new Api_Model_DbTable_Studentsignin();
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "pid"=>$pid, "uid"=>$uid);
            $datos[4]=$dbdetingreso->_getOne($where);

            $this->view->datos=$datos;
        }catch(exception $e){
            print "Error :".$e->getMessage();
        }
    }

    public function studentinfoAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;

            $uidveri=substr($uid,0,2);
            $anio=date('Y');
            $anioveri=substr($anio,2,2);


            $wherep=array('eid'=>$eid,'pid'=>$pid);
            $dbrelation=new Api_Model_DbTable_Relationship();
            $datafami=$dbrelation->_getFilter($wherep);

            $whered=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid,'uid'=>$uid,'escid'=>$escid,'subid'=>$subid);
            $dbdetingreso=new Api_Model_DbTable_Studentsignin();
            $dataingre=$dbdetingreso->_getOne($whered);

            $dbstatistics=new Api_Model_DbTable_Statistics();
            $dataest=$dbstatistics->_getOne($whered);

            $wherea=array('eid'=>$eid,'pid'=>$pid);
            $dbacademic=new Api_Model_DbTable_Academicrecord();
            $datacade=$dbacademic->_getFilter($wherea);

            $wherei=array('eid'=>$eid,'pid'=>$pid);
            $dbinterest=new Api_Model_DbTable_Interes();
            $datainte=$dbinterest->_getFilter($wherei);
            if ($uidveri==$anioveri) {
                if ($datafami && $dataingre && $dataest && $datacade && $datainte) {
                    $this->view->clave=1;
                }
            }
            else{
                if ($datafami && $dataest && $datacade && $datainte) {
                    $this->view->clave=1;
                }   
            }

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'impresion_ficha_estadistica');
            $dataim = $dbimpression->_getFilter($wheri);
            $co=count($dataim);
            if ($co>0) {
                $this->view->state='C';
            }

            $dataStudent = array(   'eid'   => base64_encode($eid),
                                    'oid'   => base64_encode($oid),
                                    'pid'   => base64_encode($pid),
                                    'uid'   => base64_encode($uid),
                                    'escid' => base64_encode($escid),
                                    'subid' => base64_encode($subid) );

            $this->view->dataStudent = $dataStudent;

            $dbsta=new Api_Model_DbTable_Statistics();
            $where=array("eid"=>$eid, "oid"=>$oid, "uid"=>$uid, "pid"=>$pid, "escid"=>$escid, "subid"=>$subid);
            $si=0;
            if($dbsta->_getOne($where)){
                $si=1;
            }
            $this->view->si=$si;

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

            if ($person['location']) {
                $wheres=array('disid'=>$person['location']);
                $bdubigeo=new Api_Model_DbTable_CountryDistrict();
                $dataubigeo=$bdubigeo->_infoUbigeo($wheres);
                $dataubigeo=$dataubigeo[0];                
                $person['country']=$dataubigeo['coid'];
                $person['country_s']=$dataubigeo['cosid'];
                $person['country_p']=$dataubigeo['proid'];
                $person['country_d']=$dataubigeo['disid'];
                $this->view->dataubigeo=$dataubigeo;
                $this->view->veri=1;
            }
            else{
                $this->view->veri=0;
            }

            $person["year"]=substr($person["birthday"], 0, 4);
            $person["month"]=substr($person["birthday"], 5, 2);
            $person["day"]=substr($person["birthday"], 8, 2);
            $form = new Profile_Form_Userinfo();

            if ($this->getRequest()->isPost()){
                $formdata=$this->getRequest()->getPost();
                if ($form->isValid($formdata)){
                    // print_r($formData);
                    $formdata['location']=$formdata['country_d'];
                    $formdata['birthday']=$formdata['year']."-".$formdata['month']."-".$formdata['day'];
                    unset($formdata['country']);
                    unset($formdata['country_s']);
                    unset($formdata['country_p']);
                    unset($formdata['country_d']);
                    unset($formdata['year']);
                    unset($formdata['month']);
                    unset($formdata['day']);
                    $pk=array('eid'=>$eid,'pid'=>$pid);
                    if($dbperson->_update($formdata,$pk)){
                        $this->view->clave=1;
                    }
                    
                }
                else{
                    $form->populate($formdata);
                    $this->view->data=$formdata;                    
                    // print_r($formdata);
                }
            }
            else{
                $form->populate($person);
                // print_r($person);exit();
            }
            $this->view->form=$form;
        }catch(exception $e){
            print "Error ".$e->getMessage();
        }
    }

    public function studentupdateAction(){
        $this->_helper->layout()->disableLayout();

        $dbperson=new Api_Model_DbTable_Person();

        $eid=$this->sesion->eid;
        $pid=$this->sesion->pid;
        $where=array("eid"=>$eid, "pid"=>$pid);

        if ($this->getRequest()->isPost()){
            $formdata = $this->getRequest()->getPost();
            $formdata["birthday"]=$formdata["year"]."-".$formdata["month"]."-".$formdata["day"];
            unset($formdata['year']);
            unset($formdata['month']);
            unset($formdata['day']);
            if ($dbperson->_update($formdata, $where)) {
                echo '1';
            }else{
                echo '0';
            }
        }
    }
//Editar datos de Carrera
    public function studentformsignAction(){
        $this->_helper->layout()->disableLayout();
        $studentSigninDb = new Api_Model_DbTable_Studentsignin();
        $formCareer = new Profile_Form_Career();
        $this->view->formCareer = $formCareer;

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        $pid   = $this->sesion->pid;
        $uid   = $this->sesion->uid;

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'subid' => $subid,
                        'escid' => $escid,
                        'pid'   => $pid,
                        'uid'   => $uid );
        $whySend = 'save';
        $studentSignin = $studentSigninDb->_getOne($where);
        if ($studentSignin) {
            $formCareer->populate($studentSignin);
            $whySend = 'update';
        }
        $this->view->whySend = $whySend;
    }

    public function studentsavesignAction(){
        $this->_helper->layout()->disableLayout();
        $formCareer = new Profile_Form_Career();
        $studentSigninDb = new Api_Model_DbTable_Studentsignin();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        $pid   = $this->sesion->pid;
        $uid   = $this->sesion->uid;

        $data = $this->getRequest()->getPost();

        if ($formCareer->isValid($data)) {
            if ($data['eligio_carrera'] == 'N' and $data['carrera_preferencia'] == '') {
                echo 'falta';
            }else{
                if ($data['whySend'] == 'save') {
                    unset($data['whySend']);
                    $data['eid']   = $eid;
                    $data['oid']   = $oid;
                    $data['escid'] = $escid;
                    $data['subid'] = $subid;
                    $data['pid']   = $pid;
                    $data['uid']   = $uid;

                    if ($studentSigninDb->_save($data)) {
                        echo 'true';
                    }else{
                        echo 'false';
                    }
                }else if ($data['whySend'] == 'update'){
                    unset($data['whySend']);
                    $pk = array('eid' => $eid,
                                'oid' => $oid,
                                'escid' => $escid,
                                'subid' => $subid,
                                'pid' => $pid,
                                'uid' => $uid );
                    //print_r($data);
                    if ($studentSigninDb->_update($data, $pk)) {
                        echo 'true';
                    }else{
                        echo 'false';
                    }
                }
            }
        }else{
            echo 'falta';
        }
    }

//DatosFamiliares--------------------------------------------
    public function studentfamilyAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;

            $dataStudent = array(   'eid' => $eid,
                                    'oid' => $oid,
                                    'pid' => $pid );
            $valor=$this->sesion->fullProfile->success = 'yes';
            $this->view->valor=$valor;
            $this->view->dataStudent = $dataStudent;

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

    public function studentnewfamilyAction(){
        try{
            $this->_helper->layout()->disableLayout();

            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;

            $form = new Profile_Form_Family();
            $this->view->form=$form;

            $dbfamily=new Api_Model_DbTable_Family();
            $dbrelation=new Api_Model_DbTable_Relationship();

            $where=array("eid"=>$eid, "pid"=>$pid);
            $attrib = array('assignee');
            $familiars = $dbrelation->_getFilter($where, $attrib);
            $assigneeYes = 0;
            foreach ($familiars as $assignee) {
                if ($assignee['assignee'] == 'S') {
                    $assigneeYes = 1;
                }
            }
            if ($assigneeYes == 1) {
                $form->assignee->removeMultiOption('S');
            }

            $attrib=array("type");
            $relation=$dbrelation->_getFilter($where, $attrib);
            $pa=0;
            $ma=0;
            foreach ($relation as $rel) {
                if($rel["type"]=="PA"){
                   $form->type->removeMultiOption("PA");
                }
                if($rel["type"]=="MA"){
                    $form->type->removeMultiOption("MA");
                }
            }

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studenteditfamilyAction(){
        try {
            $this->_helper->layout()->disableLayout();

            $famid = $this->getParam('famid');
            $this->view->famid = $famid;

            $eid = $this->sesion->eid;
            $pid = $this->sesion->pid;

            $dataFamiliar = array(  'eid'   => $eid,
                                    'pid'   => $pid,
                                    'famid' => $famid );
            $this->view->dataFamiliar = $dataFamiliar;

            $familyDb = new Api_Model_DbTable_Family();
            $relationDb = new Api_Model_DbTable_Relationship();
            $form=new Profile_Form_Family();

            $where=array("eid"=>$eid, "pid"=>$pid, "famid"=>$famid);
            $relationtype=$relationDb->_getOne($where);
            
            $interruptor = 0;
            $typeRel = $relationtype['type'];

            $where=array("eid"=>$eid, "pid"=>$pid);
            $attrib=array("type");
            $relation=$relationDb->_getFilter($where, $attrib);
            foreach ($relation as $rel) {
                if($rel["type"]=="PA" and $typeRel != 'PA'){
                    $form->type->removeMultiOption("PA");
                }
                if($rel["type"]=="MA" and $typeRel != 'MA'){
                    $form->type->removeMultiOption("MA");
                }
            }

            $where = array('eid'=>$eid, 'famid'=>$famid);
            $family = $familyDb->_getOne($where);
            $this->view->nameFamily = $family['firtsname'];


            $family['type'] = $relationtype['type'];
            $family['assignee'] = $relationtype['assignee'];

            $assigneeYes = 0;
            if($family['assignee'] == 'N'){
                $attrib = array('assignee');
                $where = array('eid'=>$eid, 'pid'=>$pid);
                $familiars = $relationDb->_getFilter($where, $attrib);
                foreach ($familiars as $assignee) {
                    if ($assignee['assignee'] == 'S') {
                        $assigneeYes = 1;
                    }
                }
            }
            if ($assigneeYes != 0) {
                $form->assignee->removeMultiOption('S');
            }

            $birthdayDivide = explode("-", $family['birthday']);
            $family['year'] = $birthdayDivide[0];
            $family['month'] = $birthdayDivide[1];
            $family['day'] = $birthdayDivide[2];
            
            //$form->type->setAttrib('disabled', 'disabled');
            $this->view->form=$form;
            $form->populate($family);

        } catch (Exception $e) {
            print 'Error '.$e->getMessage();
        }
    }

    public function studentsavefamilyAction(){
        $this->_helper->layout()->disableLayout();

        $familyDb   = new Api_Model_DbTable_Family();
        $relationDb = new Api_Model_DbTable_Relationship();

        $eid = $this->sesion->eid;
        $pid = $this->sesion->pid;

        $form = new Profile_Form_Family();
        $formData = $this->getRequest()->getPost();
        if ($form->isValid($formData)) {
            if ($formData['whySend'] == 'Save') {
                unset($formData['whySend']);
                $formData["birthday"] = $formData["year"]."-".$formData["month"]."-".$formData["day"];
                unset($formData["year"]);
                unset($formData["month"]);
                unset($formData["day"]);
                
                if ($formData['type'] == 'MA') {
                    $formData['sex'] = 'F';
                }elseif ($formData['type'] == 'PA') {
                    $formData['sex'] = 'M';
                }

                $formData["eid"] = $eid;
                trim($formData["numdoc"]);

                $relationData = array(  "eid"      => $eid, 
                                        "pid"      => $pid , 
                                        "type"     => $formData["type"],
                                        "assignee" => $formData["assignee"] );
                unset($formData["type"]);
                unset($formData["assignee"]);

                $save = $familyDb->_save($formData);
                if ($save) {
                    $relationData['famid'] = $save;
                    if ($relationDb->_save($relationData)) {
                        echo 'true';
                    }
                }
            }else {
                unset($formData['whySend']);
                $pkRelationship = array('eid'   => $formData['eid'],
                                        'pid'   => $formData['pid'],
                                        'famid' => $formData['famid'] );
                $pkFamiliar = array('eid'   => $formData['eid'],
                                    'famid' => $formData['famid'] );
                unset($formData['eid']);
                unset($formData['pid']);
                unset($formData['famid']);

                $formData["birthday"] = $formData["year"]."-".$formData["month"]."-".$formData["day"];
                unset($formData["year"]);
                unset($formData["month"]);
                unset($formData["day"]);

                $relationData = array(  "type"     => $formData["type"],
                                        "assignee" => $formData["assignee"] );
                unset($formData["type"]);
                unset($formData["assignee"]);
                if ($relationDb->_update($relationData, $pkRelationship)) {
                    if ($familyDb->_update($formData, $pkFamiliar)) {
                        echo "true";
                    }
                }
            }
        }else{
            echo "falta";
        }
    }


    public function studentremovefamilyAction(){
        try {
            $this->_helper->layout()->disableLayout();

            $eid = $this->sesion->eid;
            $pid = $this->sesion->pid;
            $famid = $this->getParam('famid');

            $familyDb = new Api_Model_DbTable_Family();
            $relationDb = new Api_Model_DbTable_Relationship();

            $where = array( 'eid' => $eid, 
                            'pid' => $pid, 
                            'famid' => $famid );
            if ($relationDb->_delete($where)) {
                $where = array( 'eid' => $eid, 
                                'famid' => $famid);
                if ($familyDb->_delete($where)) {
                    echo 'true';
                }else{
                    echo 'false';
                }
            }else{
                echo 'false';
            }

        } catch (Exception $e) {
            print 'Error'.$e->getMessage();
        }
    }
//---------------------------------------------------------------------





//Datos Academicos-----------------------------------------------------
    public function studentacademicAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;


            $data=array('eid' => $eid, 
                        'oid' => $oid, 
                        'pid' => $pid );

            $dbacadata=new Api_Model_DbTable_Academicrecord();
            $where=array(   "eid" => $eid,
                            "pid" => $pid);
            $acadata=$dbacadata->_getFilter($where);
            $bdubigeo=new Api_Model_DbTable_CountryDistrict();
            $i=0;
            foreach ($acadata as $acadata1) {
                $wheres=array('disid'=>$acadata1['location']);
                $dataubigeo=$bdubigeo->_infoUbigeo($wheres);
                if ($dataubigeo) {
                    $acadata[$i]['name_c']=strtoupper($dataubigeo[0]['name_c']);
                    $acadata[$i]['name_s']=strtoupper($dataubigeo[0]['name_s']);
                    $acadata[$i]['name_p']=strtoupper($dataubigeo[0]['name_p']);
                    $acadata[$i]['name_d']=strtoupper($dataubigeo[0]['name_d']);
                    $acadata[$i]['clave']="1";
                }
                else{
                    $acadata[$i]['clave']="0";
                }
                $i++;
            }

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
            $this->view->veri=0;

            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if($form->isValid($formdata)){
                    unset($formdata["save"]);
                    $formdata['location']=$formdata['country_d'];
                    unset($formdata['country']);
                    unset($formdata['country_s']);
                    unset($formdata['country_p']);
                    unset($formdata['country_d']);
                    $formdata["eid"]=$eid;
                    $formdata["pid"]=$pid;
                    // trim($formdata["location"]);
                    trim($formdata["type"]);
                    trim($formdata["institution"]);
                    trim($formdata["year_end"]);
                    trim($formdata["title"]);
                    
                    if ($academic=$dbacademic->_save($formdata)) {
                        $this->view->clave=1;
                    }
                    // print_r("Se Guardó conÉxito");
                }
            }
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studenteditacademicAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $pid = $this->sesion->pid;
            $acid = $this->getParam('acid');
            $this->view->acid = $acid;

            $academicDb = new Api_Model_DbTable_Academicrecord();
            $form=new Profile_Form_Academic();

            $where = array('acid'=>$acid, 'eid'=>$eid, 'pid'=>$pid);
            $academic = $academicDb->_getOne($where);
            if ($academic['location']) {
                $wheres=array('disid'=>$academic['location']);
                $bdubigeo=new Api_Model_DbTable_CountryDistrict();
                $dataubigeo=$bdubigeo->_infoUbigeo($wheres);
                if ($dataubigeo) {
                    $dataubigeo=$dataubigeo[0];                
                    $academic['country']=$dataubigeo['coid'];
                    $academic['country_s']=$dataubigeo['cosid'];
                    $academic['country_p']=$dataubigeo['proid'];
                    $academic['country_d']=$dataubigeo['disid'];
                    $this->view->dataubigeo=$dataubigeo;
                    $this->view->veri=1;                   
                }
                else{
                    $this->view->veri=0;
                }
            }
            else{
                $this->view->veri=0;
            }

            if ($this->getRequest()->isPost()){
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata)){ 
                    $formdata['location']=$formdata['country_d'];
                    unset($formdata['country']);
                    unset($formdata['country_s']);
                    unset($formdata['country_p']);
                    unset($formdata['country_d']);
                    if($academicDb->_update($formdata, $where)){
                        $this->view->clave=1;
                    }
                    ;
                }
                else{
                    $form->populate($formdata);
                }
            }
            else{
                $form->populate($academic);                
            }
            $this->view->form=$form;
        } catch (Exception $e) {
            print 'Error'.$e->getMessage();
        }
    }

    public function studentremoveacademicAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $pid = $this->sesion->pid;
            $acid=$this->_getParam("acid");

            $academicDb=new Api_Model_DbTable_Academicrecord();
            $where=array(   "eid"  => $eid, 
                            "pid"  => $pid, 
                            "acid" => $acid);

            if ($academicDb->_delete($where)) {
                echo 'true';
            }else{
                echo 'false';
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

            $dataStudent = array(   'eid' => $eid,
                                    'oid' => $oid,
                                    'pid' => $pid );

            $this->view->dataStudent = $dataStudent;

            $dbsta=new Api_Model_DbTable_Statistics();
            $where=array("eid"=>$eid, "oid"=>$oid, "uid"=>$uid, "pid"=>$pid, "escid"=>$escid, "subid"=>$subid);
            $sta=$dbsta->_getOne($where);
            $this->view->statistic=$sta;
            //print_r($sta);

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentnewstatisticAction()
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

    public function studenteditstatisticAction(){
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

            $form=new Profile_Form_Statistic();
            $this->view->form=$form;
            $form->populate($sta);

            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if ($form->isValid($formdata)) {
                    $pk["eid"]=$eid;
                    $pk["oid"]=$oid;
                    $pk["uid"]=$uid;
                    $pk["pid"]=$pid;
                    $pk["escid"]=$escid;
                    $pk["subid"]=$subid;
                    $formdata["state"]="T";
                    if($formdata["dependen_ud"]=="N"){
                        $formdata["num_dep_ud"]=0;
                    }
                    $update=$dbsta->_update($formdata, $pk);
                    print_r("Se Actualizaron los Datos con Exito");
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

    public function studentnewlaboralAction()
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

    public function studenteditlaboralAction(){
        try {
            $this->_helper->layout->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;
            $lid = $this->getParam('lid');

            $this->view->lid = $lid;

            $jobDb=new Api_Model_DbTable_Jobs();
            $form=new Profile_Form_Laboral();

            $where = array('eid'=>$eid, 'pid'=>$pid, 'lid'=>$lid);
            $job = $jobDb->_getOne($where);

            $this->view->form = $form;
            $form->populate($job);

            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                print_r($formdata);
                if($form->isValid($formdata)){
                    $jobupdate = $jobDb->_update($formdata, $where);
                }
            }
        } catch (Exception $e) {
            print 'Error '.$e->getMessage();
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
            $oid=$this->sesion->oid;
            $pid=$this->sesion->pid;

            $data=array("eid"=>$eid, 'oid' => $oid, "pid"=>$pid);

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

    public function studentnewinterestAction()
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

    public function studenteditinterestAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $pid=$this->sesion->pid;
            $iid = $this->getParam('iid');

            $this->view->iid = $iid;

            $interestDb=new Api_Model_DbTable_Interes();
            $form=new Profile_Form_Interest();

            $where = array('eid'=>$eid, 'pid'=>$pid, 'iid'=>$iid);
            $interes = $interestDb->_getOne($where);

            $this->view->form = $form;
            $form->populate($interes);

            if ($this->getRequest()->isPost()) {
                $formdata=$this->getRequest()->getPost();
                if($form->isValid($formdata)){
                    $interest=$interestDb->_update($formdata, $where);
                }
            }

        } catch (Exception $e) {
            print 'Error '.$e->getMessage();
        }
    }

    public function studentremoveinterestAction()
    {
        try{
            $this->_helper->layout()->disableLayout();

            $eid = $this->sesion->eid;
            $pid = $this->sesion->pid;
            $iid=$this->_getParam("iid");

            $dbinterest=new Api_Model_DbTable_Interes();
            $where=array(   "eid" => $eid,
                            "pid" => $pid, 
                            "iid" => $iid );

            if($dbinterest->_delete($where)){
                echo 1;
            }else{
                echo 0;
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
            $this->view->data=$data;

            $dbcuract=new Api_Model_DbTable_Registrationxcourse();
            $dbtyperate=new Api_Model_DbTable_PeriodsCourses();

            $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid, "perid"=>$perid, 'state'=>'M');
            $attrib=array("courseid", "turno","curid","promedio1","promedio2","nota4_i","nota9_i","nota4_ii","nota9_ii","notafinal", 'state');
            //print_r($this->sesion);
            $curact=$dbcuract->_getFilter($where, $attrib);
            //print_r($curact);
            $nc=0;
            $coursesD = 0;
            $coursesDis = null;
            $typerate=null;
            $name=null;
            $notasAplazados=null;
            if ($curact){			
				foreach ($curact as $cur) {
					$where=array("eid"=>$eid, "oid"=>$oid, "perid"=>$perid, "courseid"=>$cur['courseid'], "turno"=>$cur['turno'], "curid"=>$cur['curid']);
					$attrib=array("type_rate");
					//print_r($where);
					$typerate[$nc]=$dbtyperate->_getFilter($where,$attrib);
					$where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cur['courseid']);
					$attrib=array("name");
					$name[$nc]=$dbcuract->_getInfoCourse($where,$attrib);
					$nc++;

					if ($cur['notafinal'] and $cur['notafinal']<11) {
						$coursesDis[$coursesD]['courseid'] = $cur['courseid'];
						$coursesDis[$coursesD]['curid'] =$cur['curid'];
						$coursesD++;
					}
				}
			}
            if ($coursesD < 3) {
                $number = $rest = substr($perid, 0, 2);
                $letter = substr($perid, -1);
                if ($letter == 'A') {
                    $letter = 'D';
                    $perid = $number.$letter;
                }else{
                    $letter = 'E';
                    $perid = $number.$letter;
                }
                $c = 0;
                $attrib = array('notafinal', 'courseid');
                if ($coursesDis){
					foreach ($coursesDis as $courseDis) {
					   $where = array('eid'=>$eid,
									'oid'=>$oid,
									'escid'=>$escid,
									'subid'=>$subid,
									'courseid'=>$courseDis['courseid'],
									'curid'=>$courseDis['curid'],
									'perid'=>$perid);
					   $notasAplazados[$c]=$dbcuract->_getFilter($where, $attrib);
					   $c++;
					}
				}
            }

            $this->view->nc = $nc;
            $this->view->typerate=$typerate;
            $this->view->name=$name;
            $this->view->curact=$curact;
            $this->view->notasAplazados = $notasAplazados;

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
            $perid=$this->sesion->period->perid;

            $dbcur=new Api_Model_DbTable_Studentxcurricula();
            $dbcourxcur=new Api_Model_DbTable_Course();
            $dbcourlle=new Api_Model_DbTable_Registrationxcourse();


            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "uid"=>$uid, "pid"=>$pid);
            //print_r($where);
            $cur=$dbcur->_getOne($where);
            //print_r($cur);
            $courpercur=$dbcourxcur->_getCoursesXCurriculaXShool($eid,$oid,$cur['curid'],$escid);
            $c=0;
            if ($courpercur){
				foreach ($courpercur as $cour) {
					$where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cour['courseid'], "curid"=>$cur['curid'],"pid"=>$pid,"uid"=>$uid);
					$attrib=array("courseid","notafinal","perid","turno");
					$courlle[$c]=$dbcourlle->_getFilter($where, $attrib);
					$c++;
				}
			}
            //print_r($courlle);
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid,"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
            $attrib=array("courseid","state");
            $courlleact=$dbcourlle->_getFilter($where,$attrib);
            //print_r($where);

            $this->view->courpercur=$courpercur;
            $this->view->courlleact=$courlleact;
            $this->view->courlle=$courlle;
            //print_r($courlle);
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function printpercurAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $this->view->uid=$uid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
         
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;            
            $perid=$this->sesion->period->perid; 
            
            $faculty=$this->sesion->faculty->name;
            $this->view->faculty=$faculty;

            $speciality=$this->sesion->speciality->name;
            $this->view->speciality=$speciality;

            $dbcur=new Api_Model_DbTable_Studentxcurricula();
            $dbcourxcur=new Api_Model_DbTable_Course();
            $dbcourlle=new Api_Model_DbTable_Registrationxcourse();


            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "uid"=>$uid, "pid"=>$pid);
            $cur=$dbcur->_getOne($where);
            $courpercur=$dbcourxcur->_getCoursesXCurriculaXShool($eid,$oid,$cur['curid'],$escid);
            $c=0;
            foreach ($courpercur as $cour) {
                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cour['courseid'], "curid"=>$cur['curid'],"pid"=>$pid,"uid"=>$uid);
                $attrib=array("courseid","notafinal","perid","turno");
                $courlle[$c]=$dbcourlle->_getFilter($where, $attrib);
                // print_r($courlle);
                $c++;
            }
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid,"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
            $attrib=array("courseid","state");
            $courlleact=$dbcourlle->_getFilter($where,$attrib);

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $spe=array();
            $dbspeciality = new Api_Model_DbTable_Speciality();
            $speciality = $dbspeciality ->_getOne($where);
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $dbspeciality->_getOne($wher);
            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality['name'];
            }
            else{
                $spe['esc']=$speciality['name'];
                $spe['parent']='';  
            }

            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
            
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names."<br>".$namep;
            $whered['eid']=$eid;
            $whered['oid']=$oid;
            $whered['facid']= $speciality['facid'];
            $dbfaculty = new Api_Model_DbTable_Faculty();
            $faculty = $dbfaculty ->_getOne($whered);
            $namef = strtoupper($faculty['name']);

            $wheres=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
            $dbperson = new Api_Model_DbTable_Users();
            $person= $dbperson -> _getUserXUid($wheres);

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uidim=$this->sesion->pid;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'matriculasxcurricula',
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            $dbimpression->_save($data);            
            
            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'matriculasxcurricula');
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);

            $this->view->header=$header;
            $this->view->footer=$footer;
            $this->view->person=$person;
            $this->view->courpercur=$courpercur;
            $this->view->courlleact=$courlleact;
            $this->view->courlle=$courlle;
            
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function printfichaAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=base64_decode($this->_getParam('eid'));
            $oid=base64_decode($this->_getParam('oid'));
            $pid=base64_decode($this->_getParam('pid'));
            $uid=base64_decode($this->_getParam('uid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $this->view->pid=$pid;
            $wherep=array('eid'=>$eid,'pid'=>$pid);

            $dbrelation=new Api_Model_DbTable_Relationship();
            $datafami=$dbrelation->_getFilter($wherep);
            $len=count($datafami);

            $dbfamily=new Api_Model_DbTable_Family();
            $wherefa['eid']=$eid;
            for ($i=0; $i < $len; $i++) { 
                $wherefa['famid']=$datafami[$i]['famid'];
                $data=$dbfamily->_getOne($wherefa);
                $pidfa=$data['numdoc'];
                $namefa=$data['lastname'].", ".$data['firtsname'];
                $datafami[$i]['name']=$namefa;
                $datafami[$i]['numdoc']=$pidfa;
            }
            $this->view->datafami=$datafami;

            $whered=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid,'uid'=>$uid,'escid'=>$escid,'subid'=>$subid);
            $dbdetingreso=new Api_Model_DbTable_Studentsignin();
            $dataingre=$dbdetingreso->_getOne($whered);
            $this->view->dataingre=$dataingre;

            $dbstatistics=new Api_Model_DbTable_Statistics();
            $dataest=$dbstatistics->_getOne($whered);
            $this->view->dataest=$dataest;

            $wherea=array('eid'=>$eid,'pid'=>$pid);
            $dbacademic=new Api_Model_DbTable_Academicrecord();
            $datacade=$dbacademic->_getFilter($wherea);
            $datacade=$datacade[0];
            $this->view->datacade=$datacade;

            $wherei=array('eid'=>$eid,'pid'=>$pid);
            $dbinterest=new Api_Model_DbTable_Interes();
            $datainte=$dbinterest->_getFilter($wherei);
            $this->view->datainte=$datainte;

            $dbperson=new Api_Model_DbTable_Person();
            $datapers=$dbperson->_getOne($wherep);
            $this->view->datapers=$datapers;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $spe=array();
            $dbspeciality = new Api_Model_DbTable_Speciality();
            $speciality = $dbspeciality ->_getOne($where);
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $dbspeciality->_getOne($wher);
            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality['name'];
            }
            else{
                $spe['esc']=$speciality['name'];
                $spe['parent']='';  
            }

            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
            
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names."<br>".$namep;

            $whered=array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']);
            $dbfaculty = new Api_Model_DbTable_Faculty();
            $faculty = $dbfaculty ->_getOne($whered);
            $namef = strtoupper($faculty['name']);

            $dbimpression = new Api_Model_DbTable_Countimpressionall();    
            $uidim=$this->sesion->pid;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'impresion_ficha_estadistica',
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            // print_r($data);exit();
            $dbimpression->_save($data);            
            
            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'impresion_ficha_estadistica');
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);

            $this->view->header=$header;
            $this->view->footer=$footer;
        } catch (Exception $e) {
            print "Error".$e -> getMessage();    
        }
    }

    // public function studentsignrealizedAction()
    // {
    //     try{
    //         $this->_helper->layout()->disableLayout();
    //         $eid=$this->sesion->eid;
    //         $oid=$this->sesion->oid;
    //         $pid=$this->sesion->pid;
    //         $uid=$this->sesion->uid;
    //         $escid=$this->sesion->escid;
    //         $subid=$this->sesion->subid;

    //         $dbsignr=new Api_Model_DbTable_Registrationxcourse();
    //         $dbnamper=new Api_Model_DbTable_Periods();

    //         $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid);
    //         $attrib=array('perid', 'courseid', 'notafinal');
    //         $order=array("perid", "courseid");
    //         $signr=$dbsignr->_getFilter($where, $attrib, $order);
    //         //print_r($signr);
    //         $per="0";
    //         $c=0;
    //         $attribcour=array('courseid');
    //         foreach ($signr as $sperid) {
    //             if($sperid['perid']<>$per){
    //                 $attrib=array('name');
    //                 $where=array("eid"=>$eid, "oid"=>$oid, "perid"=>$sperid['perid']);
    //                 $namper[$c]=$dbnamper->_getFilter($where,$attrib);


    //                 $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid, "escid"=>$escid, "perid"=>$sperid['perid']);
    //                 //print_r($where);
    //                 $courxper=$dbsignr->_getFilter($where,$attribcour);
    //                 $x=0;
    //                 $attrib = array('name', 'credits');
    //                 foreach ($courxper as $cour) {
    //                     $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cour['courseid']);
    //                     //print_r($where);
    //                     $courname[$c][$x]=$dbsignr->_getInfoCourse($where, $attrib);
    //                     $x++;
    //                 }

    //                 $per=$sperid['perid'];
    //                 $c++;
    //             }
    //         }
    //         //print_r($courname);

    //         $this->view->coursesperPeriod = $signr;
    //         $this->view->courname=$courname;
    //         $this->view->namper=$namper;

    //     }catch(exception $e){
    //         print "Error : ".$e->getMessage();
    //     }
    // }

}
