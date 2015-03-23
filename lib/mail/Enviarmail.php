<?php 
/*
 * *
 * Clase que envia correos a los usuarios 
 * */
 
class Enviarmail {
	public function scorreo($lista,$body,$asunto){
        try{
        	//$lista = array('correo','apenom','escuela')
            // COnfiguracion del Correo Institucional
            $html = $this->html($body);
            $config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => 'informatica@undac.edu.pe', 'password' => '1nf0rm4t1c4000123');
            $smtpConnection = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);            
            if ($lista<>"" && $html<>"" && $asunto<>""){
                foreach ($lista as $usuario){
                    $correo = $usuario['correo'];   
                    if ($correo){
                        $datos = $usuario['apenom'];                        
                        $mail = new Zend_Mail("UTF-8");
                        $mail->setBodyHtml($html);
                        $mail->setFrom('informatica@undac.edu.pe', 'Oficina de Estadistica e Informatica');
                        $mail->addTo($correo, $datos);
                        $mail->setSubject($asunto);
                        $mail->send($smtpConnection);                        
                    }
                }
            }
        }  catch (Exception $ex){
            print "Error Enviar Correo:".$ex->getMessage();
        }
    }

	private function html($cuerpo){
		$html='
			<div style="width:100%;margin:10px;">
				<div style="height:80px;">
                    <table style="border-bottom:1px solid #AAA;" >
                        <tr>
                            <th style="width:1px"><img src="http://intranet.undac.edu.pe/img/undac_opt.png" bothrder="0" height="50" width="50"></th>
                            <th style="text-align: left">
                                <div style="color:#24348D;font-size:14px;">UNIVERSIDAD NACIONAL</div>
                                <div style="color:#2D6CA2;font-size:11px;">DANIEL ALCIDES CARRIÓN</div>
                                <div style="color:#333333;font-size:9px">La mas Alta del Mundo, con Excelencia Academica y<br>Responsabilidad social</div>
                            </th>
                        </tr>
                    </table>
			   	</div><br><br>'.
			   	$cuerpo.
				'
                <br><br>
                <div style="text-align:left;font-size:11px; width:37%; border-top:1px solid #BBB">
                    <div style="color:#9099a7; font-size:13px"><b>Sistema Academico e-Undac<b></div>
                    <div style="color:#9099a7">Universidad Nacional Daniel Alcides Carrión</div>
                    <div>www.undac.edu.pe / informatica@undac.edu.pe</div>
                    <div style="color:#9099a7">Teléfono: (51) 063 421365</div
                </div>
                <div style="clear:both"></div>
			</div>';
		return $html;
	}
	
}
?>