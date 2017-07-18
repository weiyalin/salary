<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='csrf' content="{{ csrf_token() }}"/>
    <title>{{env('APP_NAME')}}</title>
    <!-- app theme -->
    <link type="text/css" href="{{ url('dist/css/element-theme/index.css') }}" rel="stylesheet"/>
    {{--<link href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">--}}
    <link href="{{ url('dist/css/ionicons.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ url('dist/css/app.css') }}"/>
    <link rel="stylesheet" href="{{ url('dist/css/main.css') }}"/>

    <script type="text/javascript" src="{{ url('dist/js/zfeedback.js') }}"></script>
    <script type="text/javascript" src="{{ url('dist/js/jquery.min.js') }}"></script>
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
<div id="app"></div>
</body>
<script>
    window.GammaApp = {
        app_name: "{{ env('APP_NAME') }}",
        company_name: "{{ env('COMPANY_NAME') }}",
        profile: {
            id: {{$profile->id}},
            name: "{{$profile->name}}",
            code: "{{$profile->code}}",
        }
    };
</script>
<script type="text/javascript" src="{{ url('dist/vendor.js') }}"></script>
<script type="text/javascript" src="{{ url('dist/app.js') }}"></script>
<script type="text/javascript" src="{{ url('dist/js/utils.js') }}"></script>
</html>
