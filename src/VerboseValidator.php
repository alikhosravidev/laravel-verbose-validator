<?php

namespace Alikhosravidev\VerboseValidator;

use Illuminate\Validation\InvokableValidationRule;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator as BaseValidator;

class VerboseValidator extends BaseValidator
{
    /** @var bool */
    protected $verbose = false;

    /** @var array */
    protected $report = [];

    /**
     * Enable verbose reporting for the validation process.
     *
     * @return $this
     */
    public function verbose()
    {
        $this->verbose = true;

        $this->report = [];

        return $this;
    }

    /**
     * Get the detailed validation report.
     *
     * @return array
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * {@inheritdoc}
     */
    public function passes()
    {
        if ($this->verbose) {
            $this->report = [];
        }

        return parent::passes();
    }

    /**
     * {@inheritdoc}
     */
    protected function validateAttribute($attribute, $rule)
    {
        if (! $this->verbose) {
            return parent::validateAttribute($attribute, $rule);
        }
        $beforeMessageCount = $this->messages->count();

        parent::validateAttribute($attribute, $rule);

        $afterMessageCount = $this->messages->count();

        $result = ($beforeMessageCount === $afterMessageCount);

        [$parsedRule, $parameters] = ValidationRuleParser::parse($rule);
        $value = $this->getValue($attribute);
        $this->recordValidationStep(
            $attribute, $parsedRule, $parameters, $value, $result
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function validateUsingCustomRule($attribute, $value, $rule)
    {
        if (! $this->verbose) {
            return parent::validateUsingCustomRule($attribute, $value, $rule);
        }

        $beforeMessageCount = $this->messages->count();

        parent::validateUsingCustomRule($attribute, $value, $rule);

        $afterMessageCount = $this->messages->count();

        $result = ($beforeMessageCount === $afterMessageCount);

        $ruleClass = $rule instanceof InvokableValidationRule
            ? get_class($rule->invokable())
            : get_class($rule);

        $this->recordValidationStep(
            $this->replacePlaceholderInString($attribute), $ruleClass, [], $value, $result
        );
    }

    /**
     * Record a single validation step in the report.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array  $parameters
     * @param  mixed  $value
     * @param  bool  $result
     */
    protected function recordValidationStep($attribute, $rule, $parameters, $value, $result)
    {
        if (! $this->verbose) {
            return;
        }

        $this->report[$attribute][] = [
            'rule' => $rule,
            'parameters' => $parameters,
            'value' => $value,
            'result' => $result,
        ];
    }
}
