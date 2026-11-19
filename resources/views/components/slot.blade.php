@if ($enabled)<div {{ $attributes->merge($wrapperAttributes()) }}>@if ($slot->hasActualContent())<template data-adcenter-fallback>{{ $slot }}</template>@endif</div>@else{{ $slot }}@endif
