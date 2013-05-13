<?php

class ImageButton extends Button {
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ($Creator,$type,$label = DEFAULT_LABEL,$image_position = FLO_IMAGEBUTTON_POSITION,$image_url = NULL,$target = NULL) {
		parent::__construct ($Creator,$type,$label,$target);
		$this->image_position = $image_position;
		$this->image_url = $image_url;
		//DebugInfo ('. IMAGE BUTTON created');
	}
	
	static public function create () {
		// create ( Creator, type [, label [, image_position [, image_url [, target ]]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new ImageButton ($args[0],$args[1]);
			case 3: return new ImageButton ($args[0],$args[1],$args[2]);
			case 4: return new ImageButton ($args[0],$args[1],$args[2],$args[3]);
			case 4: return new ImageButton ($args[0],$args[1],$args[2],$args[3],$args[4]);
			case 4: return new ImageButton ($args[0],$args[1],$args[2],$args[3],$args[4],$args[5]);
			default: DebugWarning ('addImageButton ignored: invalid number of arguments');
		}
	}
	
	// HTML OUTPUT ---------------------------------------------------------------
	
	protected function ImageURL () {
		if (is_null($this->image_url)) {
			return FLO_IMAGEBUTTON_PATH.$this->type.'.'.FLO_IMAGEBUTTON_EXTENSION;
		}
		return $this->image_url;
	}
	
	public function HTMLContent () {
		$img = '<img src="'.$this->ImageURL().'" alt="'.$this->label.'">';
		switch ($this->image_position) {
			case IMAGE_ABOVE: return $img.'<br/>'.$this->label;
			case IMAGE_BELOW: return $this->label.'<br/>'.$img;
			case IMAGE_RIGHT: return $this->label.' '.$img;
			case IMAGE_LEFT : return $img.' '.$this->label;
			case IMAGE_ONLY : return $img;
			default:					return NULL;
		}
	}

}

?>
