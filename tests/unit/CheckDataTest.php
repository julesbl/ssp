<?php


class CheckDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
	
	/**
	 * Check data for validity
	 * @var w34u\ssp\CheckData
	 */
	protected $checkData;

	protected function _before()
    {
		$this->checkData = new w34u\ssp\CheckData();
    }

    protected function _after()
    {
    }

    // tests
    public function testText(){
		$this->specify('Valid characters', function(){
			$this->assertTrue($this->checkData->check('text', 'asdvdf.gjkng123456@;[]') === 0, 'Valid text');
		});
    }
}