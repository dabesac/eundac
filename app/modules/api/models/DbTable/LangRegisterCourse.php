<?php
class Api_Model_DbTable_LangRegisterCourse extends Zend_Db_Table_Abstract{
  protected $_name = 'lang_student_register_course';
  protected $_primary = array('eid', 'oid', 'perid', 'subid', 'pid', 'uid', 'course_id', 'type', 'turn_id');
  

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
          if($where['eid']=='' || $where['oid']=='') return false;
            $select = $this->_db->select();
            if ($attrib=='') $select->from("lang_student_register_course");
            else $select->from("lang_student_register_course",$attrib);
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
        if ($data['eid']=='' || $data['oid']=='' || $data['perid']=='' || $data['subid']=='' || $data['pid']=='' || $data['uid']=='' || $data['course_id']=='' || $data['type']=='' || $data['turn_id']=='') return false;
        return $this->insert($data);
        return false;     
    } catch (Exception $e) {
      print "Error: Save Period".$e->getMessage();
    }
  }

  public function _update($data,$pk){
      try {
          if ($pk['eid']=='' || $pk['oid']=='' || $pk['perid']=='' || $pk['subid']==''  || $pk['pid']==''  || $pk['uid']=='' || $pk['course_id']=='' || $pk['type']=='' || $pk['turn_id']=='') return false;
          $where = "eid = '".$pk['eid'].
                    "' and oid='".$pk['oid'].
                    "' and perid='".$pk['perid'].
                    "' and subid='".$pk['subid'].
                    "' and pid='".$pk['pid'].
                    "' and uid='".$pk['uid'].
                    "' and course_id='".$pk['course_id'].
                    "' and type='".$pk['type'].
                    "' and turn_id='".$pk['turn_id']."'";
          return $this->update($data, $where);
          return false;
      } catch (Exception $e) {
        print "Error: Update Course".$e->getMessage();
      }
  }

  public function _updateState($data,$pk){
      try {
          if ($pk['eid']=='' || $pk['oid']=='' || $pk['perid']=='' || $pk['subid']=='' || $pk['pid']=='' || $pk['uid']=='') return false;
          $where = "eid = '".$pk['eid']."' and perid='".$pk['perid']."' and pid='".$pk['pid']."'";
          return $this->update($data, $where);
          return false;
      } catch (Exception $e) {
        print "Error: Update Course".$e->getMessage();
      }
  }


  //Retorna la lista de periodos
  /*public function _getMatriculados($eid,$perid,$course_id){
      try{
          $f=$this->fetchAll("eid='$eid' and perid='$perid' and course_id='$course_id'");
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
           select c.course_id,c.nombre from idiomas_matricula_curso as m
            inner join idiomas_cursos as c
            on m.course_id=c.course_id
            where perid='$perid'
            group by c.course_id,c.nombre
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