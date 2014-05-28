<?php
class Api_Model_DbTable_LangUser extends Zend_Db_Table_Abstract
{
    protected $_name = 'lang_user';
    protected $_primary = array("eid","pid");

    public function _getFilter($where=null,$attrib=null,$orders=null){
      try{
        if($where['eid']=='') return false;
          $select = $this->_db->select();
          if ($attrib=='') $select->from("lang_user");
          else $select->from("lang_user",$attrib);
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
        print "Error: Read Filter Users ".$e->getMessage();
      }
    }

    public function _getOne($where=array()){
    try{
      if ($where['eid']=="" || $where['pid']=='') return false;
      $wherestr="eid = '".$where['eid']."' and pid='".$where['pid']."'";
      $row = $this->fetchRow($wherestr);
      if($row) return $row->toArray();
      return false;
    }catch (Exception $e){
      print "Error: Read One Course ".$e->getMessage();
    }
  }

    public function _save($data)
    {
      try{
        if ($data['eid']=='' ||  $data['pid']=='' || $data['rid']=='' || $data['pwd']=='' || $data['state']=='') return false;
        return $this->insert($data);
        return false;
      }catch (Exception $e){
          print "Error: Save User ".$e->getMessage();
      }
    }

    public function _update($data,$pk){
      try {
          if ($pk['eid']=='' || $pk['pid']=='') return false;
          $where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."'";
          return $this->update($data, $where);
          return false;
      } catch (Exception $e) {
        print "Error: Update Person".$e->getMessage();
      }
    }

       
    /*//  Retorna los datos de un usuario deacuerdo al codigo de Usuario
    public function _getUser($eid='',$oid='',$uid=''){
        try{
            if ($eid==''|| $oid=='' || $uid=='' ) return false;
            $r = $this->fetchRow("eid='$eid' and oid='$oid' and uid='$uid'");
            return ($r)?$r->toArray():false;
        }catch (Exception $ex){
            print "Error: Leer datos de usuario ".$ex->getMessage();
        }
 
     }
    
    // Guarda un registro en la tabla Usuario 
    public function _guardar($data){
        try{
         if ($data['eid']=="" || $data['oid']=="" || $data['escid']=="" || $data['sedid']=="" || $data['pid']=="" || $data['uid']=="") return false;
            return $this->insert($data);
        }catch (Exception $ex){
           //print "Error: Guardar Registro de Usuario".$ex->getMessage();
        } 
    }
    
    // Actualiza datos($data) de un usuario deacuerdo a una cadena $str 
    public function _updatestr($data,$str){
        try{
            if ($data=='' || $str=="") return false;
            return $this->update($data,$str);
        }catch (Exception $ex){
            // print "Error: Actualizando datos del Usuario ".$ex->getMessage();
        }
    }


   
    // Actualiza datos($array) de un usuario deacuerdo a los primary key "eid","oid","escid","sedid","pid","uid"
    public function _update($array,$eid,$oid,$escid,$sedid,$pid,$uid){
        try{
            if ($array=='' || $eid==''|| $oid=='' || $escid=='' || $sedid=='' || $pid=='' || $uid=='' ) return false;
            $str="eid='$eid' and oid='$oid' and escid='$escid' and sedid='$sedid' and pid='$pid' and uid='$uid'";
            return ($this->update($array,$str));
        }catch (Exception $ex){
            print "Error: Actualizando datos del Usuario ".$ex->getMessage();
        }
    }

    // Elimina un registro segun el parametro enviado
    public function _eliminar($uid,$escid,$pid,$oid,$eid,$sedid){
        try{
            if ($uid=="" || $escid=="" || $pid=="" || $oid=="" || $eid=="" || $sedid=="") return false;
            return $this->delete("uid='$uid' and escid='$escid' and pid='$pid' and oid='$oid' and eid='$eid' and sedid='$sedid'");
        }catch (Exception $ex){
            print "Error: Eliminando datos de un registro del Usuario ".$ex->getMessage();
        }

    } 
   

    // Retorna los datos del alumno deacuerdo a una palabra ingresada($nom) 
    public function _getUsuarioXCodigo($uid='',$rid='',$eid='',$oid=''){
        try{
            $sql=$this->_db->query("
            select ape_pat || ' ' || ape_mat || ', ' || nombres as nombrecompleto
                ,u.uid,u.rid,u.sedid,u.eid,u.oid,u.escid,u.pid,p.nombres,p.ape_pat,p.ape_mat,u.escid,u.categoria_doc,u.condision_doc,u.dedicasion_doc,u.estado , e.nom_escuela
            from usuario as u
            inner join persona as p
               on u.pid=p.pid and u.eid=p.eid and u.oid=p.oid
            inner join escuela as e
                on u.escid=e.escid and u.eid=e.eid and u.oid=e.oid
               where u.eid='$eid' and u.oid ='$oid' and u.rid='$rid' and u.uid='$uid'
               order by p.ape_pat,p.ape_mat,p.nombres
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) {
            print "Error: Retornando los datos del alumno deacuerdo a una palabra ingresada".$ex->getMessage();
        }
    }
    //busqueda por apellidos y nombres
    public function _getUsrioXnombre($eid='',$rid='',$nom='')
    {
            try {
            if ($eid=='' || $rid=='' || $nom=='') return false;
            $sql=$this->_db->query("
                select ape_pat || ' ' || ape_mat || ', ' || nombres as nombrecompleto
               ,u.rid,u.eid,u.pid,p.nombres,p.ape_pat,p.ape_mat 
               from idiomas_usuario as u
               inner join persona as p
               on u.pid=p.pid and u.eid=p.eid  
               where  u.eid='$eid'and rid='$rid' and u.estado='A' and upper(ape_pat) || ' ' || upper(ape_mat) || ', ' || upper(nombres) like '%$nom%'
               order by p.ape_pat,p.ape_mat,p.nombres");
            $row=$sql->fetchAll();
            return $row;
        } catch (Exception $e) {
            
        }
    }
    // busqueda por usuario
    public function _getUsrioXusuario($eid='',$rid='',$pid='')
    {
            try {
            if ($eid=='' || $rid=='' || $pid=='') return false;
            $sql=$this->_db->query("
                select ape_pat || ' ' || ape_mat || ', ' || nombres as nombrecompleto
               ,u.rid,u.eid,u.pid,p.nombres,p.ape_pat,p.ape_mat 
               from idiomas_usuario as u
               inner join persona as p
               on u.pid=p.pid and u.eid=p.eid  
               where  u.eid='$eid'and  rid='$rid' and u.pid='$pid'
               ");
            $row=$sql->fetchAll();
            return $row;
        } catch (Exception $e) {
            print "Error: Retornando".$ex->getMessage();
        }
    }

    
    public function _getUsuariotodos(){

      try{
          $f=$this->fetchAll();
          if($f) return $f->toArray(); 
          return false;
      }catch (Exception $e){
          print "Error en lecturar usuarios".$e->getMessage();
      }
    }
    
    public function _getUsuarioxpersona($pid)
     {
        try{
            $sql=$this->_db->query(" 
            select u.pid, p.ape_pat || ' ' || p.ape_mat || ', ' || p.nombres as nombrecompleto
            from persona p 
            inner join idiomas_usuario u 
            on p.pid=u.pid  where u.pid='$pid' 
            
            ");
            return $sql->fetchAll(); 

            }  
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }*/
}