<?php

declare(strict_types=1);

// Stub the Opcodes\LogViewer controller referenced in routes to avoid Intelephense
// 'Undefined type' diagnostics when the package isn't indexed by the language server.

namespace Opcodes\LogViewer\Http\Controllers;

if (! class_exists(__NAMESPACE__.'\\IndexController')) {
    class IndexController
    {
        public function __invoke($view = null)
        {
            return null;
        }
    }
}
