<?php
 class Report_PerformanceController extends Zend_Controller_Action{

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
            $where['subid'] = $this->sesion->subid;        
            $where['facid'] = $this->sesion->faculty->facid;
            $nomfac = $this->sesion->faculty->name;
            $nomesc = $this->sesion->speciality->name;
            $where['escid'] = $this->sesion->escid;
            $rid = $this->sesion->rid;
            $where['uid'] = $this->sesion->uid;
            $where['pid'] = $this->sesion->pid;
            $where['eid'] = $this->sesion->eid;
            $where['oid'] = $this->sesion->oid;
            $infodoc =new Api_Model_DbTable_Infoteacher();
            $doc=$infodoc->_getOne($where);
            $direc=$doc['is_director'];
            $this->view->esdirector=$direc;
            $this->view->escid=$where['escid'];
            $this->view->nom_escuela=$nomesc;
            $this->view->nom_fac=$nomfac;
            $this->view->subid=$where['subid'];
            $this->view->rid=$rid;
            $this->view->facid=$where['facid'];
            $fm=new Report_Form_Location();
            if ($rid=='RF' and $where['facid']=='TODO'){ 
                $fm->facid->setAttrib("enabled", false); 
            }
            else{
                if ($rid=='RC' or $rid=='VA' or $rid=='PD'){
                    $fm->facid->setAttrib("enabled", false); 
                }
                else{ 
                    $fm->facid->setAttrib("disabled", true); 
                }
            }
            $this->view->fm=$fm;    
        } catch (Exception $e) {
            print ('Error: get datos'. $e->getMessage());
            
        }

    }

  public function lperiodsAction()
    {
        try
        {
             $this->_helper->layout()->disableLayout();
             $where['eid']= $this->sesion->eid;
             $where['oid']= $this->sesion->oid;
             $where['subid'] = $this->sesion->subid;        
             $facid =$this->sesion->faculty->facid;
             $nomfac = $this->sesion->faculty->name;
             $nomesc = $this->sesion->speciality->name;
             $where['escid'] = $this->sesion->escid;
             $rid = $this->sesion->rid;
             $where['uid'] = $this->sesion->uid;
             $where['pid'] = $this->sesion->pid;
             $perid = $this->sesion->period->perid;
             $this->view->perid=$perid;
             $infodoc =new Api_Model_DbTable_Infoteacher();
             $doc=$infodoc->_getOne($where);
             $direc=$doc['is_director'];
             $this->view->esdirector=$direc;
             $anio = $this->_getParam("anio");
             if ($where['eid']=="" || $where['oid']==""||$anio=="") return false;
             $p = substr($anio, 2, 3);
             $where['p1']=$p."A";
             $where['p2']=$p."B";
             $where['p3']=$p."N";
             $periodos = new Api_Model_DbTable_Periods();
             $lper = $periodos->_getPeriodsXAyByN($where);
             $this->view->lper=$lper;
             $this->view->escid=$where['escid']; 
        }  
        catch(Exception $ex)
        {
          print $ex->getMessage();
        }
    }
public function listshoolAction(){
    try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;        
        $where['oid'] = $this->sesion->oid;
        $where['subid'] = $this->sesion->subid;
        $where['escid'] = $this->sesion->escid;  
        $rid = $this->sesion->rid;
        $where['facid'] = $this->_getParam("facid");
        $db_esc = new Api_Model_DbTable_Speciality();
        $datos['eid']=$where['eid'];
        $datos['oid']=$where['oid'];
        $datos['facid']=$where['facid'];
        $datos['subid']=$where['subid'];
        $datos['state']='A';
        if ($where['subid'] == '1901')
        {  if ($rid=='RF') {
              if($where['facid']=='2'){
              $escuelas = $db_esc->_getSchoolXSecundaria($where);
              $this->view->escuelas = $escuelas;
              }
              else{
                $escuelas = $db_esc->_getFilter($datos);
                $this->view->escuelas = $escuelas;
              }
            } 
            if ($rid=='RC' || $rid=='VA' ||  $rid=='PD')
            {
              $data['eid']=$where['eid'];
              $data['oid']=$where['oid'];
              $data['facid']=$where['facid'];
              $data['state']='A';
              $escuelas = $db_esc->_getFilter($data);
              $this->view->escuelas = $escuelas;
            }
        }
        else
        {  if ($rid=='RF')
            { if ($sedid=="1901") {
                $escuela= $db_esc->_getFilter($datos);  
               }
               else{
                  $dat['eid']=$where['eid'];
                  $dat['oid']=$where['oid'];
                  $dat['subid']=$where['subid'];
                $escuela= $db_esc->_getFilter($dat);
                }
             $this->view->escuelas=$escuela;  
            }       
        }
        if($where['escid']=='2ESTY'){
              $datx['eid']=$where['eid'];
              $datx['oid']=$where['oid'];
              $datx['subid']=$where['subid'];
              $datx['escid']=$where['escid'];
              $datx1['eid']=$where['eid'];
              $datx1['oid']=$where['oid'];
              $datx1['subid']=$where['subid'];
              $datx1['escid']="2ESCY";
              $escuelas = $db_esc->_getFilter($datx);
              $escuelas1 = $db_esc->_getFilter($datx1);
              $result = array_merge($escuelas, $escuelas1);
              $this->view->escuelas = $result;
             }
    }catch (Exception $ex){
        print "Error : ".$ex->getMessage();
    }
}


public function listcurriculaAction()
    {
      try{
          $this->_helper->layout()->disableLayout();
          $where['perid'] = $this->_getParam('periodo');
          $where['eid']= $this->sesion->eid;
          $where['oid']= $this->sesion->oid;
          $where['subid'] = $this->sesion->subid;        
          $where['escid'] = $this->_getParam('escid');
          $where['uid'] = $this->sesion->uid;
          $where['pid'] = $this->sesion->pid;
          if(!$where['escid']){
            $where['escid']=$this->sesion->escid;   
          }
          $this->view->escid=$where['escid'];
          $this->view->perid=$where['perid'];          
          $bdescuela = new Api_Model_DbTable_Speciality();
          $escuela=$bdescuela->_getOne($where);
          $nom_escuela = strtoupper($escuela['name']);
          $this->view->nom_escuela = $nom_escuela;      
          $bdcurri = new Api_Model_DbTable_Curricula();
          $listcurricula = $bdcurri->_getCurriculasXSchool($where);
          $this->view->listacurricula=$listcurricula;
          }catch(Exception $ex ){
              print ("Error Controlador Condiciones: ".$ex->getMessage());
          } 
    }

    public function rendimientoAction(){
      try{
          $this->_helper->layout()->disableLayout();
          $where['eid'] = $this->sesion->eid;
          $where['oid'] = $this->sesion->oid;
          $where['escid'] = base64_decode($this->_getParam("escid"));
          $where['curid'] = base64_decode($this->_getParam("curid"));
          $where['perid'] = base64_decode($this->_getParam("perid"));
          $data['eid']=$where['eid'];
          $data['oid']=$where['oid'];
          $data['escid']=$where['escid'];

          $this->view->escid=$where['escid'];
          $this->view->curid=$where['curid'];
          $this->view->eid=$where['eid'];
          $this->view->oid=$where['oid'];
          $this->view->perid=$where['perid']; 

          $bdescuela =new Api_Model_DbTable_Speciality();
          $escuela=$bdescuela->_getFilter($data);      
          $nom_escuela = strtoupper($escuela[0]['name']);
          $this->view->nom_escuela=$nom_escuela;   
          $db_rendimiento = new Api_Model_DbTable_Curricula();
          $rep_rendimiento= $db_rendimiento->_getPerformance($where);
          $this->view->rep_rendimiento=$rep_rendimiento;

      
      }
      catch (Exception $ex) {
          print "Error rendimiento: ".$ex->getMessage();
        }
    }


    public function printrendimientoAction(){
      try{
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = base64_decode($this->_getParam("escid"));
            $curid = base64_decode($this->_getParam("curid"));
            $perid = base64_decode($this->_getParam("perid"));

            $where = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'curid'=>$curid,'perid'=>$perid);
            $this->view->perid=$perid;

            $db_rendimiento = new Api_Model_DbTable_Curricula();
            $rep_rendimiento= $db_rendimiento->_getPerformance($where);
            $this->view->rep_rendimiento=$rep_rendimiento;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getFilter($where);
            $parent=$speciality[0]['parent'];
            $subid=$speciality[0]['subid'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);

            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality[0]['name'];
            }
            else{
                $spe['esc']=$speciality[0]['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namev=$names." ".$namep;
            $this->view->namev=$namev;
            $namefinal=$names." <br> ".$namep;

            $namelogo = (!empty($speciality[0]['header']))?$speciality[0]['header']:"blanco";
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality[0]['facid']);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);
            // $escid=$this->sesion->escid;
            // $where['escid']=$escid;

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'reporte_rendimiento');
            $dataim = $dbimpression->_getFilter($wheri);

            if ($dataim) {
              $pk = array('eid'=>$eid,'oid'=>$oid,'countid'=>$dataim[0]['countid'],'escid'=>$escid,'subid'=>$subid);
              $data_u = array('count_impression'=>$dataim[0]['count_impression']+1);

              $dbimpression->_update($data_u,$pk);
              $co=$data_u['count_impression'];
            }
            else{
              $data = array(
                  'eid'=>$eid,
                  'oid'=>$oid,
                  'uid'=>$uid,
                  'escid'=>$escid,
                  'subid'=>$subid,
                  'pid'=>$pid,
                  'type_impression'=>'reporte_rendimiento',
                  'date_impression'=>date('Y-m-d H:i:s'),
                  'pid_print'=>$uidim,
                  'count_impression'=>1
                  );
              $dbimpression->_save($data);
              $co=1;
            }

            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);

            $this->view->header=$header;
            $this->view->footer=$footer;

      }
      catch (Exception $ex) {
          print "Error rendimiento: ".$ex->getMessage();
        }
    }

    public function primerospuestosAction(){
      try{
          $this->_helper->layout()->disableLayout();
          $where['escid'] = base64_decode($this->_getParam("escid"));
          $where['curid'] = base64_decode($this->_getParam("curid"));
          $where['perid'] = base64_decode($this->_getParam("perid"));
          $nomcurricula = base64_decode($this->_getParam("nomcur"));
          $this->view->nomcurricula=$nomcurricula;
          $where['eid'] = $this->sesion->eid;
          $where['oid'] = $this->sesion->oid;
          $data['eid']=$where['eid'];
          $data['oid']=$where['oid'];
          $data['escid']=$where['escid'];
          $this->view->perid=$where['perid'];          
          $bdescuela =new Api_Model_DbTable_Speciality();
          $escuela=$bdescuela->_getFilter($data);      
          $nom_escuela = strtoupper($escuela[0]['name']);
          $this->view->nom_escuela=$nom_escuela;  
          $bdcurri = new Api_Model_DbTable_Curricula();
          $listcurricula=$bdcurri->_getCurriculasXSchool($where);
          $this->view->listcurricula=$listcurricula;  
          $rep_semestre= $bdcurri->_getSemesterXcurr($where); 
          $this->view->rep_semestre=$rep_semestre;
          $nro_rep_semestre=count($rep_semestre);
          $this->view->nrorepsemestre=$nro_rep_semestre;

          $where['semid'] = $this->_getParam("semid");
          $db_sem = new Api_Model_DbTable_Semester();
          $lissem = $db_sem->_getOne($where);
          $this->view->semestre=strtoupper($lissem['name']);                 
          $rep_alumnos= $bdcurri->_getPrimerospuestos($where);
          $this->view->escid=$where['escid'];
          $this->view->curid=$where['curid'];
          $this->view->perid=$where['perid'];
          $this->view->semid=$where['semid'];
          $this->view->rep_alumnos=$rep_alumnos;
      }
      catch (Exception $ex) 
      {
          print "Error rendimiento: ".$ex->getMessage();
      }
    }

    public function printprimerospuestosAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $escid = base64_decode($this->_getParam("escid"));
            $curid = base64_decode($this->_getParam("curid"));
            $perid = base64_decode($this->_getParam("perid"));
            $semid = ($this->_getParam("semid"));
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $this->view->perid=$perid;
            $this->view->curid=$curid;
  
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'curid'=>$curid,'perid'=>$perid,'semid'=>$semid);
            $db_sem = new Api_Model_DbTable_Semester();
            $lissem = $db_sem->_getOne($where);
            $this->view->semestre=strtoupper($lissem['name']); 
            $bdcurri = new Api_Model_DbTable_Curricula();
            $rep_alumnos= $bdcurri->_getPrimerospuestos($where);
            $this->view->rep_alumnos=$rep_alumnos;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getFilter($where);
            $parent=$speciality[0]['parent'];
            $subid=$speciality[0]['subid'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);

            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality[0]['name'];
            }
            else{
                $spe['esc']=$speciality[0]['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namev=$names." ".$namep;
            $this->view->namev=$namev;
            $namefinal=$names." <br> ".$namep;

            $namelogo = (!empty($speciality[0]['header']))?$speciality[0]['header']:"blanco";
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality[0]['facid']);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);
            // $escid=$this->sesion->escid;
            // $where['escid']=$escid;

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;
            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'reporte_primeros_puestos_'.$semid);
            $dataim = $dbimpression->_getFilter($wheri);

            if ($dataim) {
              $pk = array('eid'=>$eid,'oid'=>$oid,'countid'=>$dataim[0]['countid'],'escid'=>$escid,'subid'=>$subid);
              $data_u = array('count_impression'=>$dataim[0]['count_impression']+1);

              $dbimpression->_update($data_u,$pk);
              $co=$data_u['count_impression'];
            }
            else{
              $data = array(
                  'eid'=>$eid,
                  'oid'=>$oid,
                  'uid'=>$uid,
                  'escid'=>$escid,
                  'subid'=>$subid,
                  'pid'=>$pid,
                  'type_impression'=>'reporte_primeros_puestos_'.$semid,
                  'date_impression'=>date('Y-m-d H:i:s'),
                  'pid_print'=>$uidim,
                  'count_impression'=>1
                  );
              $dbimpression->_save($data);            
              $co=1;              
            }
            
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
          
            $this->view->header=$header;
            $this->view->footer=$footer;
        }
        catch (Exception $ex) 
        {
            print "Error rendimiento: ".$ex->getMessage();
        }
    }

    public function superiorAction(){
      try{
          $this->_helper->layout()->disableLayout();
          $where['escid'] = base64_decode($this->_getParam("escid"));
          $where['perid'] = base64_decode($this->_getParam("perid"));
          $superior = $this->_getParam("superior");
          $where['eid'] = $this->sesion->eid;
          $where['oid'] = $this->sesion->oid;
          $data['eid']=$where['eid'];
          $data['oid']=$where['oid'];
          $data['escid']=$where['escid'];
          $this->view->perid=$where['perid'];
          $bdescuela =new Api_Model_DbTable_Speciality();
          $escuela=$bdescuela->_getFilter($data);      
          $nom_escuela = strtoupper($escuela[0]['name']);
          $this->view->nom_escuela=($nom_escuela); 

          $db_35superior = new Api_Model_DbTable_Curricula();
       
          if ($superior=='3')
          {
            $rep_superior= $db_35superior->_get3superiorXcurricula($where);
          }
         
          if ($superior=='5')
          {
            $rep_superior= $db_35superior->_get5superiorXcurricula($where);
          }
          if(count($rep_superior)==0){
             ?> 
                 <script type="text/javascript"> 
                         alert("NINGUN ALUMNO ALCANZO EL PONDERADO PARA PERTENECER AL TERCIO SUPERIOR VERIFIQUE QUINTO SUPERIOR"); 
                 </script> 
                 <?php 
          }
         $this->view->rep_superior=$rep_superior;
         $this->view->superior=$superior;
         $this->view->escid=$where['escid'];
         $this->view->perid=$where['perid'];

      }
      catch (Exception $ex) {
          print "Error rendimiento: ".$ex->getMessage();
        }
    }


    public function printsuperiorAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $escid = base64_decode($this->_getParam("escid"));
            $perid = base64_decode($this->_getParam("perid"));
            $superior = $this->_getParam("superior");
            $oid = $this->sesion->oid;
            $eid = $this->sesion->eid;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'perid'=>$perid,'superior'=>$superior);
            $this->view->superior=$superior;
            $this->view->perid=$perid;
      
            $db_35superior = new Api_Model_DbTable_Curricula();
       
            if ($where['superior']=='3'){
                $rep_superior= $db_35superior->_get3superiorXcurricula($where);
            }
         
            if ($where['superior']=='5'){
                $rep_superior= $db_35superior->_get5superiorXcurricula($where);
            }
            $this->view->rep_superior=$rep_superior;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getFilter($where);
            $parent=$speciality[0]['parent'];
            $subid=$speciality[0]['subid'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);

            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality[0]['name'];
            }
            else{
                $spe['esc']=$speciality[0]['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namev=$names." ".$namep;
            $this->view->namev=$namev;
            $namefinal=$names." <br> ".$namep;

            $namelogo = (!empty($speciality[0]['header']))?$speciality[0]['header']:"blanco";
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality[0]['facid']);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);
            // $escid=$this->sesion->escid;
            // $where['escid']=$escid;

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'reporte_'.$superior.'_superior');
            $dataim = $dbimpression->_getFilter($wheri);

            if ($dataim) {
              $pk = array('eid'=>$eid,'oid'=>$oid,'countid'=>$dataim[0]['countid'],'escid'=>$escid,'subid'=>$subid);
              $data_u = array('count_impression'=>$dataim[0]['count_impression']+1);

              $dbimpression->_update($data_u,$pk);
              $co=$data_u['count_impression'];
            }
            else{
              $data = array(
                  'eid'=>$eid,
                  'oid'=>$oid,
                  'uid'=>$uid,
                  'escid'=>$escid,
                  'subid'=>$subid,
                  'pid'=>$pid,
                  'type_impression'=>'reporte_'.$superior.'_superior',
                  'date_impression'=>date('Y-m-d H:i:s'),
                  'pid_print'=>$uidim,
                  'count_impression'=>1
                  );
              $dbimpression->_save($data);            
              $co=1;
            }
              
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
          
            $this->view->header=$header;
            $this->view->footer=$footer;
    
        }
        catch (Exception $ex) {
            print "Error rendimiento: ".$ex->getMessage();
        }
    }
 }