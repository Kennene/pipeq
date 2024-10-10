<!DOCTYPE html>
<html lang="pl">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/user.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
</head>
<body>
    
    User

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Echo.channel('chat');
    });

</script>

</html>