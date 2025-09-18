<?php

namespace Alikhosravidev\VerboseValidator;

use Illuminate\Support\ServiceProvider;

class VerboseValidatorServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->app['validator']->resolver(
            function ($translator, $data, $rules, $messages, $attributes) {
                return new VerboseValidator(
                    $translator, $data, $rules, $messages, $attributes
                );
            }
        );
    }
}