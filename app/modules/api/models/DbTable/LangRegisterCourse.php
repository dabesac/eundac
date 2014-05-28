<?php
class Api_Model_DbTable_LangRegisterCourse extends Zend_Db_Table_Abstract{
  protected $_name = 'lang_registrationcourse';
  protected $_primary = array("turno","cid","eid","pid");
  

  public function _getAll($eid){
        try{
            $f=$this->fetchAll("eid='$eid'");
            if($f) return $f->toArray(); 
            return false;
        }catch (Exception $e){
            print "Error: al Mostrar  Matriculas por Curso
             (Module)".$e->getMessage();
        }
    }


  public function _getFilter($where=null,$attrib=null,$orders=null){
    try{
          if($where['eid']=='') return false;
            $select = $this->_db->select();
            if ($attrib=='') $select->from("lang_registrationcourse");
            else $select->from("lang_registrationcourse",$attrib);
            foreach ($where as $atri=>$value){
              $select->where("$atri = ?", $value);
            }
            if($orders){
              foreach ($orders as $key => $order) {
                $select->order($order);
              } 
            }
            
            $results = $select->query();
            $rows = $results->fetchAll();
            if ($rows) return $rows;
            return false;
      }catch (Exception $e){
        print "Error: Read Filter CoursesxRegister ".$e->getMessage();
      }
    }


  public function _save($data){
    try { 
        if ($data['eid']=='' || $data['pid']=='' || $data['perid']=='' || $data['cid']=='' || $data['turno']=='') return false;
        return $this->insert($data);
        return false;     
    } catch (Exception $e) {
      print "Error: Save Period".$e->getMessage();
    }
  }

  public function _update($data,$pk){
      try {
          if ($pk['eid']=='' || $pk['perid']=='' || $pk['pid']=='' || $pk['turno']=='' || $pk['cid']=='') return false;
          $where = "eid = '".$pk['eid']."' and perid='".$pk['perid']."' and pid='".$pk['pid']."' and turno='".$pk['turno']."' and cid='".$pk['cid']."'";
          return $this->update($data, $where);
          return false;
      } catch (Exception $e) {
        print "Error: Update Course".$e->getMessage();
      }
  }

  public function _updateState($data,$pk){
      try {
          if ($pk['eid']=='' || $pk['perid']=='' || $pk['pid']=='') return false;
          $where = "eid = '".$pk['eid']."' and perid='".$pk['perid']."' and pid='".$pk['pid']."'";
          return $this->update($data, $where);
          return false;
      } catch (Exception $e) {
        print "Error: Update Course".$e->getMessage();
      }
  }


  //Retorna la lista de periodos
  /*public function _getMatriculados($eid,$perid,$cid){
      try{
          $f=$this->fetchAll("eid='$eid' and perid='$perid' and cid='$cid'");
          if($f) return $f->toArray(); 
          return false;
      }catch (Exception $e){
          print "Erro: en Lecturar cursos".$e->getMessage();
      }
  } 


   public function _getMatXcurso($eid='',$perid='')
    {
        try
        {
            $sql=$this->_db->query("
           select c.cid,c.nombre from idiomas_matricula_curso as m
            inner join idiomas_cursos as c
            on m.cid=c.cid
            where perid='$perid'
            group by c.cid,c.nombre
           ");
           
           $row=$sql->fetchAll();
           return $row;  
        }
        catch (Exception $ex) 
        {
            print $ex->getMessage();
        }
    }*/

}