<?php
namespace NCATesting\service;
use App\Service\SpamProtection;
use Codeception\Example;
use NCATesting\UnitTester;
use Codeception\Stub;

class spamProtectionCest
{
    private $fixture;
    private $I;

    public function _before(UnitTester $I)
    {
        $this->I = $I;
        $this->fixture = new SpamProtection();
    }

    // tests
    public function validateUserInputsReturnEmptyArrayOnValidData(UnitTester $I)
    {
        $this->fixture = Stub::make(
            $this->fixture,
            [
                'validateName' => true,
                'validateEmail' => true,
                'validateMessage' => true,
                'validateIp' => true
            ]
        );

        $data = [
            'name' => '',
            'email' => '',
            'message' => '',
            'ip' => ''
        ];

        $I->assertEmpty($this->fixture->validateUserInputs($data));
    }

    public function validateNameEmptyIsTrue(UnitTester $I)
    {
        $methodReturn = $I->getMethodReturn($this->fixture,'validateName', '');
        $I->assertTrue($methodReturn);
    }

    public function validateNameIsSpam(UnitTester $I)
    {
        $methodReturn = $I->getMethodReturn($this->fixture,'validateName', 'viagra');
        $I->assertFalse($methodReturn);
    }

    /**
     * @dataProvider invalidIpsProvider
     */
    public function validateIpWithInvalidIpsReturnFalse(UnitTester $I, Example $data)
    {
        $ip = $data[0];
        $methodReturn = $I->getMethodReturn($this->fixture,'validateIp', $ip);
        $I->assertFalse($methodReturn, 'IP: ' . $ip);
    }
    /**
     * @return array
     */
    protected function invalidIpsProvider()
    {
        return [
            [123],
            ['192.168.0.1']
        ];
    }

    /**
     * @dataProvider validIpsProvider
     */
    public function validateIpWithValidIpsReturnFalse(UnitTester $I, Example $data)
    {
        $this->fixture = Stub::make(
            $this->fixture,
            [
                'isIpFromDe' => true
            ]
        );

        $ip = $data[0];
        $methodReturn = $I->getMethodReturn($this->fixture,'validateIp', $ip);
        $I->assertTrue($methodReturn, 'IP: ' . $ip);
    }

    /**
     * @return array
     */
    protected function validIpsProvider() // alternatively, if you want the function to be public, be sure to prefix it with `_`
    {
        return [
            ['176.95.142.6'] // DE
        ];
    }

    public function validateEmailEmptyStringReturnFalse(UnitTester $I)
    {
        $methodReturn = $I->getMethodReturn($this->fixture,'validateEmail', '');
        $I->assertFalse($methodReturn);
    }

    public function validateEmailNotValidEmailReturnFalse(UnitTester $I)
    {
        $methodReturn = $I->getMethodReturn($this->fixture,'validateEmail', 'testify');
        $I->assertFalse($methodReturn);
    }

    public function validateEmailValidEmailReturnTrue(UnitTester $I)
    {
        $methodReturn = $I->getMethodReturn($this->fixture,'validateEmail', 'test@testify.com');
        $I->assertTrue($methodReturn);
    }

    public function validateEmailSpamReturnFalse(UnitTester $I)
    {
        $methodReturn = $I->getMethodReturn($this->fixture,'validateEmail', 'viagra@testify.com');
        $I->assertFalse($methodReturn);
    }

    public function validateMessageEmptyReturnTrue(UnitTester $I)
    {
        $methodReturn = $I->getMethodReturn($this->fixture,'validateMessage', '');
        $I->assertTrue($methodReturn);
    }

    public function validateMessageSpamWordsReturnFalse(UnitTester $I)
    {
        $spamProtection = new SpamProtection();
        $spamWords = $spamProtection->spamWords;

        foreach ($spamWords as $spamWord) {
            $methodReturn = $I->getMethodReturn($this->fixture,'validateMessage', $spamWord);
            $I->assertFalse($methodReturn, $spamWord);
        }
    }

    public function validateMessageWithLowerUpperMixSpamWords(UnitTester $I)
    {
        $spamWords = [
            'VIAGRA',
            'ViAgRa',
            'VIAgra'
        ];

        foreach ($spamWords as $spamWord) {
            $methodReturn = $I->getMethodReturn($this->fixture,'validateMessage', $spamWord);
            $I->assertFalse($methodReturn, $spamWord);
        }
    }

}
