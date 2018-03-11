<?php

interface IdentifierAble
{

	/**
	 * Pro aktuální objekt vrátí jeho identifikátor
	 *
	 * @return int číslo identifikátoru nebo -1, pokud jej nebylo možné získat
	 */
	public function getIdentifier():int;
}