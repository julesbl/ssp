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
	
	// test tokens used to verify form submission
	public function testFormTokens(){
		$this->formId = 'form_id';
		$this->formToken = \w34u\ssp\SSP_Token($this->formId);
		$this->anotherFromId = 'anotherForm_id';
		$this->specify('One form pretending to be another', function(){
			$this->assertTrue(w34u\ssp\SSP_TokenCheck($this->formToken, $this->anotherFromId) !== true);
		});
		$this->specify('Form token valid', function(){
			$this->assertTrue(w34u\ssp\SSP_TokenCheck($this->formToken, $this->formId) === true);
		});
		$this->specify('Form token used and no longer available', function(){
			$this->assertTrue(w34u\ssp\SSP_TokenCheck($this->formToken, $this->formId) !== true);
		});
		$cfg = w34u\ssp\Configuration::getConfiguration();
		$original = $cfg->tokenClean;
		$cfg->tokenClean = 2;
		$this->formToken = \w34u\ssp\SSP_Token($this->formId);
		sleep(3);
		$this->specify('Form token cleaned out due to timeout', function(){
			$this->assertTrue(w34u\ssp\SSP_TokenCheck($this->formToken, $this->formId) !== true);
		});
		$cfg->tokenClean = $original;
	}
	
	// email function, uses mailcatcher
	/*
	public function testEmail(){
		$this->subject = "Test email";
		$this->targetEmail = 'test1@w34u.com';
		$this->targetName = 'Testing recipient';
		$this->fromName = 'Testy McTestFace';
		$this->fromEmail = 'test2@w34u.com';
		$this->textEmail = 'A test email, used for testing the ssp email function';
		$this->specify('Sending text email', function(){
			$result = \w34u\ssp\SSP_SendMail($this->fromName, $this->fromEmail, $this->targetName, $this->targetEmail, $this->subject, $this->textEmail);
			$this->assertTrue($result === true, 'Check email sent');
		});
	}
	 * 
	 */
	
	// response token routines
	public function testResponseTokens(){
		$this->responseId = 'responseId';
		$this->responseToken = \w34u\ssp\SSP_ResponseToken($this->responseId, 10);
		$this->specify('Response token is valid', function(){
			$this->assertTrue(strcmp(w34u\ssp\SSP_CheckResponseToken($this->responseToken), $this->responseId) === 0);
		});
		$this->specify('Response token has been used', function(){
			$this->assertTrue(w34u\ssp\SSP_CheckResponseToken($this->responseToken) === false);
		});
		$this->responseToken = \w34u\ssp\SSP_ResponseToken($this->responseId, 1);
		sleep(2);
		$this->specify('Response token has times out', function(){
			$this->assertTrue(w34u\ssp\SSP_CheckResponseToken($this->responseToken) === false);
		});
	}
}