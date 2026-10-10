@props(['html'])

{{-- Admin-authored HTML: sanitized on output so stored markup can never run scripts. --}}
<div {{ $attributes->merge(['class' => 'rich-text']) }}>{!! str($html)->sanitizeHtml() !!}</div>
