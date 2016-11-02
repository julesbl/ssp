<?php

class ImprovedAdminIpLocking extends Ruckusing_Migration_Base
{
    public function up()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->change_column($cfg->userTable, 'UserIp', 'string', ['limit' => 255]);
    }//up()

    public function down()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->change_column($cfg->userTable, 'UserIp', 'string', ['limit' => 30]);
    }//down()
}
