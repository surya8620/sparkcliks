@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">                   
                    <!-- Support Board CSS and JS -->
                    <link href="{{ $supportBoardUrl }}/css/main.css" type="text/css" rel="stylesheet">
                    <link href="{{ $supportBoardUrl }}/css/articles.css" type="text/css" rel="stylesheet">
                    
                    <!-- Support Board Articles Container -->
                    <div id="sb-articles">
                        <?php
                        // Build URL with query parameters
                        $params = [];
                        if ($request->has('category')) $params['category'] = $request->category;
                        if ($request->has('article_id')) $params['article_id'] = $request->article_id;
                        if ($request->has('search')) $params['search'] = $request->search;
                        if ($request->has('lang')) $params['lang'] = $request->lang;
                        
                        $query = !empty($params) ? '?' . http_build_query($params) : '';
                        $url = $supportBoardUrl . '/include/articles.php' . $query;
                        
                        // Fetch and include Support Board content
                        echo @file_get_contents($url);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
