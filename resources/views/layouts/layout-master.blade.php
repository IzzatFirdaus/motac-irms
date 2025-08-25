{{-- resources/views/layouts/layout-master.blade.php --}}
{{-- Dynamic dispatcher for picking the correct MOTAC application layout shell based on config.
    Filename updated from layoutMaster.blade.php to layout-master.blade.php as per new convention.
--}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
    $configData = \App\Helpers\Helpers::appClasses();
    $layoutToExtend = 'layouts.layout-content-navbar'; // Default layout for MOTAC Blade views

    if (isset($configData['layout'])) {
        if ($configData['layout'] === 'horizontal') {
            // $layoutToExtend = 'layouts.layout-horizontal'; // Not implemented
        } elseif ($configData['layout'] === 'blank') {
            $layoutToExtend = 'layouts.layout-blank';
        }
        // Default: 'layout-content-navbar' covers vertical menu systems for traditional views.
        // 'layout-app' is used for Livewire-based apps, both extending commonMaster.
    }
@endphp

@include($layoutToExtend)
