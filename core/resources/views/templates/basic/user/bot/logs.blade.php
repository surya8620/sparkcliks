@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <div class="row justify-content-center">
        <div class="col-12">            <div class="card custom-card">                <!-- Header hidden initially, shown after connection -->
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2" id="logHeader" style="display: none !important;">
                    <h5 class="card-title mb-0 mb-md-0">
                        <i class="las la-stream"></i> @lang(' Campaign Logs ')
                    </h5>
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <button type="button" class="btn btn-sm btn--base" id="connectBtn" onclick="toggleConnection()">
                            <i class="las la-plug"></i> <span class="d-none d-sm-inline">@lang('Connect')</span><span class="d-inline d-sm-none">@lang('Connect')</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="clearBtn" onclick="clearLogs()">
                            <i class="las la-trash"></i> <span class="d-none d-sm-inline">@lang('Clear')</span><span class="d-inline d-sm-none">@lang('Clear')</span>
                        </button>
                    </div>
                </div>
                  <div class="card-body" id="logCardBody">
                    <!-- Hidden Order ID Input -->
                    <input type="hidden" id="orderId" value="{{ $orderId ?? '' }}">
                    
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" class="loading-overlay">
                        <div class="loading-content">
                            <div class="loading-spinner">
                                <i class="las la-sync la-spin"></i>
                            </div>
                            <h4 class="mt-3 mb-2" id="loadingTitle">@lang('Initializing Logs')</h4>
                            <p class="text-muted mb-0" id="loadingMessage">@lang('Please wait, authentication in progress...')</p>
                        </div>
                    </div>

                    <!-- Reconnection Overlay (hidden by default) -->
                    <div id="reconnectOverlay" class="reconnect-overlay" style="display: none;">
                        <div class="reconnect-content">
                            <div class="reconnect-icon">
                                <i class="las la-exclamation-triangle"></i>
                            </div>
                            <h4 class="mt-3 mb-2">@lang('Connection Lost')</h4>
                            <p class="text-muted mb-3" id="reconnectMessage">@lang('The connection to the log server was lost.')</p>
                            <button type="button" class="btn btn--base" onclick="reconnectManually()">
                                <i class="las la-plug"></i> @lang('Reconnect')
                            </button>
                        </div>
                    </div>                    <!-- Main Content (hidden until connected) -->
                    <div id="mainContent" style="display: none; opacity: 0; transform: translateY(10px); transition: opacity 0.5s ease, transform 0.5s ease;">
                        <!-- Statistics Cards -->                    <div class="row g-3 mb-3">
                        <!-- <div class="col-6 col-md-3">
                            <div class="card bg--primary-light text-center stat-card">
                                <div class="card-body py-2">
                                    <h3 class="mb-0 text--primary" id="totalLines">0</h3>
                                    <small class="text-muted">@lang('Total Log Lines')</small>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-6 col-md-4">
                            <div class="card bg--success-light text-center stat-card">
                                <div class="card-body py-2">
                                    <h3 class="mb-0 text--success" id="newLines">0</h3>
                                    <small class="text-muted">@lang('Total Log Lines')</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card bg--info-light text-center stat-card">
                                <div class="card-body py-2">
                                    <h3 class="mb-0 text--info" id="connectionTime">00:00:00</h3>
                                    <small class="text-muted">@lang('Connected Time')</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card bg--warning-light text-center stat-card">
                                <div class="card-body py-2">
                                    <h3 class="mb-0 text--warning" id="currentOrder">-</h3>
                                    <small class="text-muted">@lang('Campaign ID')</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Export Controls -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="@lang('Search logs...')" onkeyup="filterLogs()">
                            </div>
                        </div>                        <div class="col-12 col-md-6 d-flex gap-2 flex-wrap flex-md-nowrap">
                            <button type="button" class="btn btn-outline--primary flex-fill btn-sm-mobile" onclick="exportLogs()">
                                <i class="las la-file-export"></i> <span class="d-none d-sm-inline">@lang('Export Visible')</span><span class="d-inline d-sm-none">@lang('Export')</span>
                            </button>
                            <button type="button" class="btn btn-outline--info flex-fill btn-sm-mobile" id="downloadBtn" onclick="downloadFullLog()">
                                <i class="las la-download"></i> <span class="d-none d-sm-inline">@lang('Download Full Log')</span><span class="d-inline d-sm-none">@lang('Download')</span>
                            </button>
                        </div>
                    </div>

                    <!-- Log Container -->
                    <div class="log-container border rounded">
                        <div class="log-header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                            <span><i class="las la-clipboard-list"></i> @lang('Live Log Stream')</span>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="autoScroll" checked>
                                <label class="form-check-label" for="autoScroll">
                                    @lang('Auto-scroll')
                                </label>
                            </div>
                        </div>                        <div id="logContent" class="log-content p-3">
                            <div class="log-line text-muted" id="initialMessage">
                                <span class="badge bg-info">[Initializing]</span> 
                                <span>@lang('Please wait, initializing log connection...')</span>                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" style="position: fixed; top: 80px; right: 20px; z-index: 9999;"></div>
@endsection

@push('style')
<style>
    /* ===== BASE LAYOUT ===== */
    .card-body {
        position: relative;
        min-height: 400px;
        padding: 20px;
    }
    
    /* ===== HEADER STYLES ===== */
    #logHeader {
        transition: opacity 0.5s ease-in-out;
    }
    
    /* ===== MAIN CONTENT ===== */
    #mainContent {
        will-change: opacity, transform;
        position: relative;
        z-index: 1;
    }
    
    /* ===== LOADING OVERLAY ===== */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.98);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(5px);
        border-radius: 0.5rem;
        transition: opacity 0.4s ease-in-out;
    }
    
    .loading-content {
        text-align: center;
        padding: 30px 25px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        max-width: 380px;
        width: 90%;
        transition: transform 0.4s ease-in-out, opacity 0.4s ease-in-out;
    }
    
    .loading-content h4 {
        font-size: 1.25rem;
        margin-top: 15px;
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }
    
    .loading-content p {
        font-size: 0.95rem;
        margin-bottom: 0;
        transition: color 0.3s ease;
    }
    
    .loading-spinner i {
        font-size: 48px;
        color: #375c6c;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* ===== RECONNECTION OVERLAY ===== */
    .reconnect-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background: rgba(220, 53, 69, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        backdrop-filter: blur(10px);
        border-radius: 0.5rem;
        transition: opacity 0.4s ease-in-out;
    }
    
    .reconnect-content {
        text-align: center;
        padding: 30px 25px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        max-width: 400px;
        width: 90%;
        transition: transform 0.4s ease-in-out, opacity 0.4s ease-in-out;
    }
    
    .reconnect-content h4 {
        font-size: 1.25rem;
        margin-top: 15px;
        margin-bottom: 10px;
    }
    
    .reconnect-content p {
        font-size: 0.95rem;
        margin-bottom: 20px;
    }
    
    .reconnect-icon i {
        font-size: 48px;
        color: #dc3545;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    /* ===== STAT CARDS ===== */
    .bg--primary-light {
        background-color: rgba(55, 92, 108, 0.1);
    }
    
    .bg--success-light {
        background-color: rgba(76, 175, 80, 0.1);
    }
    
    .bg--info-light {
        background-color: rgba(79, 195, 247, 0.1);
    }
    
    .bg--warning-light {
        background-color: rgba(255, 183, 77, 0.1);
    }
    
    .stat-card {
        height: 100%;
        min-height: 85px;
        max-height: 85px;
        margin-bottom: 0;
    }
    
    .stat-card .card-body {
        padding: 12px 10px !important;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        min-height: auto;
    }
    
    .stat-card h3 {
        font-size: 1.3rem;
        margin-bottom: 3px !important;
        line-height: 1.1;
        font-weight: 600;
    }
    
    .stat-card small {
        font-size: 0.7rem;
        display: block;
        margin-top: 2px;
        line-height: 1.2;
    }
    
    /* ===== LOG CONTAINER ===== */
    .log-container {
        background: #1e1e1e;
        height: 600px;
        overflow: hidden;
        border-radius: 8px;
        margin-top: 0;
    }
    
    .log-header {
        background: #2d2d2d !important;
        color: #fff;
        font-size: 0.95rem;
    }
    
    .log-content {
        height: calc(100% - 60px);
        overflow-y: auto;
        overflow-x: hidden;
        background: #1e1e1e;
        font-family: 'Courier New', monospace;
        line-height: 1.6;
        color: #d4d4d4;
        padding: 12px;
    }
    
    /* ===== LOG LINES ===== */
    .log-line {
        padding: 8px 12px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        transition: background 0.2s ease;
        font-size: 13px;
        word-wrap: break-word;
        word-break: break-word;
        color: #d4d4d4;
        overflow-wrap: break-word;
    }
    
    .log-line:hover {
        background: rgba(255,255,255,0.05);
        border-left: 3px solid #375c6c;
    }
    
    .log-line.search-highlight {
        background: rgba(255,193,7,0.2) !important;
        border-left: 4px solid #ffc107 !important;
    }
    
    .log-line .timestamp {
        color: #4fc3f7;
        font-weight: 600;
    }
    
    .log-line .session {
        color: #ffb74d;
        font-weight: 600;
    }
    
    .log-line .status {
        color: #81c784;
        font-weight: 600;
    }
    
    .log-line .error {
        color: #f48fb1;
        font-weight: 600;
    }
    
    .log-line .url {
        color: #ce93d8;
        text-decoration: underline;
        cursor: pointer;
    }
    
    /* ===== NOTIFICATIONS ===== */
    #toast-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9999;
    }
    
    .notification {
        margin-bottom: 10px;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease-out;
        cursor: pointer;
        max-width: 350px;
    }
    
    .notification.success {
        background: #d1e7dd;
        border-left: 4px solid #198754;
        color: #0f5132;
    }
    
    .notification.error {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }
    
    .notification.warning {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        color: #856404;
    }
    
    .notification.info {
        background: #cff4fc;
        border-left: 4px solid #0dcaf0;
        color: #055160;
    }
    
    @keyframes slideInRight {
        0% {
            opacity: 0;
            transform: translateX(100%);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        0% {
            opacity: 1;
            transform: translateX(0);
        }
        100% {
            opacity: 0;
            transform: translateX(100%);
        }
    }
    
    /* ===== MOBILE RESPONSIVE - TABLET ===== */
    @media (max-width: 768px) {
        .card-body {
            min-height: 350px;
            padding: 15px;
        }
        
        .card-header .card-title {
            font-size: 0.95rem;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
        
        .btn-sm-mobile {
            font-size: 0.8rem !important;
            padding: 6px 10px !important;
        }
        
        .form-control,
        .input-group-text {
            font-size: 0.85rem;
            padding: 6px 10px;
        }
        
        /* Stat cards on tablet */
        .stat-card {
            min-height: 75px;
            max-height: 75px;
        }
        
        .stat-card h3 {
            font-size: 1.1rem;
        }
        
        .stat-card small {
            font-size: 0.65rem;
        }
        
        .stat-card .card-body {
            padding: 8px 6px !important;
        }
        
        /* Log container on tablet */
        .log-container {
            height: 400px;
        }
        
        .log-header {
            font-size: 0.85rem;
            padding: 10px !important;
            flex-direction: column;
            gap: 8px;
        }
        
        .log-header span {
            font-size: 0.85rem;
        }
        
        .log-content {
            height: calc(100% - 70px);
            padding: 8px;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .log-line {
            padding: 6px 8px;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .log-line .badge {
            font-size: 0.65rem;
            padding: 2px 4px;
        }
        
        .form-check-label {
            font-size: 0.8rem;
        }
        
        /* Toast notifications on tablet */
        #toast-container {
            top: 60px !important;
            right: 10px !important;
            left: 10px !important;
            max-width: calc(100% - 20px);
        }
        
        .notification {
            max-width: 100%;
            font-size: 0.85rem;
            padding: 10px 15px;
        }
        
        /* Loading overlays on tablet */
        .loading-content,
        .reconnect-content {
            max-width: 90%;
            padding: 25px 20px;
        }
        
        .loading-spinner i,
        .reconnect-icon i {
            font-size: 40px;
        }
        
        .loading-content h4,
        .reconnect-content h4 {
            font-size: 1.1rem;
        }
        
        .loading-content p,
        .reconnect-content p {
            font-size: 0.9rem;
        }
        
        .row.g-3 {
            --bs-gutter-x: 0.75rem;
            --bs-gutter-y: 0.75rem;
        }
    }
    
    /* ===== MOBILE RESPONSIVE - PHONE ===== */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        
        .card-body {
            min-height: 300px;
            padding: 12px;
        }
        
        .card-header {
            padding: 12px !important;
        }
        
        .card-header h5,
        .card-header .card-title {
            font-size: 0.9rem;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 3px 6px;
        }
        
        /* Stat cards on phone */
        .stat-card {
            min-height: 70px;
            max-height: 70px;
        }
        
        .stat-card h3 {
            font-size: 1rem;
        }
        
        .stat-card small {
            font-size: 0.6rem;
        }
        
        /* Log container on phone */
        .log-container {
            height: 350px;
        }
        
        .log-content {
            font-size: 10px;
            padding: 6px;
        }
        
        .log-line {
            padding: 5px 6px;
            font-size: 10px;
        }
        
        .col-6 {
            margin-bottom: 8px;
        }
        
        .col-12.col-md-6 {
            margin-bottom: 10px;
        }    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        let ws = null;
        let isConnected = false;
        let totalLinesCount = 0;
        let newLinesCount = 0;
        let connectionStartTime = null;
        let timeInterval = null;
        let reconnectAttempts = 0;
        let maxReconnectAttempts = 5;
        let reconnectTimeout = null;
        let maxLogLines = 1000;
        let authToken = null; // Secure token from backend
        let wsUrl = null; // WebSocket server URL from backend
        let isInitializing = true; // Track if we're in initial load state
        let tokenExpiry = null; // Token expiration timestamp
        
        // Configuration (NO CREDENTIALS HERE!)
        const tokenEndpoint = "{{ route('user.bot.logs.token') }}";
        
        // Session storage helpers
        function getCachedToken(orderId) {
            try {
                const cacheKey = `bot_logs_token_${orderId}`;
                const cached = sessionStorage.getItem(cacheKey);
                
                if (!cached) {
                    console.log('No cached token found for order:', orderId);
                    return null;
                }
                
                const data = JSON.parse(cached);
                const now = Date.now();
                
                // Check if token has expired (with 5 minute buffer)
                if (data.expiry && now >= (data.expiry - 300000)) {
                    console.log('Cached token expired for order:', orderId);
                    sessionStorage.removeItem(cacheKey);
                    return null;
                }
                
                console.log('Using cached token for order:', orderId, 'expires in:', Math.round((data.expiry - now) / 1000 / 60), 'minutes');
                return data;
            } catch (error) {
                console.error('Error reading cached token:', error);
                return null;
            }
        }
        
        function setCachedToken(orderId, token, wsUrl, expiresIn) {
            try {
                const cacheKey = `bot_logs_token_${orderId}`;
                const expiry = Date.now() + (expiresIn * 1000); // Convert seconds to milliseconds
                
                const data = {
                    token: token,
                    ws_url: wsUrl,
                    order_id: orderId,
                    expiry: expiry,
                    cached_at: Date.now()
                };
                
                sessionStorage.setItem(cacheKey, JSON.stringify(data));
                console.log('Token cached for order:', orderId, 'expires in:', expiresIn, 'seconds');
            } catch (error) {
                console.error('Error caching token:', error);
            }
        }
        
        function clearCachedToken(orderId) {
            try {
                const cacheKey = `bot_logs_token_${orderId}`;
                sessionStorage.removeItem(cacheKey);
                console.log('Cleared cached token for order:', orderId);
            } catch (error) {
                console.error('Error clearing cached token:', error);
            }
        }
        
        function clearAllCachedTokens() {
            try {
                const keys = Object.keys(sessionStorage);
                keys.forEach(key => {
                    if (key.startsWith('bot_logs_token_')) {
                        sessionStorage.removeItem(key);
                    }
                });
                console.log('Cleared all cached tokens');
            } catch (error) {
                console.error('Error clearing all cached tokens:', error);
            }
        }
          // Overlay management functions with smooth transitions
        let overlayTimeout = null;
        let currentOverlayState = 'loading'; // 'loading', 'content', 'reconnect'
        
        function showLoadingOverlay(title, message) {
            // Clear any pending transitions
            if (overlayTimeout) {
                clearTimeout(overlayTimeout);
                overlayTimeout = null;
            }
            
            // Update content first
            $('#loadingTitle').text(title);
            $('#loadingMessage').text(message);
            
            // Only show if not already visible
            if (!$('#loadingOverlay').is(':visible')) {
                $('#loadingOverlay').stop(true, false).fadeIn(400);
            }
            
            // Hide reconnect overlay smoothly
            if ($('#reconnectOverlay').is(':visible')) {
                $('#reconnectOverlay').stop(true, false).fadeOut(300);
            }
            
            currentOverlayState = 'loading';
        }
          function hideLoadingOverlay() {
            // Prevent rapid transitions
            if (overlayTimeout) {
                clearTimeout(overlayTimeout);
            }
            
            overlayTimeout = setTimeout(() => {
                $('#loadingOverlay').stop(true, false).fadeOut(400, function() {
                    // After loading overlay is completely hidden, show content smoothly
                    $('#logHeader').stop(true, false).css('display', 'flex').hide().fadeIn(500);
                    
                    // Show main content with smooth transition
                    $('#mainContent')
                        .stop(true, false)
                        .css('display', 'block')
                        .css('opacity', '0')
                        .css('transform', 'translateY(10px)')
                        .animate({opacity: 1}, 500)
                        .css('transform', 'translateY(0)');
                    
                    isInitializing = false;
                    currentOverlayState = 'content';
                });
            }, 300); // Small delay to prevent flashing
        }
        
        function showReconnectOverlay(message) {
            // Clear any pending transitions
            if (overlayTimeout) {
                clearTimeout(overlayTimeout);
                overlayTimeout = null;
            }
            
            $('#reconnectMessage').text(message || 'The connection to the log server was lost.');
            
            // Smooth transition from loading to reconnect
            $('#loadingOverlay').stop(true, false).fadeOut(300, function() {
                $('#reconnectOverlay').stop(true, false).fadeIn(400);
            });
            
            currentOverlayState = 'reconnect';
        }
        
        function hideReconnectOverlay() {
            $('#reconnectOverlay').stop(true, false).fadeOut(400);
        }
        
        window.reconnectManually = function() {
            hideReconnectOverlay();
            showLoadingOverlay('Reconnecting...', 'Please wait, reconnecting to log server...');
            reconnectAttempts = 0; // Reset reconnect attempts
            connect();
        };
        
        // Notification system
        function showNotification(message, type = 'info', duration = 3000) {
            const notification = $(`
                <div class="notification ${type}">
                    <i class="las ${type === 'success' ? 'la-check-circle' : type === 'error' ? 'la-times-circle' : type === 'warning' ? 'la-exclamation-triangle' : 'la-info-circle'}"></i>
                    ${message}
                </div>
            `);
            
            $('#toast-container').append(notification);
            
            setTimeout(() => {
                notification.css('animation', 'slideOutRight 0.3s ease-out');
                setTimeout(() => notification.remove(), 300);
            }, duration);
            
            notification.on('click', function() {
                $(this).css('animation', 'slideOutRight 0.3s ease-out');
                setTimeout(() => $(this).remove(), 300);
            });
        }        function updateStatus(message, type) {
            // Only log to console, no DOM updates
            // Status messages now shown only via toast notifications
            console.log(`[${new Date().toISOString()}] Status: ${message} (${type})`);
        }
        
        function updateStats() {
            $('#totalLines').text(totalLinesCount.toLocaleString());
            $('#newLines').text(newLinesCount.toLocaleString());
            $('#currentOrder').text($('#orderId').val() || '-');
        }
        
        function updateConnectionTime() {
            if (connectionStartTime) {
                const elapsed = Date.now() - connectionStartTime;
                const hours = Math.floor(elapsed / 3600000);
                const minutes = Math.floor((elapsed % 3600000) / 60000);
                const seconds = Math.floor((elapsed % 60000) / 1000);
                
                $('#connectionTime').text(
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                );
            }
        }
        
        function addLogLine(text, isNew = false) {
            const logContent = $('#logContent');
            
            // Remove old lines if exceeded limit
            while (logContent.children().length >= maxLogLines) {
                logContent.children().first().remove();
                totalLinesCount = Math.max(0, totalLinesCount - 1);
            }
              const logLine = $('<div>')
                .addClass('log-line')
                // Removed 'new' class - no green highlighting
                .html(formatLogLine(text))
                .data('lineNumber', totalLinesCount + 1);
            
            logContent.append(logLine);
            totalLinesCount++;
              if (isNew) {
                newLinesCount++;
                // Removed: Green highlighting animation for new lines
                // Removed: Error toast notifications (too many with live data)
            }
            
            updateStats();
            
            if ($('#autoScroll').is(':checked')) {
                logContent[0].scrollTop = logContent[0].scrollHeight;
            }
            
            // Apply current search filter
            const searchTerm = $('#searchInput').val().toLowerCase();
            if (searchTerm && !text.toLowerCase().includes(searchTerm)) {
                logLine.hide();
            } else if (searchTerm && text.toLowerCase().includes(searchTerm)) {
                logLine.addClass('search-highlight');
            }
        }
        
        function formatLogLine(text) {
            if (!text) return '';
            
            const escaped = $('<div>').text(text).html();
            
            return escaped
                .replace(/(\[\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z\])/g, '<span class="timestamp">$1</span>')
                .replace(/(Session\d+)/g, '<span class="session">$1</span>')
                .replace(/(✓|successfully|successful|Working|completed|success)/gi, '<span class="status">$1</span>')
                .replace(/(error|failed|❌|✗|failure|exception)/gi, '<span class="error">$1</span>')
                .replace(/(https?:\/\/[^\s]+)/g, '<span class="url">$1</span>')
                .replace(/(IP:\s*[\d.]+)/g, '<span class="status">$1</span>')
                .replace(/(\b\d+\.\d+\s*seconds?\b)/g, '<span class="timestamp">$1</span>');
        }
        
        window.clearLogs = function() {
            const logContent = $('#logContent');
            
            if (totalLinesCount > 100) {
                if (!confirm(`Clear ${totalLinesCount} log lines? This action cannot be undone.`)) {
                    return;
                }
            }
            
            logContent.html('<div class="log-line text-muted"><span class="badge bg-info">[Cleared]</span> Log cleared by user</div>');
            totalLinesCount = 0;
            newLinesCount = 0;
            updateStats();
            showNotification('Logs cleared successfully', 'info');
        };
        
        window.filterLogs = function() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const logLines = $('.log-line');
            let visibleCount = 0;
            
            logLines.each(function() {
                const line = $(this);
                line.removeClass('search-highlight');
                
                if (!searchTerm || line.text().toLowerCase().includes(searchTerm)) {
                    line.show();
                    if (searchTerm) line.addClass('search-highlight');
                    visibleCount++;
                } else {
                    line.hide();
                }
            });
            
            if (searchTerm) {
                updateStatus(`Showing ${visibleCount} of ${totalLinesCount} lines matching "${searchTerm}"`, isConnected ? 'connected' : 'disconnected');
            } else if (isConnected) {
                updateStatus('Connected and monitoring logs', 'connected');
            }
        };
        
        window.exportLogs = function() {
            const logLines = $('.log-line:visible');
            const logText = logLines.map(function() {
                return $(this).text().trim();
            }).get().join('\n');
            
            if (!logText) {
                showNotification('No logs to export', 'warning');
                return;
            }
            
            const orderId = $('#orderId').val() || 'unknown';
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
            const filename = `visible-logs-${orderId}-${timestamp}.txt`;
            
            const blob = new Blob([logText], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            showNotification(`Exported ${logLines.length} visible lines to ${filename}`, 'success');
        };        window.downloadFullLog = async function() {
            const orderId = $('#orderId').val().trim();
            
            if (!orderId) {
                showNotification('Please enter an Order ID', 'warning');
                return;
            }
            
            if (!/^\d+$/.test(orderId)) {
                showNotification('Order ID must be numeric', 'warning');
                return;
            }
            
            const downloadButton = $('#downloadBtn');
            const originalText = downloadButton.html();
            downloadButton.html('<i class="las la-sync fa-spin"></i> Downloading...').prop('disabled', true);
            
            try {
                // Get token and server URL
                let token = authToken;
                let serverUrl = wsUrl;
                
                // Try cached token if not currently connected
                if (!token || !serverUrl) {
                    const cached = getCachedToken(orderId);
                    if (cached && cached.token && cached.ws_url) {
                        token = cached.token;
                        serverUrl = cached.ws_url;
                        console.log('Using cached token for download');
                    }
                }
                
                // If still no token, fetch new one
                if (!token || !serverUrl) {
                    console.log('Fetching new token for download...');
                    downloadButton.html('<i class="las la-sync fa-spin"></i> Authenticating...').prop('disabled', true);
                    
                    const tokenResponse = await fetch(tokenEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order_id: orderId })
                    });
                    
                    if (!tokenResponse.ok) {
                        throw new Error('Failed to get authentication token');
                    }
                    
                    const tokenData = await tokenResponse.json();
                    
                    if (!tokenData.success) {
                        throw new Error(tokenData.message || 'Authentication failed');
                    }
                    
                    token = tokenData.token;
                    serverUrl = tokenData.ws_url;
                    
                    setCachedToken(orderId, token, serverUrl, tokenData.expires_in || 3600);
                    console.log('✅ Token fetched and cached');
                    downloadButton.html('<i class="las la-sync fa-spin"></i> Downloading...').prop('disabled', true);
                }
                  // Use Laravel backend as proxy to avoid CORS issues
                const downloadUrl = "{{ route('user.bot.logs.download', ':id') }}".replace(':id', orderId);
                
                console.log('📥 Downloading via Laravel proxy:', downloadUrl);
                
                // Send request through Laravel backend with token
                const response = await fetch(downloadUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        token: token,
                        serverUrl: serverUrl
                    })
                });
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    
                    if (response.status === 401) {
                        clearCachedToken(orderId);
                        throw new Error('Token expired. Please try again.');
                    } else if (response.status === 403) {
                        throw new Error('Access denied for this order');
                    } else if (response.status === 404) {
                        throw new Error('Log file not found');
                    } else {
                        throw new Error(errorData.error || `Download failed (${response.status})`);
                    }
                }
                
                const data = await response.json();
                
                if (!data.lines || !Array.isArray(data.lines)) {
                    throw new Error('Invalid response from server');
                }
                
                // Create and download file
                const logText = data.lines.join('\n');
                const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
                const filename = `order-${orderId}-logs-${timestamp}.txt`;
                
                const blob = new Blob([logText], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                console.log('✅ Download complete:', filename);
                showNotification(`Downloaded: ${data.totalLines.toLocaleString()} lines (${(data.fileSize / 1024).toFixed(1)} KB)`, 'success', 5000);
                
            } catch (error) {
                console.error('❌ Download error:', error);
                showNotification(`Download failed: ${error.message}`, 'error', 5000);
            } finally {
                downloadButton.html(originalText).prop('disabled', false);
            }
        };
          window.toggleConnection = function() {
            if (isConnected) {
                disconnect();
            } else {
                connect();
            }
        };        async function connect() {
            const orderId = $('#orderId').val().trim();
            
            if (!orderId) {
                hideLoadingOverlay();
                updateStatus('Order ID is required', 'disconnected');
                showNotification('Please enter an Order ID', 'warning');
                return;
            }
            
            if (!/^\d+$/.test(orderId)) {
                hideLoadingOverlay();
                updateStatus('Order ID must be numeric', 'disconnected');
                showNotification('Order ID must be a number', 'warning');
                return;
            }
              // Check for cached token first
            const cached = getCachedToken(orderId);
            if (cached && cached.token && cached.ws_url) {
                console.log('✅ Using cached token for order:', orderId);
                authToken = cached.token;
                wsUrl = cached.ws_url;
                tokenExpiry = cached.expiry;
                
                // Single loading message for cached token
                showLoadingOverlay('Connecting', 'Please Wait...');
                $('#connectBtn').html('<i class="las la-sync fa-spin"></i> Connecting...').prop('disabled', true);
                
                // Connect directly with cached credentials
                setTimeout(() => connectWebSocket(), 500);
                return;
            }
            
            // No cached token, fetch from server
            console.log('No valid cached token, fetching from server...');
            showLoadingOverlay('Authenticating', 'Requesting secure access...');
            $('#connectBtn').html('<i class="las la-sync fa-spin"></i> Connecting...').prop('disabled', true);
              try {
                // Step 1: Get secure token from backend
                const tokenResponse = await fetch(tokenEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order_id: orderId })
                });
                
                if (!tokenResponse.ok) {
                    const errorData = await tokenResponse.json();
                    throw new Error(errorData.message || 'Failed to get authentication token');
                }
                  const tokenData = await tokenResponse.json();
                
                console.log('Token response:', tokenData);
                
                if (!tokenData.success) {
                    throw new Error(tokenData.message || 'Failed to get authentication token');
                }
                
                authToken = tokenData.token;
                wsUrl = tokenData.ws_url;
                const expiresIn = tokenData.expires_in || 3600; // Default to 1 hour
                tokenExpiry = Date.now() + (expiresIn * 1000);
                
                // Cache the token for future use
                setCachedToken(orderId, authToken, wsUrl, expiresIn);
                  console.log('✅ Token received and cached:', authToken);
                console.log('WebSocket URL:', wsUrl);
                console.log('Token expires in:', expiresIn, 'seconds');
                
                // Update overlay message smoothly (no rapid changes)
                showLoadingOverlay('Connecting', 'Connecting to log server...');
                
                // Step 2: Connect directly to external WebSocket server
                setTimeout(() => connectWebSocket(), 500);
                
            } catch (error) {
                console.error('Connection error:', error);
                hideLoadingOverlay();
                updateStatus(`Connection failed: ${error.message}`, 'disconnected');
                showNotification(`Failed to connect: ${error.message}`, 'error');
                $('#connectBtn').html('<i class="las la-plug"></i> Connect').prop('disabled', false);
                
                // Clear any cached token on error
                clearCachedToken(orderId);
                
                // Show reconnect overlay if we had an order ID
                if (orderId) {
                    showReconnectOverlay(`Failed to connect: ${error.message}`);
                }
            }
        }function connectWebSocket() {
            updateStatus('Connecting to log server...', 'connecting');
            console.log('=== Starting WebSocket Connection ===');
            
            if (!wsUrl) {
                console.error('WebSocket URL is not set!');
                updateStatus('WebSocket URL not available', 'disconnected');
                disconnect();
                return;
            }
            
            if (!authToken) {
                console.error('Auth token is not set!');
                updateStatus('Authentication token not available', 'disconnected');
                disconnect();
                return;
            }
            
            console.log('Creating WebSocket connection to:', wsUrl);
            ws = new WebSocket(wsUrl);
            
            const connectionTimeout = setTimeout(() => {
                if (ws && ws.readyState === WebSocket.CONNECTING) {
                    console.error('WebSocket connection timeout after 10 seconds');
                    ws.close();
                    updateStatus('Connection timeout', 'disconnected');
                    disconnect();
                }
            }, 10000);            ws.onopen = function() {
                clearTimeout(connectionTimeout);
                console.log('WebSocket connection opened successfully');
                console.log('WebSocket ready state:', ws.readyState);
                reconnectAttempts = 0;
                
                // No overlay update here - keep showing "Connecting" message
                
                // Send authentication with token (matching dash-ext.html format)
                const authMessage = {
                    type: 'authenticate',
                    token: authToken
                };
                console.log('Sending authentication message:', authMessage);
                ws.send(JSON.stringify(authMessage));
            };
            
            ws.onmessage = function(event) {
                console.log('WebSocket message received:', event.data);
                try {
                    const message = JSON.parse(event.data);
                    console.log('Parsed message:', message);
                    handleMessage(message);
                } catch (error) {
                    console.error('Error parsing message:', error);
                    console.log('Raw message data:', event.data);
                    // If it's not JSON, it might be a plain log line
                    if (isConnected) {
                        addLogLine(event.data, true);
                    }
                }
            };
            
            ws.onerror = function(error) {
                clearTimeout(connectionTimeout);
                console.error('WebSocket error occurred:', error);
                console.log('WebSocket ready state:', ws.readyState);
                updateStatus('Connection error', 'disconnected');
                scheduleReconnect();
            };
              ws.onclose = function(event) {
                clearTimeout(connectionTimeout);
                console.log('WebSocket closed - Code:', event.code, 'Reason:', event.reason, 'Clean:', event.wasClean);
                
                const orderId = $('#orderId').val().trim();
                
                // Check if closure is due to authentication failure
                // Code 1008 = Policy Violation (usually auth failures)
                // Code 1002 = Protocol Error
                const isAuthError = event.code === 1008 || 
                                   (event.reason && (
                                       event.reason.toLowerCase().includes('auth') ||
                                       event.reason.toLowerCase().includes('invalid') ||
                                       event.reason.toLowerCase().includes('expired') ||
                                       event.reason.toLowerCase().includes('credentials')
                                   ));
                
                if (isAuthError) {
                    console.error('⚠️ WebSocket closed due to authentication error');
                    
                    // Clear cached token since it's invalid
                    if (orderId) {
                        clearCachedToken(orderId);
                        console.log('🗑️ Cleared invalid cached token');
                    }
                    
                    // Check if it's a token expiration issue
                    const isTokenExpired = event.reason && (
                        event.reason.toLowerCase().includes('expired') ||
                        event.reason.toLowerCase().includes('token')
                    );
                    
                    if (isTokenExpired && orderId) {
                        // Automatically regenerate expired token
                        console.log('⚠️ Token expired, automatically regenerating...');
                        cleanup();
                        showLoadingOverlay('Token Expired', 'Regenerating authentication token...');
                        showNotification('Token expired, regenerating...', 'warning', 3000);
                        
                        setTimeout(() => {
                            console.log('🔄 Attempting to reconnect with fresh token...');
                            reconnectAttempts = 0;
                            connect();
                        }, 1500);
                        return;
                    } else {
                        // Other auth errors - show reconnect overlay
                        cleanup();
                        hideLoadingOverlay();
                        updateStatus(`Authentication failed: ${event.reason || 'Invalid credentials'}`, 'disconnected');
                        showNotification(event.reason || 'Authentication failed', 'error');
                        showReconnectOverlay(event.reason || 'Authentication failed. Please try again.');
                        return;
                    }
                }
                
                // Normal disconnection handling
                if (isConnected) {
                    updateStatus('Connection lost', 'disconnected');
                    scheduleReconnect();
                } else {
                    updateStatus('Connection closed', 'disconnected');
                }
                
                cleanup();
            };
        }          function handleMessage(message) {
            console.log('Handling message type:', message.type || message.action);
            console.log('Full message:', message);
              switch (message.type || message.action) {
                case 'authenticated':
                case 'auth_success':
                    console.log('Authentication successful!');
                    // No overlay update - let it continue showing "Connecting"
                    
                    // Send subscribe message with order ID (matching dash-ext.html)
                    ws.send(JSON.stringify({
                        type: 'subscribe',
                        orderId: $('#orderId').val()
                    }));
                    break;
                  
                case 'subscribed':
                    console.log('Subscribed to order logs successfully!');
                    // Smooth transition to content
                    hideLoadingOverlay();
                    isConnected = true;
                    connectionStartTime = Date.now();
                    timeInterval = setInterval(updateConnectionTime, 1000);
                    $('#connectBtn').html('<i class="las la-plug"></i> Disconnect').prop('disabled', false);
                    
                    // Single notification after everything is ready
                    setTimeout(() => {
                        showNotification('Successfully connected to log stream!', 'success', 4000);
                    }, 600);
                    break;                case 'auth_failed':
                case 'error':
                    console.error('Authentication failed or error:', message.message);
                    
                    const orderId = $('#orderId').val().trim();
                    const errorMessage = (message.message || '').toLowerCase();
                    
                    // Check if it's a token expiration error
                    const isTokenExpired = errorMessage.includes('expired') || 
                                          errorMessage.includes('invalid token') ||
                                          errorMessage.includes('token expired');
                    
                    if (isTokenExpired && orderId) {
                        console.log('⚠️ Token expired, automatically regenerating...');
                        
                        // Clear expired cached token
                        clearCachedToken(orderId);
                        console.log('Cleared expired cached token');
                        
                        // Cleanup current connection
                        cleanup();
                        
                        // Show regeneration message
                        showLoadingOverlay('Token Expired', 'Regenerating authentication token...');
                        showNotification('Token expired, regenerating...', 'warning', 3000);
                        
                        // Automatically reconnect with new token (small delay for smooth UX)
                        setTimeout(() => {
                            console.log('🔄 Attempting to reconnect with fresh token...');
                            reconnectAttempts = 0; // Reset reconnect counter
                            connect();
                        }, 1500);
                    } else {
                        // Other authentication errors - show reconnect overlay
                        hideLoadingOverlay();
                        
                        if (orderId) {
                            clearCachedToken(orderId);
                            console.log('Cleared cached token due to auth failure');
                        }
                        
                        updateStatus(`Error: ${message.message || 'Authentication failed'}`, 'disconnected');
                        showNotification(message.message || 'Authentication failed', 'error');
                        showReconnectOverlay(message.message || 'Authentication failed. Please try again.');
                        disconnect();
                    }
                    break;
                    
                case 'initial_content':
                    console.log('Received initial content, lines:', message.lines ? message.lines.length : 0);
                    // Clear and load initial log history
                    const logContent = $('#logContent');
                    logContent.html(''); // Clear completely
                    totalLinesCount = 0;
                    newLinesCount = 0;
                    
                    if (message.lines && Array.isArray(message.lines)) {
                        message.lines.forEach(line => addLogLine(line, false));
                        updateStatus(`✅ Loaded ${message.lines.length} initial log lines`, 'connected');
                    }
                    break;
                    
                case 'new_lines':
                    console.log('Received new lines, count:', message.lines ? message.lines.length : 0);
                    // New log lines received
                    if (message.lines && Array.isArray(message.lines)) {
                        message.lines.forEach(line => addLogLine(line, true));
                    }
                    break;
                    
                case 'log':
                case 'log_line':
                    console.log('Received single log line');
                    // Single new log line received
                    if (message.data || message.line) {
                        addLogLine(message.data || message.line, true);
                    }
                    break;
                    
                case 'initial_logs':
                case 'history':
                    console.log('Received log history, count:', message.lines ? message.lines.length : 0);
                    // Alternative initial log history format
                    const content = $('#logContent');
                    content.html('');
                    totalLinesCount = 0;
                    newLinesCount = 0;
                    
                    if (message.lines && Array.isArray(message.lines)) {
                        message.lines.forEach(line => addLogLine(line, false));
                        updateStatus(`✅ Loaded ${message.lines.length} log lines`, 'connected');
                    }
                    break;
                    
                default:
                    console.warn('Unknown message type:', message.type || message.action);
                    console.log('Attempting to handle as plain log line');
                    // Try to handle as a plain log line
                    if (message.data || message.message) {
                        addLogLine(message.data || message.message, true);
                    }
            }
        }
          function scheduleReconnect() {
            if (reconnectAttempts >= maxReconnectAttempts) {
                hideLoadingOverlay();
                updateStatus(`Failed to reconnect after ${maxReconnectAttempts} attempts`, 'disconnected');
                showReconnectOverlay(`Failed to reconnect after ${maxReconnectAttempts} attempts. Click Reconnect to try again.`);
                return;
            }
            
            reconnectAttempts++;
            const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
            
            showLoadingOverlay('Reconnecting', `Reconnecting in ${Math.ceil(delay/1000)}s... (Attempt ${reconnectAttempts}/${maxReconnectAttempts})`);
            updateStatus(`Reconnecting in ${Math.ceil(delay/1000)}s... (${reconnectAttempts}/${maxReconnectAttempts})`, 'connecting');
            
            reconnectTimeout = setTimeout(() => {
                if (!isConnected) {
                    connect();
                }
            }, delay);
        }        function disconnect() {
            cleanup();
            updateStatus('Disconnected - Click Connect to start monitoring', 'disconnected');
            showNotification('Disconnected from log stream', 'info');
        }        function cleanup() {
            if (ws) {
                ws.close();
                ws = null;
            }
            
            isConnected = false;
            connectionStartTime = null;
            authToken = null;
            wsUrl = null;
            tokenExpiry = null;
            
            // Don't clear cached token here - let it expire naturally
            // Token will be auto-regenerated if expired when reconnecting
            // Only clear if explicitly needed (auth failures already do this)
            
            if (timeInterval) {
                clearInterval(timeInterval);
                timeInterval = null;
            }
            
            if (reconnectTimeout) {
                clearTimeout(reconnectTimeout);
                reconnectTimeout = null;
            }
            
            $('#connectBtn').html('<i class="las la-plug"></i> Connect').prop('disabled', false);
            $('#connectionTime').text('00:00:00');
        }
        
        // Keyboard shortcuts
        $(document).on('keydown', function(event) {
            if (event.ctrlKey || event.metaKey) {
                switch (event.key) {
                    case 'Enter':
                        event.preventDefault();
                        toggleConnection();
                        break;
                    case 'l':
                        event.preventDefault();
                        clearLogs();
                        break;
                    case 'f':
                        event.preventDefault();
                        $('#searchInput').focus();
                        break;
                    case 's':
                        event.preventDefault();
                        exportLogs();
                        break;
                }
            }
            
            if (event.key === 'Escape') {
                $('#searchInput').val('');
                filterLogs();
            }
        });
        
        // Double-click to copy
        $(document).on('dblclick', '.log-line', function() {
            const text = $(this).text().trim();
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    $(this).css('background', 'rgba(76,175,80,0.3)');
                    setTimeout(() => {
                        $(this).css('background', '');
                    }, 500);
                    showNotification('Copied to clipboard', 'info', 1000);
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            }
        });        // Initialize
        $(document).ready(function() {
            console.log('=== Bot Logs Dashboard Initialized ===');
            updateStats();
            
            const orderId = $('#orderId').val().trim();
            console.log('Order ID from page load:', orderId);
            
            if (orderId && /^\d+$/.test(orderId)) {
                // Auto-connect if order ID is present with smooth delay
                console.log('Order ID detected, auto-connecting in 800ms...');
                setTimeout(() => {
                    connect();
                }, 800); // Longer delay for smoother initial experience
            } else {
                // No order ID, show ready state smoothly
                console.log('No order ID detected, showing ready state');
                setTimeout(() => {
                    hideLoadingOverlay();
                }, 500);
            }
        });
        
        $(window).on('beforeunload', cleanup);
        
    })(jQuery);
</script>
@endpush
