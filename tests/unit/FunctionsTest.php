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

    // tests
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
}