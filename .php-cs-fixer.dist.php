<?php

/**
 * MOTAC IRMS - PHP CS Fixer Configuration
 * ----------------------------------------
 * This file enforces code style for the MOTAC Integrated Resource Management System.
 * - Based on PSR-12 standard, with additional rules for modern PHP, Laravel conventions,
 *   and improved code readability.
 * - Applies to all application, config, database factories/seeders, routes, and tests.
 * - See: https://cs.symfony.com/doc/ruleSets/PSR12.html for PSR-12 standards
 */

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . "/app",
        __DIR__ . "/config",
        __DIR__ . "/database/factories",
        __DIR__ . "/database/seeders",
        __DIR__ . "/routes",
        __DIR__ . "/tests",
    ]);

$config = new PhpCsFixer\Config();

// Custom order for class elements, matching Laravel and MOTAC IRMS best practices
$orderedClassElements = [
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
];

return $config
    ->setRules([
        "@PSR12" => true, // Base PSR-12 standard
        "array_syntax" => ["syntax" => "short"], // Use short [] arrays
        "no_unused_imports" => true, // Remove unused 'use' statements
        "ordered_imports" => ["sort_algorithm" => "alpha"], // Alphabetical imports
        "ordered_class_elements" => [
            "order" => $orderedClassElements,
        ],
        "no_superfluous_phpdoc_tags" => false, // Allow extra PHPDoc for better docs
        "phpdoc_align" => true, // Align PHPDoc annotations
        "phpdoc_separation" => true, // Blank lines between PHPDoc blocks
        "phpdoc_order" => true, // Order PHPDoc tags consistently
        "single_quote" => true, // Use single quotes where possible
        "binary_operator_spaces" => [
            "default" => "single_space",
        ],
        "method_argument_space" => [
            "on_multiline" => "ensure_fully_multiline",
            "keep_multiple_spaces_after_comma" => false,
        ],
        "blank_line_before_statement" => [
            "statements" => ['return', 'throw', 'try', 'if', 'foreach', 'while'],
        ],
        "no_extra_blank_lines" => [
            "tokens" => [
                "extra",
                "throw",
                "use",
                "parenthesis_brace_block",
                "square_brace_block",
                "curly_brace_block",
            ],
        ],
        "single_line_comment_style" => [
            "comment_types" => ["hash"], // Converts # to //
        ],
        "trailing_comma_in_multiline" => [
            "elements" => ["arrays"],
        ],
        // Add more rules if desired for stricter formatting
    ])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true) // Allow some risky rules for code quality improvements
;
