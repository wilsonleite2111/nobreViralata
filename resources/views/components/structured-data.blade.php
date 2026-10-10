@props(['data'])

{{-- JSON_HEX_TAG keeps admin-written text from closing the script element. --}}
@push('head')
    <script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_THROW_ON_ERROR) !!}</script>
@endpush
