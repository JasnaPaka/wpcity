<?php

include_once $ROOT."utils/Licenses.php";

use PHPUnit\Framework\TestCase;


class LicensesTest extends TestCase
{

	public function testNotDuplicateId() {
		$tool = new Licenses();
		$licenses = $tool->getLicenses();

		$ids = array();
		$duplicate = false;
		foreach ($licenses as $license) {
			foreach ($ids as $id) {
				if ($id === $license->getId()) {
					$duplicate = true;
				}
			}
		}

		$this->assertFalse($duplicate);
	}

}