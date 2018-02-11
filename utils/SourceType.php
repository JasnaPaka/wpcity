<?php

class SourceType
{
	private $code;
	private $name;
	private $description;
	private $url;
	private $system;

	public function __construct($code, $name, $description, $url, $system = false)
	{
		$this->code = $code;
		$this->name = $name;
		$this->description = $description;
		$this->url = $url;
		$this->system = $system;
	}

	public function getCode() {
		return $this->code;
	}

	public function getName() {
		return $this->name;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getSystem() {
		return $this->system;
	}

}