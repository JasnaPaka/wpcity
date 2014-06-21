<?php
	abstract class JPMessage {
		
		CONST ERROR = "error";
		CONST WARN = "warn";
		CONST INFO = "info";
		
		private $type;
		private $message;
		
		function __construct($type, $message) {
			$this->type = $type;
			$this->message = $message;
		}
		
		public function getType() {
			return $this->type;	
		} 
		
		public function getMessage() {
			return $this->message;	
		}
	}
	
	class JPErrorMessage extends JPMessage {
		function __construct($message) {
			parent::__construct(JPMessage::ERROR, $message);
		}
	}
	
	class JPWarnMessage extends JPMessage {
		function __construct($message) {
			parent::__construct(JPMessage::WARN, $message);
		}
	}
	
	class JPInfoMessage extends JPMessage {
		function __construct($message) {
			parent::__construct(JPMessage::INFO, $message);
		}
	}
?>