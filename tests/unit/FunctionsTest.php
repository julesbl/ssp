<?php


class FunctionsTest extends \Codeception\Test\Unit
{
	use \Codeception\Specify;
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // check unique id function
    public function testSSP_uniqueId()
    {
		$this->id = w34u\ssp\SSP_uniqueId();
		$this->specify('Check id is 32 characters', function(){
			$this->assertTrue(strlen($this->id) === 32);
		});
		$this->specify('Check id is hexadecimal', function(){
			$this->assertTrue(ctype_xdigit($this->id));
		});
		
    }
	// string encryption functions
	public function testStringEncryption(){
		$this->stringToBeEncrypted = "this string is top be encrypted #12345%!";
		$this->encryptedString = w34u\ssp\SSP_encrypt($this->stringToBeEncrypted);
		$this->decryptedString = \w34u\ssp\SSP_decrypt($this->encryptedString);
		$this->specify('Encrypted result is different from orriginal', function(){
			$this->assertTrue(strcmp($this->stringToBeEncrypted, $this->encryptedString) !== 0);
		});
		$this->specify('Decrypted the same as orriginal', function(){
			$this->assertTrue(strcmp($this->stringToBeEncrypted, $this->decryptedString) === 0);
		});
	}
	
	public function testPadIp(){
		$this->IPv4ToBePadded = "192.0.12.23";
		$this->IPv4Padded = "192.000.012.023";
		$this->IPv6NotToBePadded = "2001::192";
		$this->specify('Padding IPv4 address', function(){
			$this->assertTrue(strcmp($this->IPv4Padded, w34u\ssp\SSP_paddIp($this->IPv4ToBePadded)) === 0);
		});
		$this->specify('Not padding IPv6 address', function(){
			$this->assertTrue(strcmp($this->IPv6NotToBePadded, w34u\ssp\SSP_paddIp($this->IPv6NotToBePadded)) === 0);
		});
	}
	
	public function testTrimIp(){
		$cfg = w34u\ssp\Configuration::getConfiguration();
		$cfg->checkIpAccuracy = 3;
		$this->IPv4ToBeTrimmed = "192.0.12.23";
		$this->IPv4Trimmed = "192.000.012";
		$this->specify('Trimming IPv4 address '.$this->IPv4ToBeTrimmed. ' to '. w34u\ssp\SSP_trimIp($this->IPv4ToBeTrimmed). ' == '. $this->IPv4Trimmed , function(){
			$this->assertTrue(strcmp($this->IPv4Trimmed, w34u\ssp\SSP_trimIp($this->IPv4ToBeTrimmed)) === 0);
		});
	}
	
	public function testFormTokens(){
		$this->formId = 'form_id';
		$this->formToken = \w34u\ssp\SSP_Token($this->formId);
		$this->specify('Form token valid', function(){
			$this->assertTrue(w34u\ssp\SSP_TokenCheck($this->formToken, $this->formId) === true);
		});
		$this->specify('Form token used and no longer available', function(){
			$this->assertTrue(w34u\ssp\SSP_TokenCheck($this->formToken, $this->formId) !== true);
		});
	}
}