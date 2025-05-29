{{-- resources/views/layouts/layoutMaster.blade.php --}}
@isset($pageConfigs)
  {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = \App\Helpers\Helpers::appClasses();
  $layoutToExtend = 'layouts.contentNavbarLayout'; // Default MOTAC layout

  if (isset($configData["layout"])) {
      if ($configData["layout"] === 'horizontal') {
          // $layoutToExtend = 'layouts.horizontalLayout'; // If horizontal layout is ever implemented
      } elseif ($configData["layout"] === 'blank') {
          $layoutToExtend = 'layouts.blankLayout';
      }
      // Default is contentNavbarLayout which covers vertical menu systems (app.blade.php or contentNavbarLayout.blade.php)
      // The choice between app.blade.php (Livewire full-page) and contentNavbarLayout (traditional Blade)
      // is typically handled by how routes are defined or controllers return views,
      // rather than this dispatcher, which picks the overall page shell.
      // For MOTAC, 'vertical' layout typically uses contentNavbarLayout as the shell for traditional views,
      // and 'app.blade.php' for Livewire based views, both extending commonMaster.
  }
@endphp

@include($layoutToExtend)
