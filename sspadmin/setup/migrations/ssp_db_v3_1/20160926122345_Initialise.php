<?php

class Initialise extends Ruckusing_Migration_Base {
	
	/**
	 * SSP Configuration
	 * @var \w34u\ssp\Configuration 
	 */
	private $cfg;
	
	public function __construct($ad) {
		parent::__construct($ad);
		$this->cfg = new \w34u\ssp\Configuration();
	}

	public function up() {
		
	}

//up()

	public function down() {
		
	}

//down()
}
