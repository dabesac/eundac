<?php

class Api_Model_DbTable_Poll extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_poll_question';
	protected $_primary = array('eid', 'oid', 'pollid');


      /* Busca el nombre de la persona deacuerdo a una palabra ingresada */
    public function _getPollDetail($where=null){
        try{
            $sql=$this->_db->query("
             SELECT DISTINCT ps.qid , ps.question,
             (select distinct count(*) from base_poll_restuls r inner join base_poll_aternatives a on r.oid= a.oid and r.altid=a.atlid
             where r.escid= rs.escid and r.code = rs.code and r.qid = ps.qid and r.eid= rs.eid and r.oid= rs.oid and a.position='1'
             ) AS SIEMPRE,
             (select distinct count(*) from base_poll_restuls r inner join base_poll_aternatives a on r.oid= a.oid and r.altid=a.atlid
             where r.escid= rs.escid and r.code = rs.code and r.qid = ps.qid  and r.eid= rs.eid and r.oid= rs.oid and a.position='2'
             ) AS CASI_SIEMPRE,
             (select distinct count(*) from base_poll_restuls r inner join base_poll_aternatives a on r.oid= a.oid and r.altid=a.atlid
             where r.escid= rs.escid and r.code = rs.code and r.qid = ps.qid  and r.eid= rs.eid and r.oid= rs.oid and a.position='3'
             ) AS AVECES,
             (select distinct count(*) from base_poll_restuls r inner join base_poll_aternatives a on r.oid= a.oid and r.altid=a.atlid
             where r.escid= rs.escid and r.code = rs.code and r.qid = ps.qid  and r.eid= rs.eid and r.oid= rs.oid and a.position='4'
             ) AS NUNCA
             from base_poll_restuls rs inner join base_poll_question ps
             on rs.qid=ps.qid and rs.eid=ps.eid and rs.oid=ps.oid inner join base_poll e
             on e.eid=ps.eid and e.oid=ps.oid and ps.pollid=e.pollid
             where rs.escid='".$where['escid']."' and e.perid='".$where['perid']."' and code = '".$where['codigo']."'
             order by ps.qid
            ");
            $row=$sql->fetchAll();
            return $row;  
        }catch (Exception $ex) {
            print "Error: Al momento de buscar el nombre de una persona".$ex->getMessage();
        }
    }


        public function _getPollTotal($where=null){
        try{
            $sql=$this->_db->query("
            select a.position,count(*) cantidad from base_poll_aternatives a inner join base_poll_restuls r on
            a.qid=r.qid and a.atlid=r.altid and a.eid=r.eid and a.oid=r.oid inner join base_poll_question p
            on p.eid=r.eid and p.oid=r.oid and p.qid=r.qid inner join base_poll e
            on e.eid=r.eid and e.oid=r.oid and p.pollid=e.pollid 
            where r.escid='".$where['escid']."' and e.perid='".$where['perid']."' and code = '".$where['codigo']."'
            group by a.position order by a.position             
            ");
            $row=$sql->fetchAll();
            return $row;  
        }catch (Exception $ex) {
            print "Error: Al momento de buscar el nombre de una persona".$ex->getMessage();
        }
    }


}
