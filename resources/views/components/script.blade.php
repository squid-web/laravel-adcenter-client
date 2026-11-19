<script async src="{{ $src }}" data-site="{{ $site }}"@if ($ctx !== '') data-ctx="{{ $ctx }}"@endif{!! $attributes->isNotEmpty() ? ' '.$attributes : '' !!}></script>
