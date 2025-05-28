{{-- resources/views/layouts/layoutMaster.blade.php --}}
{{-- This file dispatches to the appropriate main layout based on theme configuration. --}}
{{-- System Design: 3.3 (Helpers::appClasses determines configData['layout']) --}}

@isset($pageConfigs)
  {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  // $configData is fetched to determine which layout to include.
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

{{-- Include the specific layout file based on $configData['layout'] --}}
{{-- For MOTAC, default is 'vertical', which should map to 'contentNavbarLayout' or a similar vertical layout. --}}
@isset($configData["layout"])
  @include((($configData["layout"] === 'horizontal') ? 'layouts.horizontalLayout' :
  (($configData["layout"] === 'blank') ? 'layouts.blankLayout' : 'layouts.contentNavbarLayout')))
@else
  {{-- Fallback to a default layout if 'layout' is not set in $configData --}}
  @include('layouts.contentNavbarLayout')
@endisset
