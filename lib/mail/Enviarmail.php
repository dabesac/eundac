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
			<div style="width:500px;margin:10px;">
				<div style="height:80px;">
			    	<img src="http://intranet.undac.edu.pe/img/header_mail.png">
			   	</div>'.
			   	$cuerpo.
				'<div style="height:40px;">
		    		<img src="http://intranet.undac.edu.pe/img/footer_mail.png">
		  		</div>
		    	<div style="clear:both"></div>
			</div>';
		return $html;
	}
	
}
?>