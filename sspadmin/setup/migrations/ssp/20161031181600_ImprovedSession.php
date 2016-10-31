<?php

class ImprovedSession extends Ruckusing_Migration_Base
{
    public function up()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->change_column($cfg->sessionTable, 'SessionRandom', 'string', ['limit' => 255]);
    }//up()

    public function down()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->change_column($cfg->sessionTable, 'SessionRandom', 'integer', ['limit' => 11]);
    }//down()
}
