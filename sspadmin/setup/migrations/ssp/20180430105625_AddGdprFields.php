<?php

class AddGdprFields extends Ruckusing_Migration_Base
{
    public function up()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->add_column($cfg->userMiscTable, 'tandcs', 'tinyinteger', ['limit' => 1, 'default' => 0]);
		$this->add_column($cfg->userMiscTable, 'privacy_policy', 'tinyinteger', ['limit' => 1, 'default' => 0]);
		$this->add_column($cfg->userMiscTable, 'contact_them', 'tinyinteger', ['limit' => 1, 'default' => 0]);
    }//up()

    public function down()
    {
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->remove_column($cfg->userMiscTable, 'tandcs');
		$this->remove_column($cfg->userMiscTable, 'privacy_policy');
		$this->remove_column($cfg->userMiscTable, 'contact_them');
    }//down()
}
