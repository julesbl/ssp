<?php
/**
 *   Site by w34u
 *   https://www.w34u.com
 *   j.blundell@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:    Simple site protection
 *   Routine:    20200812160600_ChangeSessoionIdFieldLength.php
 *   Created:    12/08/2020
 *   Descrip:    Change the Session iid field length so it can handle ids longer than 32 chars.
 *
 *   Revision:    a
 *   Rev. Date    12/08/2020
 *   Descrip:    Created.
 */
class ChangeSessoionIdFieldLength extends Ruckusing_Migration_Base
{
	public function up()
	{
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->change_column($cfg->sessionTable, 'SessionId', 'string', ['limit' => 255]);
	}//up()

	public function down()
	{
		$cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->change_column($cfg->sessionTable, 'SessionId', 'string', ['limit' => 32]);
	}//down()
}

/**
 *   File name: 20200812160600_ChangeSessoionIdFieldLength.php
 *   Path: sspadmin/setup/migrations/ssp/20200812160600_ChangeSessoionIdFieldLength.php
 */