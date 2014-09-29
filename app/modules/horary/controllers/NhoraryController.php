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

        if ($datahours) {
            // $this->_helper->redirector('listteacher');
            $this->_helper->redirector('index','distribution','distribution');
        }
        else{
            $this->_helper->redirector('index','distribution','distribution');
        }


    }
    public function listteacherAction(){
        try {
            $this->_helper->layout()->disableLayout();
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
            print "Error: ".$ex->getMessage();
        }
    }

    public function fillhoraryAction(){
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));

        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['pid']=$pid;
        $where['escid']=$escid;
        $where['perid']=$perid;
        $where['subid']=$subid;

        $n=new Api_Model_DbTable_Course();
        $curso=new Api_Model_DbTable_Coursexteacher();
        $datcur=$curso->_getFilter($where);

        if ($datcur) {
            foreach ($datcur as $key => $data) {
                $where = array('eid'=>$data['eid'],'oid'=>$data['oid'],'escid'=>$data['escid'],
                               'subid'=>$data['subid'],'courseid'=>$data['courseid'],'curid'=>$data['curid']);
                $ncur=$n->_getOne($where);
                $datcur[$key]['name']=$ncur['name'];
            }
            $this->view->cursos=$datcur;
        }

        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;

        $hora=new Api_Model_DbTable_Horary();
        $wher['eid']=$eid;
        $wher['oid']=$oid;
        $wher['perid']=$perid;
        $wher['escid']=$escid;
        $wher['subid']=$subid;
        $wher['teach_pid']=$pid;
        $wher['teach_uid']=$uid;
        $dathora=$hora->_getFilter($wher);

        if ($dathora) $this->view->dathora=1;
    }

    public function fillhorarypracAction(){
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));

        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['pid']=$pid;
        $where['escid']=$escid;
        $where['perid']=$perid;
        $where['subid']=$subid;

        $n=new Api_Model_DbTable_Course();
        $curso=new Api_Model_DbTable_Coursexteacher();
        $datcur=$curso->_getFilter($where);

        if ($datcur) {
            foreach ($datcur as $key => $data) {
                $where = array('eid'=>$data['eid'],'oid'=>$data['oid'],'escid'=>$data['escid'],
                               'subid'=>$data['subid'],'courseid'=>$data['courseid'],'curid'=>$data['curid']);
                $ncur=$n->_getOne($where);
                $datcur[$key]['name']=$ncur['name'];
            }
            $this->view->cursos=$datcur;
        }

        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;

        $hora=new Api_Model_DbTable_Horary();
        $wher['eid']=$eid;
        $wher['oid']=$oid;
        $wher['perid']=$perid;
        $wher['escid']=$escid;
        $wher['subid']=$subid;
        $wher['teach_pid']=$pid;
        $wher['teach_uid']=$uid;
        $dathora=$hora->_getFilter($wher);

        if ($dathora) $this->view->dathora=1;
    }

    public function fillhorarysuportAction(){
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));

        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['pid']=$pid;
        $where['escid']=$escid;
        $where['perid']=$perid;
        $where['subid']=$subid;

        $n=new Api_Model_DbTable_Course();
        $curso=new Api_Model_DbTable_Coursexteacher();
        $datcur=$curso->_getFilter($where);

        if ($datcur) {
            foreach ($datcur as $key => $data) {
                $where = array('eid'=>$data['eid'],'oid'=>$data['oid'],'escid'=>$data['escid'],
                               'subid'=>$data['subid'],'courseid'=>$data['courseid'],'curid'=>$data['curid']);
                $ncur=$n->_getOne($where);
                $datcur[$key]['name']=$ncur['name'];
            }
            $this->view->cursos=$datcur;
        }

        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;

        $hora=new Api_Model_DbTable_Horary();
        $wher['eid']=$eid;
        $wher['oid']=$oid;
        $wher['perid']=$perid;
        $wher['escid']=$escid;
        $wher['subid']=$subid;
        $wher['teach_pid']=$pid;
        $wher['teach_uid']=$uid;
        $dathora=$hora->_getFilter($wher);

        if ($dathora) $this->view->dathora=1;
    }

    public function asignationhoursAction(){
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));

        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        $this->view->datahours=$datahours;

        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;
    }

    public function asignationhourspracAction(){
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));

        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        $this->view->datahours=$datahours;

        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;
    }

    public function asignationhoursuportAction(){
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));

        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        $this->view->datahours=$datahours;

        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;
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
        $valhoras=null;
        $valinim=null;
        $valinit=null;
        $valini=null;
        //Obtenemos un array con todas las horas academicas, desde la hora de inicio.
        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours = new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);

        if ($datahours) {
            $hora=new Api_Model_DbTable_Horary();

            if ($datahours[0]['hours_begin_afternoon']) {
                $valhorasm[0]=$datahours[0]['hours_begin'];
                $valhorast[0]=$datahours[0]['hours_begin_afternoon'];
                $k=0;
                while ($valhorasm[$k] < $datahours[0]['hours_begin_afternoon']) {
                    $dho=$hora->_getsumminutes($valhorasm[$k],'50');
                    $valhorasm[$k+1]=$dho[0]['hora'];
                    $k++;
                }

                $len=count($valhorasm);
                $w=0;

                for ($g=0; $g < $len; $g++) {
                    if ($valhorasm[$g]==$valhorast[0] && $w==0) {
                        $valhoras[0]=$datahours[0]['hours_begin'];
                        for ($k=0; $k < 20; $k++) {
                            $dho=$hora->_getsumminutes($valhoras[$k],'50');
                            $valhoras[$k+1]=$dho[0]['hora'];
                        }
                        $w=1;
                    }
                }
                if ($w==0) {
                    $j=0;
                    while ( $j < 12) {
                        $dho=$hora->_getsumminutes($valhorast[$j],'50');
                        $valhorast[$j+1]=$dho[0]['hora'];
                        $j++;
                    }
                    $endtarde=$valhorast[$j-1];
                }
            }
            else{
                $valhoras[0]=$datahours[0]['hours_begin'];
                for ($k=0; $k < 20; $k++) {
                    $dho=$hora->_getsumminutes($valhoras[$k],'50');
                    $valhoras[$k+1]=$dho[0]['hora'];
                }
            }
        }

        if ($valhorasm) {
            // valida que la hora de inicio sea multiplo de 50 min.(MAÑANA)
            $lenm=count($valhorasm);
            for ($zm=0; $zm < $lenm-2; $zm++) {
                if ($hora_ini==$valhorasm[$zm]) {
                    $valinim=1;
                }
            }
        }
        if ($valhorast) {
            // valida que la hora de inicio sea multiplo de 50 min.(TARDE)
            $lentar=count($valhorast);
            for ($zt=0; $zt < $lentar; $zt++) {
                if ($hora_ini==$valhorast[$zt]) {
                    $valinit=1;
                }
            }

        }

        if($valhoras){
            // valida que la hora de inicio sea multiplo de 50 min.(TODO)
            for ($zz=0; $zz < 20; $zz++) {
                if ($hora_ini==$valhoras[$zz]) {
                    $valini=1;
                }
            }
        }

        if ($valinim=="1" or $valinit=="1" or $valini=="1") {
            //Sacamos de hora de finalizacion deacuerdo a la cantidad de horas academicas.
            $hora_fin[0]['hora']=$hora_ini;
            for ($x=0; $x < $hora_acad; $x++) {
                $hora_fin=$hora->_getsumminutes($hora_fin[0]['hora'],'50');
            }
            $hora_fin=$hora_fin[0]['hora'];

            $data=array();
            $data['eid']=$eid;
            $data['oid']=$oid;
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

            $is_electivo=$this->_getParam('electivo');

            if ($is_electivo=="S") {
                $horaexis=$hora->_getHorary($eid,$oid,$perid,$escid,$curid,$courseid,$turno,$subid,$uid,$hora_ini,$hora_fin,$data['day']);
                $horasem=null;
                $intervalHouricur=null;
                $intervalHourfcur=null;
                $horateach=$hora->_getHoraryXteacherXday($eid,$oid,$perid,$escid,$subid,$uid,$hora_ini,$hora_fin,$data['day']);
                $intervalHouriteach=$hora->_intervalHoraryi($eid,$oid,$perid,$uid,$data['day'],$hora_ini);
                $intervalHourfteach=$hora->_intervalHoraryf($eid,$oid,$perid,$uid,$data['day'],$hora_fin);
            }
            else{
                $horaexis=$hora->_getHorary($eid,$oid,$perid,$escid,$curid,$courseid,$turno,$subid,$uid,$hora_ini,$hora_fin,$data['day']);
                $horasem=$hora->_getHoraryXsemXturno($eid,$oid,$perid,$escid,$subid,$semid,$turno,$hora_ini,$hora_fin,$data['day']);
                $horateach=$hora->_getHoraryXteacherXday($eid,$oid,$perid,$escid,$subid,$uid,$hora_ini,$hora_fin,$data['day']);
                $intervalHouricur=$hora->_intervalHoraryicur($eid,$oid,$perid,$escid,$subid,$semid,$data['day'],$hora_ini);
                $intervalHourfcur=$hora->_intervalHoraryfcur($eid,$oid,$perid,$escid,$subid,$semid,$data['day'],$hora_fin);
                $intervalHouriteach=$hora->_intervalHoraryi($eid,$oid,$perid,$uid,$data['day'],$hora_ini);
                $intervalHourfteach=$hora->_intervalHoraryf($eid,$oid,$perid,$uid,$data['day'],$hora_fin);
            }

            if ($horaexis) {
                $json = array('sms'=>"¡ Ya existe un horario de otro curso en esta hora !");
            }
            elseif ($horasem) {
            $json = array('sms'=>"¡ Ya existe un horario de un curso de este semestre en esta hora !");
            }
            elseif ($horateach) {
                $json = array('sms'=>"¡ Ya existe un horario del docente en esta hora !");
            }
            elseif ($intervalHouricur) {
                $json = array('sms'=>"¡ Se cruza la hora de inicio del curso con otro curso del semestre !");
            }
            elseif ($intervalHourfcur) {
                $json = array('sms'=>"¡ Se cruza la hora de finalización del curso con otro curso del semestre !");
            }
            elseif ($intervalHouriteach) {
                $json = array('sms'=>"¡ Se cruza la hora de inicio del curso con otro curso del docente !");
            }
            elseif ($intervalHourfteach) {
                $json = array('sms'=>"¡ Se cruza la hora de finalización del curso con otro curso del docente !");
            }else {
                $valhorasm[$k-1]=(!empty($valhorasm[$k-1]))?$valhorasm[$k-1]:null;
                if ($data['hora_ini'] <= $valhorasm[$k-1]) {
                    if ($data['hora_fin'] <= $valhorasm[$k-1]) {
                        if($hora->_save($data)){
                            $json = array('status'=>true);
                        }
                    }else{
                        $json = array('sms'=>"¡ La Hora de Inicio no coincide con las horas académicas establecidas !<br> Por favor ingrese nuevamente. ");
                    }
                }
                else{
                    if ($hora->_save($data)) {
                        $json = array('status'=>true);
                    }
                }
            }

        }
        else{
            $json = array('sms'=>"¡ La Hora de Inicio no coincide con las horas académicas establecidas !<br> Por favor ingrese nuevamente.");
        }
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->data = $json;
    }

    public function horaryteacherAction()
    {
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours = new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        // exit();

        if ($datahours) {
            $hora=new Api_Model_DbTable_Horary();
            if ($datahours[0]['hours_begin_afternoon']) {
                $valhorasm[0]=$datahours[0]['hours_begin'];
                $valhorast[0]=$datahours[0]['hours_begin_afternoon'];
                $k=0;
                while ($valhorasm[$k] < $datahours[0]['hours_begin_afternoon']) {
                    $dho=$hora->_getsumminutes($valhorasm[$k],'50');
                    $valhorasm[$k+1]=$dho[0]['hora'];
                    $k++;
                }

                $len=count($valhorasm);
                $w=0;

                for ($g=0; $g < $len + 1 ; $g++) {
                    $valhorasm[$g]=(!empty($valhorasm[$g]))?$valhorasm[$g]:null;
                    if ($valhorasm[$g]==$valhorast[0] && $w==0) {

                        $valhoras[0]=$datahours[0]['hours_begin'];
                        for ($k=0; $k < 20; $k++) {
                            $dho=$hora->_getsumminutes($valhoras[$k],'50');
                            $valhoras[$k+1]=$dho[0]['hora'];
                        }
                        $this->view->valhoras=$valhoras;
                        $w=1;
                    }
                }
                if ($w==0) {
                    unset($valhorasm[$k]);
                    $this->view->valhorasm=$valhorasm;
                    $j=0;
                    while ( $j < 12) {
                        $dho=$hora->_getsumminutes($valhorast[$j],'50');
                        $valhorast[$j+1]=$dho[0]['hora'];
                        $j++;
                    }
                    $endtarde=$valhorast[$j-1];
                    $this->view->valhorast=$valhorast;
                }
            }
            else{
                $valhoras[0]=$datahours[0]['hours_begin'];
                for ($k=0; $k < 20; $k++) {
                    $dho=$hora->_getsumminutes($valhoras[$k],'50');
                    $valhoras[$k+1]=$dho[0]['hora'];
                }
                $this->view->valhoras=$valhoras;
            }

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['perid']=$perid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['teach_uid']=$uid;
            $where['teach_pid']=$pid;
            $order=array('day');
            $dathora=$hora->_getFilter($where,$attrib=null,$order);
            // print_r($dathora);
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

    public function horaryteacherpracAction()
    {
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours = new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        // exit();

        if ($datahours) {
            $hora=new Api_Model_DbTable_Horary();
            if ($datahours[0]['hours_begin_afternoon']) {
                $valhorasm[0]=$datahours[0]['hours_begin'];
                $valhorast[0]=$datahours[0]['hours_begin_afternoon'];
                $k=0;
                while ($valhorasm[$k] < $datahours[0]['hours_begin_afternoon']) {
                    $dho=$hora->_getsumminutes($valhorasm[$k],'50');
                    $valhorasm[$k+1]=$dho[0]['hora'];
                    $k++;
                }

                $len=count($valhorasm);
                $w=0;

                for ($g=0; $g < $len + 1 ; $g++) {
                    $valhorasm[$g]=(!empty($valhorasm[$g]))?$valhorasm[$g]:null;
                    if ($valhorasm[$g]==$valhorast[0] && $w==0) {

                        $valhoras[0]=$datahours[0]['hours_begin'];
                        for ($k=0; $k < 20; $k++) {
                            $dho=$hora->_getsumminutes($valhoras[$k],'50');
                            $valhoras[$k+1]=$dho[0]['hora'];
                        }
                        $this->view->valhoras=$valhoras;
                        $w=1;
                    }
                }
                if ($w==0) {
                    unset($valhorasm[$k]);
                    $this->view->valhorasm=$valhorasm;
                    $j=0;
                    while ( $j < 12) {
                        $dho=$hora->_getsumminutes($valhorast[$j],'50');
                        $valhorast[$j+1]=$dho[0]['hora'];
                        $j++;
                    }
                    $endtarde=$valhorast[$j-1];
                    $this->view->valhorast=$valhorast;
                }
            }
            else{
                $valhoras[0]=$datahours[0]['hours_begin'];
                for ($k=0; $k < 20; $k++) {
                    $dho=$hora->_getsumminutes($valhoras[$k],'50');
                    $valhoras[$k+1]=$dho[0]['hora'];
                }
                $this->view->valhoras=$valhoras;
            }

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['perid']=$perid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['teach_uid']=$uid;
            $where['teach_pid']=$pid;
            $order=array('day');
            $dathora=$hora->_getFilter($where,$attrib=null,$order);
            // print_r($dathora);
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

    public function horaryteachersuportAction()
    {
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours = new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);
        // exit();

        if ($datahours) {
            $hora=new Api_Model_DbTable_Horary();
            if ($datahours[0]['hours_begin_afternoon']) {
                $valhorasm[0]=$datahours[0]['hours_begin'];
                $valhorast[0]=$datahours[0]['hours_begin_afternoon'];
                $k=0;
                while ($valhorasm[$k] < $datahours[0]['hours_begin_afternoon']) {
                    $dho=$hora->_getsumminutes($valhorasm[$k],'50');
                    $valhorasm[$k+1]=$dho[0]['hora'];
                    $k++;
                }

                $len=count($valhorasm);
                $w=0;

                for ($g=0; $g < $len + 1 ; $g++) {
                    $valhorasm[$g]=(!empty($valhorasm[$g]))?$valhorasm[$g]:null;
                    if ($valhorasm[$g]==$valhorast[0] && $w==0) {

                        $valhoras[0]=$datahours[0]['hours_begin'];
                        for ($k=0; $k < 20; $k++) {
                            $dho=$hora->_getsumminutes($valhoras[$k],'50');
                            $valhoras[$k+1]=$dho[0]['hora'];
                        }
                        $this->view->valhoras=$valhoras;
                        $w=1;
                    }
                }
                if ($w==0) {
                    unset($valhorasm[$k]);
                    $this->view->valhorasm=$valhorasm;
                    $j=0;
                    while ( $j < 12) {
                        $dho=$hora->_getsumminutes($valhorast[$j],'50');
                        $valhorast[$j+1]=$dho[0]['hora'];
                        $j++;
                    }
                    $endtarde=$valhorast[$j-1];
                    $this->view->valhorast=$valhorast;
                }
            }
            else{
                $valhoras[0]=$datahours[0]['hours_begin'];
                for ($k=0; $k < 20; $k++) {
                    $dho=$hora->_getsumminutes($valhoras[$k],'50');
                    $valhoras[$k+1]=$dho[0]['hora'];
                }
                $this->view->valhoras=$valhoras;
            }

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['perid']=$perid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['teach_uid']=$uid;
            $where['teach_pid']=$pid;
            $order=array('day');
            $dathora=$hora->_getFilter($where,$attrib=null,$order);
            // print_r($dathora);
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

    public function deletehoraryAction(){
        $this->_helper->layout()->disableLayout();
        $json=array();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $teach_uid=base64_decode($this->_getParam('uid'));
        $teach_pid=base64_decode($this->_getParam('pid'));
        $escid=base64_decode($this->_getParam('escid'));
        $subid=base64_decode($this->_getParam('subid'));
        $perid=base64_decode($this->_getParam('perid'));
        $curid=base64_decode($this->_getParam('curid'));
        $courseid=base64_decode($this->_getParam('courseid'));
        $turno=base64_decode($this->_getParam('turno'));
        $horaini=base64_decode($this->_getParam('horaini'));
        $horafin=base64_decode($this->_getParam('horafin'));
        $dia=base64_decode($this->_getParam('diasemana'));

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
        $hora=new Api_Model_DbTable_Horary();
        if ($hora->_delete($data)) {
            $json = array('status'=>true);
        }
        else{
            $json = array('status'=>false);
        }
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->data = $json;

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
            $escid=$this->_getParam('escid');
            $subid=$this->_getParam('subid');
            $perid=$this->_getParam('perid');

            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->subid=$subid;

            $param=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $attrib=array('hoursid','hours_begin','hours_update','hours_begin_afternoon','hours_update_afternoon');
            $bdbegin= new Api_Model_DbTable_HoursBeginClasses();
            $datah=$bdbegin->_getFilter($param,$attrib);

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
                    $uhorary= new Api_Model_DbTable_Horary();
                    $data=array('esc'=>$frmdata['escid'],'per'=>$frmdata['perid'],'sub'=>$frmdata['subid']);
                    unset($frmdata['escid']);
                    unset($frmdata['perid']);
                    unset($frmdata['subid']);
                    unset($frmdata['update']);
                    unset($frmdata['hour']);
                    unset($frmdata['minute']);
                    unset($frmdata['hour_t']);
                    unset($frmdata['minute_t']);
                    unset($frmdata['pk']);

                    $reg_= new Api_Model_DbTable_HoursBeginClasses();
                    if ($reg_->_update($frmdata,$pk)) {
                        $uhorary->_updateHoraryAll($data);
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
