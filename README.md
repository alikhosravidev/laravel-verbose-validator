# Laravel Verbose Validator

[](https://www.google.com/search?q=https://packagist.org/packages/alikhosravidev/laravel-verbose-validator)
[](https://www.google.com/search?q=https://packagist.org/packages/alikhosravidev/laravel-verbose-validator)
[](https://www.google.com/search?q=https://github.com/alikhosravidev/laravel-verbose-validator/actions)
[](https://www.google.com/search?q=https://github.com/alikhosravidev/laravel-verbose-validator/blob/main/LICENSE.md)

This package adds a "verbose" or "trace" mode to Laravel's `Validator` class to simplify debugging complex validation rules. Stop guessing why a rule failed; with this package, you can get a complete, step-by-step report of every rule executed and its outcome.

-----

## 🤔 The Problem

Sometimes, especially with complex forms, all you get from a failed validation is a generic error message. For example:

```php
$rules = ['password' => 'required|min:8|regex:/[A-Z]/'];
$data = ['password' => '12345'];

// Result: "The password must be at least 8 characters."
// But was the regex rule even checked? What was its result?
```

-----

## ✅ The Solution with Verbose Validator

This package allows you to get a detailed report of each validation step:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($data, $rules)->verbose();

if ($validator->fails()) {
    $report = $validator->getReport();
    /*
    $report = [
        "password" => [
            ["rule" => "Required", "parameters" => [], "value" => "12345", "result" => true],
            ["rule" => "Min", "parameters" => ["8"], "value" => "12345", "result" => false],
            // The regex rule is not executed due to the default "bail" behavior on the first failure.
        ]
    ]
    */
    dd($report);
}
```

-----

## 🚀 Installation

You can install the package via Composer:

```bash
composer require alikhosravidev/laravel-verbose-validator
```

The package supports Laravel's auto-discovery, so you don't need to manually register the ServiceProvider.

-----

## 📖 Usage

Using the package is straightforward. Simply chain the `->verbose()` method onto your `Validator::make()` call.

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

// Add the verbose() method here
$validator = Validator::make($data, $rules)->verbose();

if ($validator->fails()) {
    // Get the full report for debugging
    $report = $validator->getReport();
    
    dd($report);
}
```

The `getReport()` method returns an array containing all validation steps. Each step includes the following information:

- **`rule`**: The name of the rule that was executed.
- **`parameters`**: The parameters passed to the rule.
- **`value`**: The value that was tested against the rule.
- **`result`**: The outcome of the rule execution (`true` for pass, `false` for fail).

-----

## 🧪 Testing

The package is fully tested. To run the tests locally:

```bash
composer test
```

-----

## 🙌 Contributing

Contributions are welcome\! Please feel free to submit a pull request or open an issue.

-----

## 📄 License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.