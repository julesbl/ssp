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
}