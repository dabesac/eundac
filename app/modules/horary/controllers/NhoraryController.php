<?php

class Horary_NhoraryController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		//$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (($login->infouser['teacher']['is_director']<>"S")){
    		$this->_helper->redirector('index','error','default');
    	}
    	$this->sesion = $login;
    
    }
    public function indexAction(){    
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $perid=$this->sesion->period->perid;
        $escid=$this->sesion->escid;
        $subid=$this->sesion->subid;    
        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);

        if ( $datahours) {
            // $this->_helper->redirector('listteacher');   
            $this->_helper->redirector('index','distribution','distribution');
        }
        else{
            $this->_helper->redirector('index','distribution','distribution');         
        }


    }
    public function listteacherAction()
    {
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid=$this->sesion->period->perid;
            $escid=$this->sesion->escid;
            $d=new Api_Model_DbTable_PeriodsCourses();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['perid']=$perid;
            $where['escid']=$escid;

            $cur=$d->_getTeacherXPeridXEscid($eid,$oid,$escid,$perid);
            //$cur=$d->_getTeacherXPeridXEscid1($where);
            //print_r($cur);
            $this->view->docente=$cur;
            

            $this->view->cursos=$datcur;
        } catch (Exception $ex) {
            print $ex->getMessage();
        }
    }

    public function fillhoraryAction()
    {  
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));
        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['perid']=$perid;
        $where['escid']=$escid;
        $where['subid']=$subid;
        $where['pid']=$pid;
        // print_r($where);
        $curso=new Api_Model_DbTable_Coursexteacher();
        $datcur=$curso->_getFilter($where);
        $this->view->cursos=$datcur;
        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;

        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        $this->view->datahours=$datahours;

        $hora=new Api_Model_DbTable_Horary();
        $wher['eid']=$eid;
        $wher['oid']=$oid;
        $wher['perid']=$perid;
        $wher['subid']=$subid;
        $wher['teach_pid']=$pid;
        $wher['teach_uid']=$uid;

        $dathora=$hora->_getFilter($wher);
        if ($dathora) $this->view->dathora=1;
    }
    public function validatetimeAction()
    {    
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $escid=$this->_getParam('escid');
        $subid=$this->_getParam('subid');
        $perid=$this->_getParam('perid');
        $courseid=$this->_getParam('courseid');
        $curid=$this->_getParam('curid');
        $turno=$this->_getParam('turno');
        $semid=$this->_getParam('semid');
        $uid=$this->_getParam('uid');
        $pid=$this->_getParam('pid');
        $hora_ini=$this->_getParam('hora_ini');  
        $hora_acad=$this->_getParam('hora_acad');
        $dia=$this->_getParam('dia');
        $tipo_clase=$this->_getParam('tipoclase');

        //Obtenemos un array con todas las horas academicas establecidas.
        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours = new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);

        if ($datahours) {
            $valhoras[0]=$datahours[0]['hours_begin'];
            $hora=new Api_Model_DbTable_Horary();
            for ($k=0; $k < 20; $k++) { 
                $dho=$hora->_getsumminutes($valhoras[$k],'50');
                $valhoras[$k+1]=$dho[0]['hora'];
            }
        }    
        // valida que la hora de inicio sea multiplo de 50 min.
        for ($zz=0; $zz < 20; $zz++) { 
            if ($hora_ini==$valhoras[$zz]) {
                $valini=1;
            }
        }

        if ($valini=="1") {
            //Sacamos de hora de finalizacion deacuerdo a la cantidad de horas academicas.
            $hora_fin[0]['hora']=$hora_ini;
            for ($x=0; $x < $hora_acad; $x++) { 
                $hora_fin=$hora->_getsumminutes($hora_fin[0]['hora'],'50');
            }
            $hora_fin=$hora_fin[0]['hora'];
            //Recupera el nombre del día del inicio de clases del periodo.
            $per=new Api_Model_DbTable_Periods();
            $wp['eid']=$eid;
            $wp['oid']=$oid;
            $wp['perid']=$perid;
            $dper=$per->_getOnePeriod($wp);
            $time = strtotime($dper['class_start_date']);
            $day= strftime("%A", $time);
            $week[0]="Monday";
            $week[1]="Tuesday";
            $week[2]="Wednesday";
            $week[3]="Thursday";
            $week[4]="Friday";
            $week[5]="Saturday";
            $week[6]="Sunday";

            //Verificamos numero de dias que debe empezar el horario segun el dia de inicio de clases del periodo.
            for ($i=0; $i < 7; $i++) { 
                if ($week[$i]==$day) $sum=7-$i;
            }
            
            $diaempezar=$sum+$dia;

            if ($diaempezar>6) {
                if ($diaempezar==7) $diaempezar=0;
                else $diaempezar=$diaempezar-7;
            }

            //Retorna la fecha de inicio de la primera semana de clases deacuerdo al dia del horario.
             $fecha=$hora->_getsumdate($dper['class_start_date'], $diaempezar);

            $data=array();
            $data['eid']=$eid;
            $data['oid']=$oid;
            // $data['hid']=time().rand(0,9);
            $data['perid']=$perid;
            $data['escid']=$escid;
            $data['subid']=$subid;
            $data['curid']=$curid;
            $data['courseid']=$courseid;
            $data['semid']=$semid;
            $data['turno']=$turno;
            $data['hora_ini']=$hora_ini;
            $data['hora_fin']=$hora_fin;
            $data['teach_pid']=$pid;
            $data['teach_uid']=$uid;
            $data['type_class']=$tipo_clase;
            $data['day']=$dia+1;
            $horaexis=$hora->_getHorary($eid,$oid,$perid,$escid,$curid,$courseid,$turno,$subid,$uid,$fecha[0]['dia'],$data['hora_ini'],$data['hora_fin']);
            $horasem=$hora->_getHoraryXsemXturno($eid,$oid,$perid,$escid,$subid,$semid,$turno,$hora_ini,$hora_fin,$data['day']);
            // exit();
            if ($horaexis or $horasem){ ?>
                <script type="text/javascript">
                alert("Ya existe un horario en el mismo semestre ó del mismo curso, no debe duplicar el horario.");
                </script>
            <?php       
            }else {
                
                    $hora->_save($data);
                   ?>
                    <script type="text/javascript">
                    window.location.reload();
                    </script>
                
                <?php
            }

        }else{ ?>
            <script type="text/javascript">
                alert("La Hora de Inicio no coincide con las horas académicas establecidas. Por favor ingrese nuevamente.");
            </script>
        <?php
        }
    }

    public function horaryteacherAction()
    {    
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        // $perid=$this->sesion->period->perid;
        // $escid=$this->sesion->escid;
        // $subid=$this->sesion->subid;
        $escid=$this->_getParam('escid');
        $subid=$this->_getParam('subid');
        $perid=$this->_getParam('perid');
        $pid=$this->_getParam('pid');
        $uid=$this->_getParam('uid');
        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours = new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        
        if ($datahours) {
            $valhoras[0]=$datahours[0]['hours_begin'];
            $hora=new Api_Model_DbTable_Horary();
            for ($k=0; $k < 20; $k++) { 
                $dho=$hora->_getsumminutes($valhoras[$k],'50');
                $valhoras[$k+1]=$dho[0]['hora'];
            }
            $this->view->valhoras=$valhoras;
            
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['perid']=$perid;
            $where['subid']=$subid;
            $where['teach_uid']=$uid;
            $where['teach_pid']=$pid;
            $dathora=$hora->_getFilter($where);

            $this->view->horarios=$dathora;
            $curso=new Api_Model_DbTable_Coursexteacher();
            $where1['eid']=$eid;
            $where1['oid']=$oid;
            $where1['escid']=$escid;
            $where1['perid']=$perid;
            $where1['subid']=$subid;
            $where1['pid']=$pid;
            $datcur=$curso->_getFilter($where1);

            $ncursos=count($datcur);
            for ($cont=0; $cont < $ncursos; $cont++) { 
                $datcur[$cont]['num']=$cont;
            }
            $this->view->datcursos=$datcur;
            $this->view->ncursos=$ncursos;
        }
    }

    public function deletehoraryAction()
    {    
     //$this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $subid=$this->_getParam('subid');
        $perid=$this->_getParam('perid');
        $courseid=$this->_getParam('courseid');
        $escid=$this->_getParam('escid');
        $teach_uid=$this->_getParam('uid');
        $teach_pid=$this->_getParam('pid');
        $curid=$this->_getParam('curid');
        $turno=$this->_getParam('turno');
        $horaini=$this->_getParam('horaini');
        $horafin=$this->_getParam('horafin');
        $dia=$this->_getParam('diasemana');

            $data['eid']=$eid;
            $data['oid']=$oid;
            $data['perid']=$perid;
            $data['escid']=$escid;
            $data['subid']=$subid;
            $data['curid']=$curid;
            $data['courseid']=$courseid;
            $data['turno']=$turno;
            $data['hora_ini']=$horaini;
            $data['hora_fin']=$horafin;
            $data['teach_pid']=$teach_pid;
            $data['teach_uid']=$teach_uid;
            $data['day']=$dia;
            //print_r($data);
            $hora=new Api_Model_DbTable_Horary();
            $hora->_delete($data);
            
            $teach_uid=base64_encode($teach_uid);
            $teach_pid=base64_encode($teach_pid);
            $escid=base64_encode($escid);
            $subid=base64_encode($subid);
            $perid=base64_encode($perid);
            $this->_redirect("/horary/nhorary/fillhorary/uid/$teach_uid/pid/$teach_pid/escid/$escid/subid/$subid/perid/$perid");        

    }
    
    public function changehoursAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $fm = new Horary_Form_Hours();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;       
    
            $redirect=0;

            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();

                if ($fm->isValid($frmdata)){

                    $hour=$frmdata['hour'];
                        if ($hour<=9) {
                            $frmdata['hour']="0".$hour;
                        }
                    $minute=$frmdata['minute'];
                        if ($minute==0) {
                          $frmdata['minute']="0".$minute;
                        }
                    $frmdata['hours_begin']=$frmdata['hour'].":".$frmdata['minute'].":00";    

                    if ($frmdata['hour_t']) {
                        $hour_t=$frmdata['hour_t'];
                            if ($hour_t<=9) {
                                $frmdata['hour_t']="0".$hour_t;
                            }
                        $minute_t=$frmdata['minute_t'];
                            if ($minute_t==0) {
                              $frmdata['minute_t']="0".$minute_t;
                            }
                        $frmdata['hours_begin_afternoon']=$frmdata['hour_t'].":".$frmdata['minute_t'].":00";

                    }
                    unset($frmdata['save']);
                    unset($frmdata['hour']);
                    unset($frmdata['minute']);
                    unset($frmdata['hour_t']);
                    unset($frmdata['minute_t']);
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;
                    $reg_= new Api_Model_DbTable_HoursBeginClasses();
                    if ($reg_->_save($frmdata)) {
                        $redirect=1;
                    }
                }
                else{
                    $this->view->escid=$frmdata['escid'];
                    $this->view->perid=$frmdata['perid'];
                    $this->view->subid=$frmdata['subid'];
                    $this->view->distid=$frmdata['distid'];
                }   
            }
            else{
                $escid=base64_decode($this->_getParam('escid'));
                $subid=base64_decode($this->_getParam('subid'));
                $perid=base64_decode($this->_getParam('perid'));
                $distid=base64_decode($this->_getParam('distid'));
                $this->view->escid=$escid;        
                $this->view->subid=$subid;        
                $this->view->perid=$perid;        
                $this->view->distid=$distid; 
            }
            $this->view->redirect=$redirect;
            $this->view->fm=$fm;

        } catch (Exception $e) {
            print "Error: Change Hours".$e->getMessage(); 
        }
    }

    public function updatehoursAction(){
        try {
            $this->_helper->layout()->disableLayout();

            $redirec=0;

            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $perid=base64_decode($this->_getParam('perid'));

            $param=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $attrib=array('hoursid','hours_begin','hours_update','hours_begin_afternoon','hours_update_afternoon');
            $bdbegin= new Api_Model_DbTable_HoursBeginClasses();
            $datah=$bdbegin->_getFilter($param,$attrib);

            if ($datah[0]['hours_update'] or $datah[0]['hours_update_afternoon']) {
                $datahm=$datah[0]['hours_update'];
                $dataht=$datah[0]['hours_update_afternoon'];
                $datahm=split(":",$datahm);
                $hour=$datahm[0];
                $minute=$datahm[1];
                $dataht=split(":",$dataht);
                $hour_t=$dataht[0];
                $minute_t=$dataht[1];
                $arrays=array('hour'=>$hour,'minute'=>$minute,'hour_t'=>$hour_t,'minute_t'=>$minute_t);
                $fm = new Horary_Form_Hours();
                $fm->populate($arrays);
            }
            else{
                $datahm=$datah[0]['hours_begin'];
                $dataht=$datah[0]['hours_begin_afternoon'];
                $datahm=split(":",$datahm);
                $hour=$datahm[0];
                $minute=$datahm[1];
                $dataht=split(":",$dataht);
                $hour_t=$dataht[0];
                $minute_t=$dataht[1];
                $arrays=array('hour'=>$hour,'minute'=>$minute,'hour_t'=>$hour_t,'minute_t'=>$minute_t);
                $fm = new Horary_Form_Hours();
                $fm->populate($arrays);
            }
            $this->view->fm=$fm;

            $pk['hoursid']=$datah[0]['hoursid'];
        
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();

                if ($fm->isValid($frmdata)){

                    $hour=$frmdata['hour'];
                        if ($hour<=9) {
                            $frmdata['hour']="0".$hour;
                        }
                    $minute=$frmdata['minute'];
                        if ($minute==0) {
                          $frmdata['minute']="0".$minute;
                        }
                    $frmdata['hours_update']=$frmdata['hour'].":".$frmdata['minute'].":00";    

                    if ($frmdata['hour_t']) {
                        $hour_t=$frmdata['hour_t'];
                            if ($hour_t<=9) {
                                $frmdata['hour_t']="0".$hour_t;
                            }
                        $minute_t=$frmdata['minute_t'];
                            if ($minute_t==0) {
                              $frmdata['minute_t']="0".$minute_t;
                            }
                        $frmdata['hours_update_afternoon']=$frmdata['hour_t'].":".$frmdata['minute_t'].":00";

                    }
                    $pk['hoursid']=$frmdata['pk'];
                    unset($frmdata['update']);
                    unset($frmdata['hour']);
                    unset($frmdata['minute']);
                    unset($frmdata['hour_t']);
                    unset($frmdata['minute_t']);
                    unset($frmdata['pk']);
                    $reg_= new Api_Model_DbTable_HoursBeginClasses();
                    if ($reg_->_update($frmdata,$pk)) {
                        $redirec=1;
                    }
                }  
            }
            else{
                $this->view->pk=$pk;   
            }
            $this->view->redirec=$redirec;

        } catch (Exception $e) {
            print "Error: Update Hours".$e->getMessage();
        }

    } 
}
