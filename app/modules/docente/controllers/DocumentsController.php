<?php

class Docente_DocumentsController extends Zend_Controller_Action {

   public function init()
   {
      $sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      if (!$login->rol['module']=="docente"){
        $this->_helper->redirector('index','index','default');
      }
      $this->sesion = $login;   
   }
    
   public function indexAction(){
      $server = new Eundac_Connect_openerp();

      //DataBases
      $personDocumentDb = new Api_Model_DbTable_PersonDocument();

      $fullName = $this->sesion->infouser['fullname'];
      $pid      = $this->sesion->pid;
      $oid      = $this->sesion->pid;
      $escid    = $this->sesion->escid;

      $dataDocente['fullName'] = $fullName;
      $dataDocente['pid']      = $pid;

      $day   = date('d');
      $month = date('m');
      $year  = date('Y');
      switch ($month) {
         case 1:
            $nameMonth = 'Enero';
            break;
         case 2:
            $nameMonth = 'Febrero';
            break;
         case 3:
            $nameMonth = 'Marzo';
            break;
         case 4:
            $nameMonth = 'Abril';
            break;
         case 5:
            $nameMonth = 'Mayo';
            break;
         case 6:
            $nameMonth = 'Junio';
            break;
         case 7:
            $nameMonth = 'Julio';
            break;
         case 8:
            $nameMonth = 'Agosto';
            break;
         case 9:
            $nameMonth = 'Septiembre';
            break;
         case 10:
            $nameMonth = 'Octubre';
            break;
         case 11:
            $nameMonth = 'Noviembre';
            break;
         case 12:
            $nameMonth = 'Diciembre';
            break;
         default:
            $nameMonth = ':o!! Un mes que no existe';
            break;
      }

      $dataDocente['fecha'] = $day.' de '.$nameMonth.' del '.$year;


      //Codigo del Docente en el ERP
      $query = array(
                   array('column'   => 'identification_id',
                         'operator' => '=',
                         'value'    =>  $pid,
                         'type'     => 'string' )
                 );
      $idTeacher   = $server->search('hr.employee', $query);
      $attributes  = array('id');
      $dataTeacher = $server->read($idTeacher, $attributes, 'hr.employee');

      $dataDocente['erpId'] = $dataTeacher[0]['id'];

      $this->view->dataDocente = $dataDocente;

      //Buscar la solicitud en el ERP
      $query = array(
                     array('column'   => 'code',
                           'operator' => '=',
                           'value'    =>  'SOL',
                           'type'     => 'string' )
                    );
      $idDocumentType   = $server->search('procedure.documentary.documenttype', $query);
      $attributes       = array('id');
      $dataDocumentType = $server->read($idDocumentType, $attributes, 'procedure.documentary.documenttype');

      //Buscar tipo de documentos en el ERP para esta escuela...
      $query = array(
                     array('column'   => 'of_id',
                           'operator' => '=',
                           'value'    =>  $escid,
                           'type'     => 'string' )
                    );
      $idDepartment   = $server->search('hr.department', $query);
      $attributes     = array('id');
      $dataDepartment = $server->read($idDepartment, $attributes, 'hr.department');

      $query = array(
                     array('column'   => 'department_id',
                           'operator' => '=',
                           'value'    =>  $dataDepartment[0]['id'],
                           'type'     => 'int' ),

                     array('column'   => 'name',
                           'operator' => '=',
                           'value'    =>  $dataDocumentType[0]['id'],
                           'type'     => 'int' )
                    );

      $idDocumentTypeEsc   = $server->search('procedure.documentary.type', $query);
      $attributes          = array('id');
      $dataDocumentTypeEsc = $server->read($idDocumentTypeEsc, $attributes, 'procedure.documentary.type');

      $dataDocument = array();
      if ($dataDocumentTypeEsc) {
         $dataDocument['documentsType'][0]['id']   = $dataDocumentTypeEsc[0]['id'];
         $dataDocument['documentsType'][0]['name'] = 'Solicitud';
      }

      /*Buscas oficinas a las que enviar, por el momento solo informatica*/
      $dataDocument['oficinaEnvio'] = '9';

      //Acumulado de Documentos del Docente
      $where = array('eid'           => $eid,
                     'pid'           => $pid,
                     'document_type' => 'SOL' );
      $attrib = array('acumulado');
      $dataPersonDocument = $personDocumentDb->_getFilter($where, $attrib);
      $dataDocument['acumulado'] = 1;
      if ($dataPersonDocument) {
         $dataDocument['acumulado'] = $dataPersonDocument[0]['acumulado'] + 1;
      }
      //print_r($dataDocument);
      $this->view->dataDocument = $dataDocument;
   }

    public function listdocumentssendAction(){
        $this->_helper->layout->disableLayout();

        $fullName = $this->sesion->infouser['fullname'];
        $pid      = $this->sesion->pid;

        $dataDocente['pid']      = $pid;
        $dataDocente['fullName'] = $fullName;

        $this->view->dataDocente = $dataDocente;

      //Codigo del Docente en el ERP
      /*$query = array(
                   array('column'   => 'identification_id',
                         'operator' => '=',
                         'value'    =>  $pid,
                         'type'     => 'string' )
                 );
      $idTeacher   = $server->search('hr.employee', $query);
      $attributes  = array('id');
      $dataTeacher = $server->read($idTeacher, $attributes, 'hr.employee');

      $dataDocente['erpId'] = $dataTeacher[0]['id'];

      //Buscar Documentos Enviados
      $query = array(
                   array('column'   => 'create_uid',
                         'operator' => '=',
                         'value'    =>  $dataDocente['erpId'],
                         'type'     => 'int' )
                 );
      $idDocumentsSend   = $server->search('procedure.documentary.document', $query);
      $attributes        = array('id');
      $dataDocumentsSend = $server->read($idDocumentsSend, $attributes, 'procedure.documentary.document');

      $dataDocument['documents'] = array();
      if ($dataDocumentsSend) {
         foreach ($dataDocumentsSend as $c => $document) {
            $dataDocument['documents'][$c]['name']  = $document['name'];
            $dataDocument['documents'][$c]['issue'] = $document['asunto'];
            $dataDocument['documents'][$c]['state'] = $document['state'];
         }
      }*/
    }

   public function senddocumentAction(){
        $server = new Eundac_Connect_openerp();

        $this->_helper->layout()->disableLayout();
        $eid = $this->sesion->eid;

        //DataBases
        $personDocumentDb = new Api_Model_DbTable_PersonDocument();

        $formData = $this->getRequest()->getPost();
        if ($formData['abstractDocument'] != '') {
            //Motivo
            switch ($formData['motivo']) {
                case 'openAct':
                   $motivo = 'Re-apertura de Acta';
                   break;
                case 'openAssistance':
                   $motivo = 'Re-apertura de Asistencia';
                   break;
                case 'openSyllabus':
                   $motivo = 'Re-apertura de Sílabo';
                   break;
                case 'openAcademicReport':
                   $motivo = 'Re-apertura de Reporte Académico';
                   break;
                case 'openTutorshipReport':
                   $motivo = 'Re-apertura de Reporte de Tutoría';
                   break;
                default:
                   $motivo = 'Otros';
                   break;
            }
            $documentType = explode('-', $formData['typeDocument']);
            $dataSaveDocument = array(  
                                        //'create_uid'    => $formData['erpId'],
                                        'number'        => $formData['numero'],
                                        'type_document' => '1',
                                        'name'          => 'Solicitud',
                                        'fecha'         => date('Y-m-d'),
                                        'asunto'        => $motivo,
                                        'type'          => 'E',
                                        'department_id' => $formData['oficinaEnvio'] );

            print_r($dataSaveDocument);
            $create = $server->create('procedure.documentary.document', $dataSaveDocument);
            print_r($create);
            /*if ($create) {
                if ($formData['numero'] == 1) {
                    $dataSaveDocumentPerson = array( 'eid'           => $eid,
                                                    'pid'           => $formData['dni'],
                                                    'document_type' => 'SOL',
                                                    'acumulado'     => 1 );
                    if ($personDocumentDb->_save($dataSaveDocumentPerson)) {
                      $result = array('success' => 1);
                    }
                }else{
                    $pkDocumentPerson = array(  'eid'           => $eid,
                                                'pid'           => $formData['dni'],
                                                'document_type' => 'SOL' );
                    $dataUpdateDocumentPerson = array('acumulado' => $formData['numero']);
                    if ($personDocumentDb->_update($dataUpdateDocumentPerson, $pkDocumentPerson)) {
                        $result = array('success' => 1);
                    }
                }
             }else{
                $result = array('success' => 2);
             }*/
        }else{
            $result = array('success' => 0);
        }
    }

   public function documentpreviewAction(){
      $this->_helper->layout()->disableLayout();

      $formData = $this->getRequest()->getPost();

      if ($formData['abstractDocument']) {
         $dataDocument['title']     = 'Solicitud N. '.$formData['dni'].'-'.$formData['numero'];
         $dataDocument['remitente'] = $formData['fullName'];
         $dataDocument['dni']       = $formData['dni'];
         $dataDocument['date']      = $formData['nameDate'];
         $dataDocument['detail']   = $formData['abstractDocument'];

         //Motivo
         switch ($formData['motivo']) {
            case 'openAct':
               $motivo = 'Re-apertura de Acta';
               break;
            case 'openAssistance':
               $motivo = 'Re-apertura de Asistencia';
               break;
            case 'openSyllabus':
               $motivo = 'Re-apertura de Sílabo';
               break;
            case 'openAcademicReport':
               $motivo = 'Re-apertura de Reporte Académico';
               break;
            case 'openTutorshipReport':
               $motivo = 'Re-apertura de Reporte de Tutoría';
               break;
            default:
               $motivo = 'Otros';
               break;
         }
         $dataDocument['motivo'] = $motivo;

         $this->view->dataDocument = $dataDocument;
      }else{
         print 0;
      }
   }
}      