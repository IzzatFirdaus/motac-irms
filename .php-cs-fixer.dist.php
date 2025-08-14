<?php

$finder = PhpCsFixer\Finder::create()->in([
  __DIR__ . "/app",
  __DIR__ . "/config",
  __DIR__ . "/database/factories",
  __DIR__ . "/database/seeders",
  __DIR__ . "/routes",
  __DIR__ . "/tests",
]);

$config = new PhpCsFixer\Config();
return $config
  ->setRules([
    "@PSR12" => true,
    //'strict_param' => true, // Example of another rule
    "array_syntax" => ["syntax" => "short"],
    // Add more specific rules here:
    "no_unused_imports" => true, // For removing unused use statements
    "ordered_imports" => ["sort_algorithm" => "alpha"], // Sorts use statements
    "ordered_class_elements" => [
      // For ordering class elements
      "order" => [
        "use_trait",
        "constant_public",
        "constant_protected",
        "constant_private",
        "property_public_static",
        "property_protected_static",
        "property_private_static",
        "property_public",
        "property_protected",
        "property_private",
        "construct",
        "destruct",
        "magic",
        "phpunit",
        "method_public_static",
        "method_protected_static",
        "method_private_static",
        "method_public",
        "method_protected",
        "method_private",
      ],
    ],
    // You might want to add rules for line length if a fixer is available and suitable,
    // or rules for blank lines, etc.
    // 'method_chaining_indentation' => true, // Indents chained method calls
    // 'blank_line_before_statement' => [
    // 'statements' => ['return', 'throw', 'try', 'if', 'foreach', 'while'],
    // ],
    // 'single_line_comment_style' => [
    // 'comment_types' => ['hash'], // Converts # to //
    // ],
  ])
  ->setFinder($finder)
  ->setUsingCache(true);
