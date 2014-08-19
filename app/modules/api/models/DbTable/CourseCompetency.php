<?php

class Api_Model_DbTable_CourseCompetency extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_course_competency';
	protected $_primary = array('eid', 'oid', 'escid', 'subid', 'courseid', 'curid', 'perid', 'turno');

    public function _getOne($where=array()){
        try {
            if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']==""  ||
                $where['courseid']=="" || $where['curid']=="" || $where['perid']=="" || $where['turno']==""  
               ) return false;
            $wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and curid='".$where['curid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' and turno='".$where['turno']."' and perid='".$where['perid']."'";
                $row = $this->fetchRow($wherestr);
                if($row) return $row->toArray();
                return false;
        } catch (Exception $e) {
            echo "Error: read one course competency".$e->getMessage();
        }
    }

    public function _save($data){
        try {
            if ($data['eid']=="" || $data['oid']=="" || $data['escid']=="" || $data['subid']==""  ||
                $data['courseid']=="" || $data['curid']=="" || $data['perid']=="" || $data['turno']==""  
               ) return false;
            return $this->insert($data);
            return false;
        } catch (Exception $e) {
            echo "Error: save data competency".$e->getMessage();
        }
    }
    public function _update($data,$pk){
        try {
            if ($pk['eid']=="" || $pk['oid']=="" || $pk['escid']=="" || $pk['subid']==""  ||
                $pk['courseid']=="" || $pk['curid']=="" || $pk['perid']=="" || $pk['turno']==""  
               ) return false;

            $where="eid = '".$pk['eid']."' and oid='".$pk['oid']."' and curid='".$pk['curid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."' and turno='".$pk['turno']."' and perid='".$pk['perid']."'";
            return $this->update($data,$where);
            return false;
        } catch (Exception $e) {
            echo "Error: update data competency".$e->getMessage();
        }
    }
    public function _exists_persentage($where=array()){
        try {
            if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']==""  ||
                $where['courseid']=="" || $where['curid']=="" || $where['perid']=="" || $where['turno']==""  
               ) return false;
            $sql = $this->_db->query("
                select count(*) as cantidad from base_course_competency
                where  
                eid = '".$where['eid']."' and 
                oid = '".$where['oid']."' and 
                turno = '".$where['turno']."' and 
                courseid = '".$where['courseid']."' and
                escid = '".$where['escid']."' and
                curid = '".$where['curid']."' and
                perid = '".$where['perid']."' and 
                subid = '".$where['subid']."'
            ");
            $row = $sql->fetchAll();
            return $row;
        } catch (Exception $e) {
            echo "Error: read exists persentage".$e->getMessage();
        }
    }

    public function _getFilter($where=null,$attrib=null,$orders=null){
        try {


            $select = $this->_db->select();
                if ($attrib=='') $select->from("base_course_competency");
                else $select->from("base_course_competency",$attrib);
                foreach ($where as $atri=>$value){
                    $select->where("$atri = ?", $value);
                }
                if ($orders){
                    foreach ($orders as $key => $order) {
                            $select->order($order);
                    }
                }
                $results = $select->query();
                $rows = $results->fetchAll();
                if ($rows) return $rows;
                return false;

        } catch (Exception $e) {
            echo "Error: read  complete persentage".$e->getMessage();
            
        }
    }
    public function _modifypercentage($where=null){
        try {
            if ($where['eid']=='' || $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || 
                $where['curid']=='' || $where['courseid']=='' || $where['turno']=='' || $where['perid']=='' || 
                $where['txtppporcentaje1']=='' || $where['txtppporcentaje2']=='' || $where['txtppporcentaje3']=='' || 
                $where['unid']=='') return false;

            $eid=$where['eid'];
            $oid=$where['oid'];
            $escid=$where['escid'];
            $subid=$where['subid'];
            $curid=$where['curid'];
            $courseid=$where['courseid'];
            $turno=$where['turno'];
            $perid=$where['perid'];
            $p1=$where['txtppporcentaje1'];
            $p2=$where['txtppporcentaje2'];
            $p3=$where['txtppporcentaje3'];
            $unid=$where['unid'];           
            
            $sql=$this->_db->query("
                select * from change_recalculate_notes_competency('$eid','$oid','$escid','$subid','$curid','$courseid','$turno','$perid','$p1','$p2','$p3','$unid') AS
                (
                    ".'DNI'." character varying,
                    ".'CODIGO'." character varying,
                    ".'nota1'." character varying,
                    ".'nota2'." character varying,
                    ".'nota3'." character varying,
                    ".'promedio'." integer,
                    ".'PROMEDIO1'." character varying,
                    ".'PROMEDIO2'." character varying,
                    ".'PROMEDIO3'." character varying,
                    ".'PROMEDIO4'." character varying,
                    ".'NOTAFINAL'." integer
                )
                "); 
            if ($sql) return $sql->fetchAll();
            return false;
        } catch (Exception $e) {
            print "Error: disapprovedcoursemore50percentlast3 ".$e->getMessage();
        }
    }
}
