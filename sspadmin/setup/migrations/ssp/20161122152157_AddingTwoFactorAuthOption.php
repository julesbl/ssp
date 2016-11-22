<?php

class AddingTwoFactorAuthOption extends Ruckusing_Migration_Base
{
    public function up()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->add_column($cfg->userTable, 'use_two_factor_auth', 'tinyinteger', ['limit' => 1, 'default' => 0]);
    }//up()

    public function down()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->remove_column($cfg->userTable, 'use_two_factor_auth');
    }//down()
}
