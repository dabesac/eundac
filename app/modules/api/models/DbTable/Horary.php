<?php

class Api_Model_DbTable_Horary extends Zend_Db_Table_Abstract
{
	protected $_name = 'horary_periods';
	protected $_primary = array("eid","oid","hid","escid","subid","perid","courseid","curso","turno");
    protected $_sequence ="s_horaryperiods";

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("horary_periods");
				else $select->from("horary_periods",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				foreach ($orders as $key => $order) {
						$select->order($order);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Curricula ".$e->getMessage();
		}
	}

	/*La función suma los minutos de una hora indicada*/
    public function _getsumminutes($hora,$nummin){
        $sql=$this->_db->query("select time '$hora' + interval '$nummin minutes' as hora");
        $row=$sql->fetchAll();
        return $row;
    }

    public function _getsumdate($fecha,$numdia){
        $sql=$this->_db->query("select date('$fecha') + integer '$numdia' as dia");
        $row=$sql->fetchAll();
        return $row;
    }

    public function _getHorary($eid='',$oid='',$perid='',$escid='',$curid='',$cursoid='',$turno='',$subid='',$teach_uid='',$fecha='', $hora_ini='', $hora_fin=''){
        try
        {
            if ($eid==""|| $oid==""||$perid==""||$escid==""||$curid==""||$cursoid==""||$turno==""||$subid==""||$d_uid=="") return false;
            if ($fecha=="") $fechas="";
            else $fechas=" and fecha='$fecha'"; 
            $f = $this->fetchAll("eid='$eid' and oid='$oid' and subid='$subid' and perid='$perid' and escid='$escid' and curid='$curid' 
                    and cursoid='$cursoid' and turno='$turno' and teach_uid='$teach_uid' $fechas and (hora_ini='$hora_ini' or hora_fin='$hora_fin')");
            if($f) return $f->toArray();
            return false;
        } 
        catch (Exception $ex)
        {
            print $ex->getMessage();
        }            
    }

    /*Devuelve el registro del horario de un semestre.De acuerdo a parametros enviados*/
    public function _getHoraryXsemXturno($eid='',$oid='',$perid='',$escid='',$subid='',$semid='',$turno='',$hora_ini='',$hora_fin='',$day=''){
        try
        {
            if ($eid==""|| $oid==""||$perid==""||$escid==""||$subid==""||$semid==""||$turno==""||$hora_ini==''||$hora_fin==''||$day=='') return false; 
            $f = $this->fetchAll("eid='$eid' and oid='$oid' and subid='$subid' and perid='$perid' and escid='$escid' 
                    and semid='$semid' and turno='$turno' and day='$day' and (hora_ini='$hora_ini' or hora_fin='$hora_fin')");
            if($f) return $f->toArray();
            return false;
        } 
        catch (Exception $ex)
        {
            print $ex->getMessage();
        }            
    }

    public function _save($data){
        try{
            if ($data['eid']==""||$data['oid']==""||$data['perid']==""||$data['curid']==""||$data['escid']==""||$data['courseid']==""||$data['turno']==""||$data['subid']==""||$data['teach_uid']==""||$data['teach_pid']=="") return false;
            return $this->insert($data);
            return false;
        }catch (Exception $ex){
            print "Error: Guardar datos de horarios. ".$ex->getMessage();
        }
    }

    public function _delete($data)
    {
        try{
            if ($data['eid']=='' || $data['oid']=='' ||  $data['escid']=='' || $data['subid']=='' || $data['perid']=='' || $data['courseid']=='' || $data['teach_uid']=='' ||$data['teach_pid']=='' ||$data['curid']=='' ||$data['turno']=='' ||$data['hora_ini']=='' ||$data['hora_fin']=='' ||$data['day']=='') return false;
            $where =    "eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."' and perid='".$data['perid']."' and courseid='".$data['courseid']."' and teach_uid='".$data['teach_uid']."' and teach_pid='".$data['teach_pid']."' and curid='".$data['curid']."' and turno='".$data['turno']."' and hora_ini='".$data['hora_ini']."' and hora_fin='".$data['hora_fin']."' and day='".$data['day']."'";          
            return $this->delete($where);
            return false;
        }catch (Exception $e){
            print "Error: Delete User".$e->getMessage();
        }
    }

 }
