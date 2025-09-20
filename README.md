# Laravel Verbose Validator

[](https://packagist.org/packages/alikhosravidev/laravel-verbose-validator)
[](https://github.com/alikhosravidev/laravel-verbose-validator/actions)
[](https://github.com/alikhosravidev/laravel-verbose-validator/blob/main/LICENSE.md)

This package adds a "verbose" or "trace" mode to Laravel's `Validator` class to simplify debugging complex validation rules. Stop guessing why a rule failed; with this package, you can get a complete, step-by-step report of every rule executed and its outcome.

---

## 🎯 Compatibility

This package is compatible with Laravel versions **8, 9, 10, 11, and 12**.

---

## 🚀 Installation

Install via Composer. Since this package is primarily for development, it's recommended to install it as a dev dependency (`--dev`):

```bash
composer require alikhosravidev/laravel-verbose-validator --dev
```

The package supports Laravel's auto-discovery, so you don't need to manually register the ServiceProvider.

---

## ⚙️ Configuration (Optional)

You can publish the configuration file with the following command:

```bash
php artisan vendor:publish --provider="Alikhosravidev\VerboseValidator\VerboseValidatorServiceProvider"
```

This will create a `verbose-validator.php` file in your `config` directory.

**Automatic Verbose Mode:**

Verbose mode is controlled by the following setting:

```php
'enabled' => env('VERBOSE_VALIDATOR_ENABLED', env('APP_DEBUG', false)),

// Determines which type of report should be attached on failed validation ('failed', 'passed', or 'all')
'failure_report_type' => env('VERBOSE_VALIDATOR_FAILURE_REPORT', 'failed'),
```

* If `APP_DEBUG=true`, verbose mode is enabled by default.
* If `APP_DEBUG=false`, verbose mode is disabled.
* You can override this behavior with `VERBOSE_VALIDATOR_ENABLED`.
* On failed validation, by default only the **failed rules** will be attached to the response.
* You can change this to `'all'` or `'passed'` in the config.

---

## 📖 Usage

### Basic Usage

Simply chain the `->verbose()` method onto your `Validator::make()` call (unless automatic mode is enabled):

```php
use Illuminate\Support\Facades\Validator;

$data = [
    'email' => 'test@example.com',
    'password' => '123',
];
$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
];

$validator = Validator::make($data, $rules)->verbose();

if ($validator->fails()) {
    $report = $validator->getReport(); // full report
    dd($report);
}
```

---

### Filtering Reports

The `getReport()` method accepts a filter argument:

```php
$validator->getReport('all');    // default, all rules
$validator->getReport('failed'); // only failed rules
$validator->getReport('passed'); // only passed rules
```

For convenience, you can also call:

* `getFailedReport()` → Equivalent to `getReport('failed')`
* `getPassedReport()` → Equivalent to `getReport('passed')`

---

### Reports in Validation Failures

When validation fails, Laravel throws a `ValidationException`.
This package automatically attaches the validation report to the **422 JSON response** (only when verbose mode is active).

By default, only the **failed rules** are attached. You can change this via the `failure_report_type` config option.

**Example failed response (default):**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "password": [
      "The password must be at least 8 characters."
    ]
  },
  "verbose_report": {
    "password": [
      {
        "rule": "Min",
        "parameters": ["8"],
        "value": "123",
        "result": false
      }
    ]
  }
}
```

---

### Reports in Successful Validation

When validation passes, you can still access the report inside your application logic:

```php
$report = $request->validator->getReport();
```

This will return the report based on the executed validation rules for that request.

---

## 🧩 Support for Custom Rules

This package fully supports custom validation rules, both **Closure-based** and **Rule Objects**.
The result of their execution will be logged in the report just like native Laravel rules.

---

## 🧪 Testing

Run tests locally:

```bash
composer test
```

---

## 🙌 Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue.

---

## 📄 License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.