<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swagger UI</title>
    <link rel="stylesheet" href="{{ asset('public/swagger-ui/dist/swagger-ui.css') }}">
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="{{ asset('public/swagger-ui/dist/swagger-ui-bundle.js') }}"></script>
    <script src="{{ asset('public/swagger-ui/dist/swagger-ui-standalone-preset.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ui = SwaggerUIBundle({
                url: "{{ asset('storage/api-docs/api-docs.json') }}",
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIBundle.SwaggerUIStandalonePreset
                ],
                // layout: "StandaloneLayout"
            });
        });
    </script>
</body>
</html>
