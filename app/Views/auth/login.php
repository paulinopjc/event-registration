<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://accounts.google.com/gsi/client" async></script>
    </head>
    <body class="bg-light">
        <div class="container">
            <div class="row justify-content-center mt-5">
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="text-center mb-4">Admin Login</h3>

                            <div id="error-msg" class="alert alert-danger" style="display: none;"></div>

                            <div class="d-flex justify-content-center mb-3">
                                <div id="g_id_onload"
                                    data-client_id="<?= getenv('GOOGLE_CLIENT_ID') ?>"
                                    data-callback="handleCredentialResponse">
                                </div>
                                <div class="g_id_signin"
                                    data-type="standard"
                                    data-size="large"
                                    data-theme="outline"
                                    data-text="sign_in_with">
                                </div>
                            </div>

                            <p class="text-muted text-center small">
                                Only authorized emails can sign in.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function handleCredentialResponse(response) {
            fetch('/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ credential: response.credential })
            })
            .then(res => res.json())
            .then(data => {
                if (data.user) {
                    window.location.href = '/admin/dashboard';
                } else {
                    document.getElementById('error-msg').textContent = data.error || 'Login failed';
                    document.getElementById('error-msg').style.display = 'block';
                }
            });
        }
        </script>
    </body>
</html>