<?php

class License
{

	private $id;
	private $name;
	private $short;
	private $url;
	private $priority;

	/**
	 * License constructor.
	 * @param $id
	 * @param $name
	 * @param $short
	 * @param $url
	 * @param $priority
	 */
	public function __construct($id, $name, $short, $url, $priority = 100)
	{
		$this->id = $id;
		$this->name = $name;
		$this->short = $short;
		$this->url = $url;
		$this->priority = $priority;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getShort()
	{
		return $this->short;
	}

	/**
	 * @param mixed $short
	 */
	public function setShort($short)
	{
		$this->short = $short;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return mixed
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param mixed $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

}