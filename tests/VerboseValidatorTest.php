<?php

namespace Alikhosravidev\VerboseValidator\Tests;

use Alikhosravidev\VerboseValidator\VerboseValidator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

class VerboseValidatorTest extends TestCase
{
    /**
     * Helper to create a new validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @return \Alikhosravidev\VerboseValidator\VerboseValidator
     */
    protected function makeValidator(array $data, array $rules): VerboseValidator
    {
        $loader = new ArrayLoader;

        $translator = new Translator($loader, 'en');

        return new VerboseValidator($translator, $data, $rules);
    }

    public function testReportIsEmptyWhenVerboseModeIsNotEnabled()
    {
        $validator = $this->makeValidator(
            ['name' => 'Taylor'],
            ['name' => 'required']
        );

        $validator->passes();

        $this->assertEmpty($validator->getReport());
    }

    public function testReportCapturesSuccessfulValidationSteps()
    {
        $validator = $this->makeValidator(
            ['name' => 'Taylor', 'email' => 'taylor@laravel.com'],
            ['name' => 'required|string', 'email' => 'required|email']
        )->verbose();

        $this->assertTrue($validator->passes());

        $allReport = $validator->getReport();

        $this->assertArrayHasKey('name', $allReport);
        $this->assertArrayHasKey('email', $allReport);

        $this->assertEquals('Required', $allReport['name'][0]['rule']);
        $this->assertTrue($allReport['name'][0]['result']);
        $this->assertEquals('String', $allReport['name'][1]['rule']);
        $this->assertTrue($allReport['name'][1]['result']);

        $this->assertEquals('Required', $allReport['email'][0]['rule']);
        $this->assertTrue($allReport['email'][0]['result']);
        $this->assertEquals('Email', $allReport['email'][1]['rule']);
        $this->assertTrue($allReport['email'][1]['result']);

        $this->assertEmpty($validator->getFailedReport());
        $this->assertEquals($allReport, $validator->getPassedReport());
    }

    public function testReportCapturesFailedValidationSteps()
    {
        $key = 'password';
        $validator = $this->makeValidator(
            [$key => '123'],
            [$key => 'required|min:8']
        )->verbose();

        $this->assertTrue($validator->fails());

        $allReport = $validator->getReport();
        $passedReport = $validator->getPassedReport();
        $failedReport = $validator->getFailedReport();

        $this->assertArrayHasKey($key, $allReport);
        $this->assertCount(2, $allReport[$key]);
        $this->assertCount(1, $passedReport[$key]);
        $this->assertCount(1, $failedReport[$key]);
        $this->assertNotEquals($passedReport[$key], $failedReport[$key]);

        $this->assertEquals('Required', $passedReport[$key][0]['rule']);
        $this->assertEquals('123', $passedReport[$key][0]['value']);
        $this->assertTrue($passedReport[$key][0]['result']);

        $this->assertEquals('Min', $failedReport[$key][0]['rule']);
        $this->assertEquals(['8'], $failedReport[$key][0]['parameters']);
        $this->assertFalse($failedReport[$key][0]['result']);
    }

    public function testReportWorksWithCustomRuleObjects()
    {
        $customRule = new class implements Rule
        {
            public function passes($attribute, $value)
            {
                return $value === 'laravel';
            }

            public function message()
            {
                return 'The value must be "laravel".';
            }
        };

        $validator = $this->makeValidator(
            ['framework' => 'laravel'],
            ['framework' => ['required', $customRule]]
        )->verbose();

        $validator->passes();
        $report = $validator->getReport();

        $this->assertArrayHasKey('framework', $report);

        $this->assertEquals(get_class($customRule), $report['framework'][1]['rule']);
        $this->assertEquals('laravel', $report['framework'][1]['value']);
        $this->assertTrue($report['framework'][1]['result']);
    }

    public function testReportIsClearedOnEachValidationRun()
    {
        $validator = $this->makeValidator(['name' => 'A'], ['name' => 'min:2'])->verbose();

        $validator->fails();
        $report1 = $validator->getReport();
        $this->assertCount(1, $report1['name']);
        $this->assertFalse($report1['name'][0]['result']);
        $this->assertEmpty($validator->getPassedReport());
        $this->assertEquals($report1, $validator->getFailedReport());

        $validator->setData(['name' => 'Correct']);
        $validator->passes();
        $report2 = $validator->getReport();

        $this->assertCount(1, $report2['name']);
        $this->assertTrue($report2['name'][0]['result']);

        $this->assertEmpty($validator->getFailedReport());
        $this->assertEquals($report2, $validator->getPassedReport());
    }
}
