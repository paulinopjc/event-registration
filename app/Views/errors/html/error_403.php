<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>403 Forbidden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5 text-center">
        <h1 class="display-4 text-danger">403</h1>
        <p class="lead"><?= esc($message ?? 'You do not have permission to access this page.') ?></p>
        <a href="/admin/dashboard" class="btn btn-primary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
