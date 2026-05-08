{{-- Proxy options with selected state support --}}
{{-- Usage: @include('partials.proxy-options-selected', ['order' => $order]) --}}
@php
    // Load proxy configuration from config file
    $domain = config('proxies.domain');
    $portProxyMap = config('proxies.port_map');
    $proxyGroups = config('proxies.groups');
    $proxyLabels = config('proxies.proxy_labels');
    
    // Determine selected proxy - default
    $selected = config('proxies.default');
    $detectionMethod = 'default';
    
    // Detect from proxy string (format: premium-proxy.sparkcliks.com:18801)
    if (isset($order) && !empty($order->proxy) && str_contains($order->proxy, $domain)) {
        // Extract port from proxy string
        foreach ($portProxyMap as $port => $proxyType) {
            if (str_contains($order->proxy, ':' . $port)) {
                $selected = $proxyType;
                $detectionMethod = "port detection (found port {$port})";
                break;
            }
        }
        if ($detectionMethod === 'default') {
            $detectionMethod = 'domain matched but no port found';
        }
    }
    
    // Debug output
    if (config('app.debug')) {
        $proxyValue = isset($order->proxy) ? $order->proxy : 'NULL';
        echo "<!-- DEBUG PROXY SELECTION -->";
        echo "<!-- Proxy field: {$proxyValue} -->";
        echo "<!-- Domain check: {$domain} -->";
        echo "<!-- Detection method: {$detectionMethod} -->";
        echo "<!-- Selected value: {$selected} -->";
        echo "<!-- /DEBUG PROXY SELECTION -->";
    }
@endphp
@foreach($proxyGroups as $groupLabel => $proxyTypes)
<optgroup label="{{ $groupLabel }}">
    @foreach($proxyTypes as $proxyType)
    <option value="{{ $proxyType }}" {{ $selected === $proxyType ? 'selected' : '' }}>{{ $proxyLabels[$proxyType] ?? $proxyType }}</option>
    @endforeach
</optgroup>
@endforeach
