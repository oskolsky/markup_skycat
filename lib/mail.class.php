<?php
/*
-------------------------------------------------
php_libmail v 1.6.0 (09.12.2011) webi.ru
������������� PHP ����� ��� �������� �����
����� ���������� ����� ����� smtp(��������� ������), ���� ����� ����������� ������� mail()
http://webi.ru/webi_files/php_libmail.html
adm@webi.ru
-----------------------
���������� ����� Leo West - lwest@free.fr
��������� webi.ru
�� ���� �������� ������ �� adm@webi.ru
________________________________________________

��� ������:

������ 1.6.0 (09.12.2011)
��������� ����������� ��������� ����� � �������� ������ 
��������� � From, To � ReplyTo 
��� ����������� ����� ����������� ';' ��������
$m->To( "������;adm@webi.ru" ); 

-------------------------------------------
������ 1.5.1 (07.02.2011)
������������ ������� �������� email ������� �� ���������� ��� php 5.2 � ����.

-------------------------------------------
������ 1.5 (28.02.2010)
��������� ��������� �������� ����� � ������� SMTP

-------------------------------------------
������ 1.4 (24.02.2010)

��������� � ������� ���������
��������� ������ ����� ��������� �� � ���� ������ ��� ���� ������, � ��� ������������� ������ �������� $m= new Mail('windows-1251');
���� �� ��������� ���������, �� ��������� ����� windows-1251
--
�������� ������������ ������.
������� ����� ��������, ��� �����.
������ ���������� ���� ��� ����� ���� ������ ���. ��� �������, ����� ������������ ��������� ����� ����������� ����� upload, ��� ��� �� ����� �� ������������ ��������.
������ 	$m->Attach( "/toto.gif", "asd.gif" "image/gif" )
���� �� ��������� ����� ���, ������� ��� �� ���� �������������� �����
--
��������� ����������� ���������� ������ � ������� html
_______________________________________________


������

include "libmail.php";

$m= new Mail('windows-1251');  // ����� ����� ������� ���������, ����� ������ �� ��������� ($m= new Mail;)
$m->From( "������;asd@asd.com" ); // �� ���� ����� ������������ ���, ���������� ������ � �������
$m->ReplyTo( '������ �������;replay@bk.ru' ); // ���� ��������, ���� ����� ������� ���
$m->To( "kuda@asd.ru" );   // ����, � ���� ���� ��� �� ��������� ��������� ���
$m->Subject( "���� ���������" );
$m->Body("���������. ����� ������");
$m->Cc( "kopiya@asd.ru");  // ���� ��������� ����� ������
$m->Bcc( "skritaya_kopiya@asd.ru"); // ���� ��������� ������� �����
$m->Priority(4) ;	// ��������� ����������
$m->Attach( "/toto.gif", "", "image/gif" ) ;	// ������������� ���� ���� image/gif. ���� ����� ��������� �� �����������
$m->smtp_on("smtp.asd.com","login","passw", 25, 10); // ��������� �� ������� �������� ������ ����� smtp
$m->Send();	// ��������
echo "������ ����������, ��� �������� ����� ������:<br><pre>", $m->Get(), "</pre>";

��������� ���������� ������� �� ����� http://webi.ru/webi_files/php_libmail.html

*/


class Mail
{
	/*     ����������� ���������� ���� ����� VAR, ��� ����������� ������ � php ������ ������
	������� ������� ���� ���������
	@var	array
	*/
	var $sendto = array();
	/*
	@var	array
	*/
	var  $acc = array();
	/*
	@var	array
	*/
	var $abcc = array();
	/*
	������������� �����
	@var array
	*/
	var $aattach = array();
	/*
	������ ����������
	@var array
	*/
	var $xheaders = array();
	/*
	����������
	@var array
	*/
	var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	/*
	��������� �� ���������
	@var string
	*/
	var  $charset = "windows-1251";
	var  $ctencoding = "8bit";
	var  $receipt = 0;
	var  $text_html="text/plain"; // ������ ������. �� ��������� ���������
	var  $smtp_on=false;    // �������� ����� smtp. �� ��������� ���������
	var  $names_email = array(); // ����� ��� email �������, ����� ������ ��� ("������" <asd@wer.re>)

	/*
	����������� ���� �� ������� �������� ��� ������������� �� ������� �������� php
	����� �����������.
	�������� �������� ��������� ������
	������� ��������� webi.ru
	*/

	function Mail($charset="")
	{
		$this->autoCheck( true );
		$this->boundary= "--" . md5( uniqid("myboundary") );


		if( $charset != "" ) {
			$this->charset = strtolower($charset);
			if( $this->charset == "us-ascii" )
			$this->ctencoding = "7bit";
		}
	}


	/*

	��������� ���������� �������� ���������� email
	������: autoCheck( true ) �������� �������
	�� ��������� �������� ��������


	*/
	function autoCheck( $bool )
	{
		if( $bool )
		$this->checkAddress = true;
		else
		$this->checkAddress = false;
	}


	/*

	���� ������
	������� ��������� ����������� �� ��������� ��������

	*/
	function Subject( $subject )
	{

		$this->xheaders['Subject'] ="=?".$this->charset."?Q?".str_replace("+","_",str_replace("%","=",urlencode(strtr( $subject, "\r\n" , "  " ))))."?=";

	}


	/*

	�� ����
	*/

	function From( $from )
	{

		if( ! is_string($from) ) {
			echo "������, From ������ ���� �������";
			exit;
		}
		$temp_mass=explode(';',$from); // ��������� �� ����������� ��� ��������� �����
		if(count($temp_mass)==2) // ���� ������� ������� �� ��� ��������
		{
			$this->names_email['from']=$temp_mass[0]; // ��� ������ �����
			$this->xheaders['From'] = $temp_mass[1]; // ����� ������ �����
		}
		else // � ���� ��� �� ����������
		{
			$this->names_email['from']='';
			$this->xheaders['From'] = $from;
		}
	}

	/*
	�� ����� ����� ��������

	*/
	function ReplyTo( $address )
	{

		if( ! is_string($address) )
		return false;

		$temp_mass=explode(';',$address); // ��������� �� ����������� ��� ��������� �����

		if(count($temp_mass)==2) // ���� ������� ������� �� ��� ��������
		{
			$this->names_email['Reply-To']=$temp_mass[0]; // ��� ������ �����
			$this->xheaders['Reply-To'] = $temp_mass[1]; // ����� ������ �����
		}
		else // � ���� ��� �� ����������
		{
			$this->names_email['Reply-To']='';
			$this->xheaders['Reply-To'] = $address;
		}


	}


	/*
	���������� ��������� ��� ��������� ����������� � ���������. �������� ����� ������� �� "From" (��� �� "ReplyTo" ���� ������)

	*/

	function Receipt()
	{
		$this->receipt = 1;
	}


	/*
	set the mail recipient
	@param string $to email address, accept both a single address or an array of addresses

	*/

	function To( $to )
	{

		// ���� ��� ������
		if( is_array( $to ) )
		{
			foreach ($to as $key => $value) // ���������� ������ � ��������� � ������ ��� �������� ����� smtp
			{

				$temp_mass=explode(';',$value); // ��������� �� ����������� ��� ��������� �����

				if(count($temp_mass)==2) // ���� ������� ������� �� ��� ��������
				{					
					$this->smtpsendto[$temp_mass[1]] = $temp_mass[1]; // ����� � �������� ����������, ����� ��������� ����� �������
					$this->names_email['To'][$temp_mass[1]]=$temp_mass[0]; // ��� ������ �����
					$this->sendto[]= $temp_mass[1];
				}
				else // � ���� ��� �� ����������
				{
					$this->smtpsendto[$value] = $value; // ����� � �������� ����������, ����� ��������� ����� �������
					$this->names_email['To'][$value]=''; // ��� ������ �����
					$this->sendto[]= $value;
				}
			}
		}
		else
		{
				$temp_mass=explode(';',$to); // ��������� �� ����������� ��� ��������� �����

				if(count($temp_mass)==2) // ���� ������� ������� �� ��� ��������
				{
					
					$this->sendto[] = $temp_mass[1];
			        $this->smtpsendto[$temp_mass[1]] = $temp_mass[1]; // ����� � �������� ����������, ����� ��������� ����� �������
					$this->names_email['To'][$temp_mass[1]]=$temp_mass[0]; // ��� ������ �����
				}
				else // � ���� ��� �� ����������
				{
					
					$this->sendto[] = $to;
			        $this->smtpsendto[$to] = $to; // ����� � �������� ����������, ����� ��������� ����� �������

					$this->names_email['To'][$to]=''; // ��� ������ �����
				}
			
			
			
		}

		if( $this->checkAddress == true )
		$this->CheckAdresses( $this->sendto );

	}


	/*		Cc()
	*		��������� ���������� CC ( �������� �����, ��� ���������� ����� ������ ���� ���� ����� )
	*		$cc : email address(es), accept both array and string
	*/

	function Cc( $cc )
	{
		if( is_array($cc) )
		{
			$this->acc= $cc;

			foreach ($cc as $key => $value) // ���������� ������ � ��������� � ������ ��� �������� ����� smtp
			{
				$this->smtpsendto[$value] = $value; // ����� � �������� ����������, ����� ��������� ����� �������
			}
		}
		else
		{
			$this->acc[]= $cc;
			$this->smtpsendto[$cc] = $cc; // ����� � �������� ����������, ����� ��������� ����� �������
		}

		if( $this->checkAddress == true )
		$this->CheckAdresses( $this->acc );

	}



	/*		Bcc()
	*		������� �����. �� ����� �������� ��������� ���� ���� ������
	*		$bcc : email address(es), accept both array and string
	*/

	function Bcc( $bcc )
	{
		if( is_array($bcc) )
		{
			$this->abcc = $bcc;
			foreach ($bcc as $key => $value) // ���������� ������ � ��������� � ������ ��� �������� ����� smtp
			{
				$this->smtpsendto[$value] = $value; // ����� � �������� ����������, ����� ��������� ����� �������
			}
		}
		else
		{
			$this->abcc[]= $bcc;
			$this->smtpsendto[$bcc] = $bcc; // ����� � �������� ����������, ����� ��������� ����� �������
		}

		if( $this->checkAddress == true )
		$this->CheckAdresses( $this->abcc );
	}


	/*		Body( text [ text_html ] )
	*		$text_html � ����� ������� ����� ������, � ������ ��� html. �� ��������� ����� �����
	*/
	function Body( $body, $text_html="" )
	{
		$this->body = $body;

		if( $text_html == "html" ) $this->text_html = "text/html";

	}


	/*		Organization( $org )
	*		set the Organization header
	*/

	function Organization( $org )
	{
		if( trim( $org != "" )  )
		$this->xheaders['Organization'] = $org;
	}


	/*		Priority( $priority )
	*		set the mail priority
	*		$priority : integer taken between 1 (highest) and 5 ( lowest )
	*		ex: $mail->Priority(1) ; => Highest
	*/

	function Priority( $priority )
	{
		if( ! intval( $priority ) )
		return false;

		if( ! isset( $this->priorities[$priority-1]) )
		return false;

		$this->xheaders["X-Priority"] = $this->priorities[$priority-1];

		return true;

	}


	/*
	������������� �����

	@param string $filename : ���� � �����, ������� ���� ���������
	@param string $webi_filename : �������� ��� �����. ���� ����� ����������� ���� ���������, �� ��� ��� ����� ���� ����� �����..
	@param string $filetype : MIME-��� �����. �� ��������� 'application/x-unknown-content-type'
	@param string $disposition : ���������� ��������� ������� ��� ���������� ������������� ���� ("inline") ��� ����� ������ ��� ("attachment") ��� ������������� ����
	*/

	function Attach( $filename, $webi_filename="", $filetype = "", $disposition = "inline" )
	{
		// TODO : ���� ���� ����� �� ������, ������ ����������� ���
		if( $filetype == "" )
		$filetype = "application/x-unknown-content-type";

		$this->aattach[] = $filename;
		$this->webi_filename[] = $webi_filename;
		$this->actype[] = $filetype;
		$this->adispo[] = $disposition;
	}

	/*

	�������� ������


	*/
	function BuildMail()
	{

		$this->headers = "";

		// �������� ��������� TO.
		// ���������� ���� � �������
        foreach ($this->sendto as $key => $value) 
        {
        	
        	if( strlen($this->names_email['To'][$value])) $temp_mass[]="=?".$this->charset."?Q?".str_replace("+","_",str_replace("%","=",urlencode(strtr( $this->names_email['To'][$value], "\r\n" , "  " ))))."?= <".$value.">";
        	else $temp_mass[]=$value;
        }
        
		$this->xheaders['To'] = implode( ", ", $temp_mass ); // ���� ��������� ����� �� ����� ��� �������� ����� mail()

		if( count($this->acc) > 0 )
		$this->xheaders['CC'] = implode( ", ", $this->acc );

		if( count($this->abcc) > 0 )
		$this->xheaders['BCC'] = implode( ", ", $this->abcc );  // ���� ��������� ����� �� ����� ��� �������� ����� smtp


		if( $this->receipt ) {
			if( isset($this->xheaders["Reply-To"] ) )
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
			else
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
		}

		if( $this->charset != "" ) {
			$this->xheaders["Mime-Version"] = "1.0";
			$this->xheaders["Content-Type"] = $this->text_html."; charset=$this->charset";
			$this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
		}

		$this->xheaders["X-Mailer"] = "Php_libMail_v_1.5(webi.ru)";

		// ���������� �����
		if( count( $this->aattach ) > 0 ) {
			$this->_build_attachement();
		} else {
			$this->fullBody = $this->body;
		}



		// �������� ���������� ���� �������� ���� ����� smtp
		if($this->smtp_on)
		{

			// ��������� (FROM - �� ����) �� ����� � �����. ����� ����������� � ���������
			$user_domen=explode('@',$this->xheaders['From']);

			$this->headers = "Date: ".date("D, j M Y G:i:s")." +0700\r\n";
			$this->headers .= "Message-ID: <".rand().".".date("YmjHis")."@".$user_domen[1].">\r\n";


			reset($this->xheaders);
			while( list( $hdr,$value ) = each( $this->xheaders )  ) {
				if( $hdr == "From" and strlen($this->names_email['from'])) $this->headers .= $hdr.": =?".$this->charset."?Q?".str_replace("+","_",str_replace("%","=",urlencode(strtr( $this->names_email['from'], "\r\n" , "  " ))))."?= <".$value.">\r\n";
				elseif( $hdr == "Reply-To" and strlen($this->names_email['Reply-To'])) $this->headers .= $hdr.": =?".$this->charset."?Q?".str_replace("+","_",str_replace("%","=",urlencode(strtr( $this->names_email['Reply-To'], "\r\n" , "  " ))))."?= <".$value.">\r\n";
				elseif( $hdr != "BCC") $this->headers .= $hdr.": ".$value."\r\n"; // ���������� ��������� ��� �������� ������� �����

			}



		}
		// �������� �����������, ���� �������� ���� ����� mail()
		else
		{
			reset($this->xheaders);
			while( list( $hdr,$value ) = each( $this->xheaders )  ) {
				if( $hdr == "From" and strlen($this->names_email['from'])) $this->headers .= $hdr.": =?".$this->charset."?Q?".str_replace("+","_",str_replace("%","=",urlencode(strtr( $this->names_email['from'], "\r\n" , "  " ))))."?= <".$value.">\r\n";
				elseif( $hdr == "Reply-To" and strlen($this->names_email['Reply-To'])) $this->headers .= $hdr.": =?".$this->charset."?Q?".str_replace("+","_",str_replace("%","=",urlencode(strtr( $this->names_email['Reply-To'], "\r\n" , "  " ))))."?= <".$value.">\r\n";
				elseif( $hdr != "Subject" and $hdr != "To") $this->headers .= "$hdr: $value\n"; // ���������� ��������� ���� � ����... ��� ��������� ����
			}
		}




	}

	// ��������� �������� ����� smtp ��������� ������
	// ����� ������� ���� ������� �������� ����� smtp ��������
	// ��� �������� ����� ���������� ���������� ������ ����� ��������� � ����������� "ssl://" �������� ��� "ssl://smtp.gmail.com"
	function smtp_on($smtp_serv, $login, $pass, $port=25,$timeout=5)
	{
		$this->smtp_on=true; // �������� �������� ����� smtp

		$this->smtp_serv=$smtp_serv;
		$this->smtp_login=$login;
		$this->smtp_pass=$pass;
		$this->smtp_port=$port;
		$this->smtp_timeout=$timeout;
	}

	function get_data($smtp_conn)
	{
		$data="";
		while($str = fgets($smtp_conn,515))
		{
			$data .= $str;
			if(substr($str,3,1) == " ") { break; }
		}
		return $data;
	}

	/*
	�������� ������

	*/
	function Send()
	{
		$this->BuildMail();
		$this->strTo = implode( ", ", $this->sendto );

		// ���� �������� ��� ������������� smtp
		if(!$this->smtp_on)
		{
			$res = @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );
                        if (!$res) return false;
                        else return true;
		}
		else // ���� ����� smtp
		{

			if (!$this->smtp_serv OR !$this->smtp_login OR !$this->smtp_pass OR !$this->smtp_port) return false; // ���� ��� ���� �� ������ �� �������� ������ ��� ��������, ������� � �������



			// ��������� (FROM - �� ����) �� ����� � �����. ���� ����������� � ���������� � ������
			$user_domen=explode('@',$this->xheaders['From']);


			$this->smtp_log='';
			$smtp_conn = fsockopen($this->smtp_serv, $this->smtp_port, $errno, $errstr, $this->smtp_timeout);
			if(!$smtp_conn) {$this->smtp_log .= "���������� � �������� �� ������\n\n"; fclose($smtp_conn); return false; }

			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";

			fputs($smtp_conn,"EHLO ".$user_domen[0]."\r\n");
			$this->smtp_log .= "�: EHLO ".$user_domen[0]."\n";
			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";
			$code = substr($data,0,3); // �������� ��� ������

			if($code != 250) {$this->smtp_log .= "������ ���������� EHLO \n"; fclose($smtp_conn); return false; }

			fputs($smtp_conn,"AUTH LOGIN\r\n");
			$this->smtp_log .= "�: AUTH LOGIN\n";
			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";
			$code = substr($data,0,3);

			if($code != 334) {$this->smtp_log .= "������ �� �������� ������ ����������� \n"; fclose($smtp_conn); return false;}

			fputs($smtp_conn,base64_encode($this->smtp_login)."\r\n");
			$this->smtp_log .= "�: ".base64_encode($this->smtp_login)."\n";
			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";

			$code = substr($data,0,3);
			if($code != 334) {$this->smtp_log .= "������ ������� � ������ �����\n"; fclose($smtp_conn); return  false;}


			fputs($smtp_conn,base64_encode($this->smtp_pass)."\r\n");
			//$this->smtp_log .="�: ". base64_encode($this->smtp_pass)."\n"; // ��� ������ ����������� ����� ����� � �����
			$this->smtp_log .="�: parol_skryt\n"; // � ��� ������ ����� � �����
                        $this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";

			$code = substr($data,0,3);
			if($code != 235) {$this->smtp_log .= "�� ���������� ������\n"; fclose($smtp_conn); return  false;}

			fputs($smtp_conn,"MAIL FROM:<".$this->xheaders['From']."> SIZE=".strlen($this->headers."\r\n".$this->fullBody)."\r\n");
			$this->smtp_log .= "�: MAIL FROM:<".$this->xheaders['From']."> SIZE=".strlen($this->headers."\r\n".$this->fullBody)."\n";
			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";

			$code = substr($data,0,3);
			if($code != 250) {$this->smtp_log .= "������ ������� � ������� MAIL FROM\n"; fclose($smtp_conn); return  false;}



			foreach ($this->smtpsendto as $keywebi => $valuewebi)
			{
				fputs($smtp_conn,"RCPT TO:<".$valuewebi.">\r\n");
				$this->smtp_log .= "�: RCPT TO:<".$valuewebi.">\n";
				$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";
				$code = substr($data,0,3);
				if($code != 250 AND $code != 251) {$this->smtp_log .= "������ �� ������ ������� RCPT TO\n"; fclose($smtp_conn); return  false;}
			}




			fputs($smtp_conn,"DATA\r\n");
			$this->smtp_log .="�: DATA\n";
			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";

			$code = substr($data,0,3);
			if($code != 354) {$this->smtp_log .= "������ �� ������ DATA\n"; fclose($smtp_conn); return  false;}

			fputs($smtp_conn,$this->headers."\r\n".$this->fullBody."\r\n.\r\n");
			$this->smtp_log .= "�: ".$this->headers."\r\n".$this->fullBody."\r\n.\r\n";

			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";

			$code = substr($data,0,3);
			if($code != 250) {$this->smtp_log .= "������ �������� ������\n"; fclose($smtp_conn); return  false;}

			fputs($smtp_conn,"QUIT\r\n");
			$this->smtp_log .="QUIT\r\n";
			$this->smtp_log .= $data = $this->get_data($smtp_conn)."\n";
			fclose($smtp_conn);
                        return true;
		}

	}



	/*
	*		���������� ��� ���� ����������
	*
	*/

	function Get()
	{
		if(isset($this->smtp_log))
		{
			if ($this->smtp_log)
			{
				return $this->smtp_log; // ���� ���� ��� �������� smtp ������� ���
			}
		}

		$this->BuildMail();
		$mail = $this->headers . "\n\n";
		$mail .= $this->fullBody;
		return $mail;
	}


	/*
	�������� ����
	���������� true ��� false
	*/

	function ValidEmail($address)
	{

		// ���� ���������� ����������� ������� ���������� ������, �� ��������� ����� ���� ��������. ��������� � php 5.2
		if (function_exists('filter_list'))
		{
			$valid_email = filter_var($address, FILTER_VALIDATE_EMAIL);
			if ($valid_email !== false) return true;
			else return false;
		}
		else // � ���� php ��� ������ ������, �� �������� ���������� ������ ������ ��������
		{
			if( ereg( ".*<(.+)>", $address, $regs ) ) {
				$address = $regs[1];
			}
			if(ereg( "^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$address) )
			return true;
			else
			return false;
		}
	}


	/*

	�������� ������� �������


	*/

	function CheckAdresses( $aad )
	{
		for($i=0;$i< count( $aad); $i++ ) {
			if( ! $this->ValidEmail( $aad[$i]) ) {
				echo "������ : �� ������ email ".$aad[$i];
				exit;
			}
		}
	}


	/*
	������ ������ ��� ��������
	*/

	function _build_attachement()
	{

		$this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";

		$this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
		$this->fullBody .= "Content-Type: ".$this->text_html."; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";

		$sep= chr(13) . chr(10);

		$ata= array();
		$k=0;

		// ���������� �����
		for( $i=0; $i < count( $this->aattach); $i++ ) {

			$filename = $this->aattach[$i];

			$webi_filename =$this->webi_filename[$i]; // ��� �����, ������� ����� ��������� � �����, � ����� ������ ��� �����
			if(strlen($webi_filename)) $basename=basename($webi_filename); // ���� ���� ������ ��� �����, �� ��� ����� �����
			else $basename = basename($filename); // � ���� ��� ������� ����� �����, �� ��� ����� ��������� �� ������ ������������ �����

			$ctype = $this->actype[$i];	// content-type
			$disposition = $this->adispo[$i];

			if( ! file_exists( $filename) ) {
				echo "������ ������������ ����� : ���� $filename �� ����������"; exit;
			}
			$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
			$ata[$k++] = $subhdr;
			// non encoded line length
			$linesz= filesize( $filename)+1;
			$fp= fopen( $filename, 'r' );
			$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
			fclose($fp);
		}
		$this->fullBody .= implode($sep, $ata);
	}


} // class Mail


?>
