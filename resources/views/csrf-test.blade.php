<!DOCTYPE html>
<html>
<head>
    <title>CSRF Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>CSRF Test</h1>
    
    <div id="result"></div>
    
    <form id="csrf-form" method="POST" action="/csrf-test">
        @csrf
        <button type="submit">Test CSRF Token</button>
    </form>
    
    <script>
        document.getElementById('csrf-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/csrf-test', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('result').innerHTML = 
                    '<div style="color: green; margin-top: 10px;">' + data.message + '</div>';
            })
            .catch(error => {
                document.getElementById('result').innerHTML = 
                    '<div style="color: red; margin-top: 10px;">Error: ' + error.message + '</div>';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
