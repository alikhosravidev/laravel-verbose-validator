<?php

namespace Alikhosravidev\VerboseValidator;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

class VerboseValidatorServiceProvider extends ServiceProvider
{
    private $configPath = __DIR__.'/../config/verbose-validator.php';
    public function register()
    {
        $this->mergeConfigFrom(
            $this->configPath, 'verbose-validator'
        );
    }

    public function boot()
    {
        $this->publishes(
            [
                $this->configPath => config_path('verbose-validator.php'),
            ],
            'config'
        );

        $this->app['validator']->resolver(
            function ($translator, $data, $rules, $messages, $attributes) {
                $validator = new VerboseValidator(
                    $translator, $data, $rules, $messages, $attributes
                );

                if (config('verbose-validator.enabled')) {
                    $validator->verbose();
                }

                return $validator;
            }
        );

        $this->app->make(ExceptionHandler::class)
            ->renderable(
                function (ValidationException $e, $request) {
                    if (config('verbose-validator.enabled')
                        && method_exists($e->validator, 'getReport')) {

                        $report = $e->validator->getReport(
                            config('verbose-validator.failure_report_type')
                        );

                        return response()->json(
                            [
                                'message' => $e->getMessage(),
                                'errors' => $e->errors(),
                                'verbose_report' => $report,
                            ],
                            422
                        );
                    }
                }
            );
    }
}
