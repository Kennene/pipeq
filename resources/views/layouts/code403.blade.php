<!DOCTYPE html>
<html lang="en">
<head>
    @extends('layouts.head')
    <title>Access Denied</title>

    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
        }
        .icon {
            font-size: 60px;
            color: var(--red);
        }
        .btn {
            background-color: var(--red);
        }
    </style>
</head>

<body style="background-color: var(--red);">
    <div class="container shadow" >
        <div class="icon">🚫</div>
        <h1 class="text-danger">Access Denied</h1>
        <p class="text-muted">You do not have permission to view this page.</p>
        <p id="countdown" class="text-muted"></p>
        <a href="/" class="btn mt-3" style="background-color: var(--red); color: var(--white);">Go to Homepage</a>
    </div>
    
    <script>
        let countdown = 5; // Seconds before redirect
        const countdownElement = document.getElementById("countdown");

        function updateCountdown() {
            countdownElement.textContent = `Redirecting in ${countdown} seconds...`;
            if (countdown === 0) {
                window.location.href = "/"; // Redirect to homepage
            } else {
                countdown--;
                setTimeout(updateCountdown, 1000);
            }
        }
        // updateCountdown();
    </script>
</body>
</html>
