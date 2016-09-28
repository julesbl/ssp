<?php
namespace w34u\ssp;
class Initialise extends \Ruckusing_Migration_Base {
	
	/**
	 * SSP Configuration
	 * @var \w34u\ssp\Configuration 
	 */
	private $cfg;
	/**
	 * Database connection
	 * @var \w34u\ssp\SspDB
	 */
	private $db;


	public function __construct($ad) {
		parent::__construct($ad);
		$this->cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->db = \w34u\ssp\SspDb::getConnection();
	}

	public function up() {
		$values = array();
		
		$query = "CREATE TABLE `". $this->cfg->sessionTable. "` (
		  `SessionId` char(32) NOT NULL default '',
		  `UserId` char(32) NOT NULL default '',
		  `SessionTime` int(11) NOT NULL default '0',
		  `SessionName` varchar(30) NOT NULL default '',
		  `SessionIp` varchar(40) NOT NULL default '',
		  `SessionUserIp` varchar(40) NOT NULL default '',
		  `SessionCheckIp` tinyint(4) NOT NULL default '0',
		  `SessionRandom` int(11) NOT NULL default '0',
		  `SessionData` blob NOT NULL,
		  PRIMARY KEY  (`SessionId`),
		  KEY `SessionTime` (`SessionTime`)
		) CHARACTER SET ". $this->cfg->connectionEncoding. " COLLATE ". $this->cfg->tableCollation;
		$this->db->query($query, $values, "SSP Database configuration: Creating session table");

		$query = "CREATE TABLE `". $this->cfg->tokenTable. "` (
		  `token` char(32) NOT NULL default '',
		  `time` int(11) NOT NULL default '0',
		  `id` varchar(50) NOT NULL default '',
		  PRIMARY KEY  (`token`),
		  KEY `time` (`time`),
		  KEY `id` (`id`)
		) CHARACTER SET ". $this->cfg->connectionEncoding. " COLLATE ". $this->cfg->tableCollation;
		$this->db->query($query, $values, "SSP Database configuration: Creating token table");

		$query = "CREATE TABLE `". $this->cfg->userTable. "` (
		  `UserId` char(32) NOT NULL default '',
		  `UserEmail` varchar(255) NOT NULL default '',
		  `UserName` varchar(50) default NULL,
		  `UserPassword` varchar(255) NOT NULL default '',
		  `UserIp` varchar(30) NOT NULL default '',
		  `UserIpCheck` tinyint(4) NOT NULL default '0',
		  `UserAccess` varchar(20) NOT NULL default 'public',
		  `lang` varchar(10) NOT NULL default '',
		  `country` varchar(10) NOT NULL default '',
		  `UserDateLogon` int(11) NOT NULL default '0',
		  `UserDateLastLogon` int(11) NOT NULL default '0',
		  `UserDateCreated` int(11) NOT NULL default '0',
		  `UserDisabled` tinyint(4) NOT NULL default '0',
		  `UserPending` tinyint(4) NOT NULL default '0',
		  `UserAdminPending` tinyint(4) NOT NULL default '0',
		  `CreationFinished` tinyint(4) NOT NULL default '0',
		  `UserWaiting` tinyint(4) NOT NULL default '0',
		  `UserInvisible` tinyint(4) NOT NULL default '0',
		  PRIMARY KEY  (`UserId`),
		  KEY `UserEmail` (`UserEmail`),
		  UNIQUE KEY `UserName` (`UserName`),
		  KEY `UserPassword` (`UserPassword`),
		  KEY `UserDisabled` (`UserDisabled`,`UserPending`,`UserAdminPending`,`CreationFinished`,`UserWaiting`)
		) CHARACTER SET ". $this->cfg->connectionEncoding. " COLLATE ". $this->cfg->tableCollation;
		$this->db->query($query, $values, "SSP Database configuration: Creating login table");

		$query = "CREATE TABLE `". $this->cfg->userMiscTable. "` (
		  `UserId` char(32) NOT NULL default '',
		  `Title` varchar(15) NOT NULL default '',
		  `FirstName` varchar(20) NOT NULL default '',
		  `Initials` varchar(5) NOT NULL default '',
		  `FamilyName` varchar(30) NOT NULL default '',
		  `Address` varchar(255) NOT NULL default '',
		  `TownCity` varchar(30) NOT NULL default '',
		  `PostCode` varchar(10) NOT NULL default '',
		  `County` varchar(20) NOT NULL default '',
		  `Country` varchar(5) NOT NULL default '',
		  PRIMARY KEY  (`UserId`)
		) CHARACTER SET ". $this->cfg->connectionEncoding. " COLLATE ". $this->cfg->tableCollation;
		$this->db->query($query, $values, "SSP Database configuration: Creating user misc data table");

		$query = "CREATE TABLE `". $this->cfg->responseTable. "` (
		  `token` char(32) NOT NULL default '',
		  `time` int(11) NOT NULL default '0',
		  `UserId` char(32) NOT NULL default '',
		  PRIMARY KEY  (`token`),
		  KEY `time` (`time`)
		) CHARACTER SET ". $this->cfg->connectionEncoding. " COLLATE ". $this->cfg->tableCollation;
		$this->db->query($query, $values, "SSP Database configuration: Creating user misc data table");

		$query = "CREATE TABLE `". $this->cfg->tableRememberMe. "` (
		  `id` char(32) NOT NULL default '',
		  `user_id` char(32) NOT NULL default '',
		  `date_expires` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `date_expires` (`date_expires`)
		) CHARACTER SET ". $this->cfg->connectionEncoding. " COLLATE ". $this->cfg->tableCollation;
		$this->db->query($query, $values, "SSP Database configuration: Creating remember me table");
	}

//up()

	public function down() {
		$this->drop_table($this->cfg->sessionTable);
		$this->drop_table($this->cfg->tokenTable);
		$this->drop_table($this->cfg->userTable);
		$this->drop_table($this->cfg->userMiscTable);
		$this->drop_table($this->cfg->responseTable);
		$this->drop_table($this->cfg->tableRememberMe);
	}

//down()
}
