<?php

define('SENDMAIL_WRAP',70);

class Action_Sendmail extends Action {
	
	protected $querystring;
	
	protected function __construct () {
		$args = func_get_args();
		$this->Creator = $args[0];
		$this->recipient_email = $args[1];
		$this->subject = $args[2];
		$this->content = $args[3];
		$this->from_email = $args[4];
		$this->from_name = $args[5];
		$this->from_sender = $args[6];
		$this->additional_headers = 'MIME-Version: 1.0'."\r\n";
		$this->additional_headers .= 'Content-type: text/plain; charset="UTF-8"'."\r\n";
		$this->additional_parameters = NULL;
		$this->Creator->debuglog->Write(DEBUG_INFO,'. SENDMAIL ACTION created');
	}
	
	static public function create () {
		// create ( Creator , recipient_email , subject , content , from_email , from_name , from_sender )
		$args = func_get_args();
		switch (func_num_args()) {
			case 5: return new Action_Sendmail ($args[0],$args[1],$args[2],$args[3],$args[4],NULL,NULL);
			case 6: return new Action_Sendmail ($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],NULL);
			case 7: return new Action_Sendmail ($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],$args[6]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. SENDMAIL ACTION - invalid number of arguments');
		}
	}
	
	function onSubmit () {
		$this->Creator->debuglog->Write(DEBUG_INFO,'SENDMAIL ACTION - composing mail');
		// if required: wrap content
		if (SENDMAIL_WRAP > 0) $this->content = wordwrap($this->content,SENDMAIL_WRAP);
		// if required: get field values
		// TODO: this should also be implemented for the other fields
		$this->recipient_email = preg_replace('/(\{(\w*)\})/e',"\$this->Creator->Fields[$2]->user_value",$this->recipient_email);
		$this->subject = preg_replace('/(\{(\w*)\})/e',"\$this->Creator->Fields[$2]->user_value",$this->subject);
		$this->content = preg_replace('/(\{(\w*)\})/e',"\$this->Creator->Fields[$2]->user_value",$this->content);
		// if required: format name
		if ($this->from_name)
			$this->additional_headers .= 'From: "'.$this->from_name.'" <'.$this->from_email.">\r\n";
		else
			$this->additional_headers .= 'From: <'.$this->from_email.">\r\n";
		// the following parameter depends on host
		// TODO: this should be made a public setting
		//$this->additional_parameters = '-f '.$this->from_email;
		// now go
		if(empty($this->additional_parameters)) {
			if (mail($this->recipient_email, $this->subject, $this->content, $this->additional_headers)) {
				$this->Creator->debuglog->Write(DEBUG_INFO,'. SENDMAIL ACTION - mail sent');
			} else {
				$this->Creator->debuglog->Write(DEBUG_WARNING,'. SENDMAIL ACTION - error on sending mail');
			}
		} else {
			if (mail($this->recipient_email, $this->subject, $this->content, $this->additional_headers, $this->additional_parameters)) {
				$this->Creator->debuglog->Write(DEBUG_INFO,'. SENDMAIL ACTION - mail sent');
			} else {
				$this->Creator->debuglog->Write(DEBUG_WARNING,'. SENDMAIL ACTION - error on sending mail');
			}
		}
	}
}

?>
