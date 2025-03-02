<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/bootstrap/node_modules/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="/assets/Styles/index.css">
    <title>
        <?= $title ?? "Task Manager" ?>
    </title>

</head>

<body>

    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">Task Manage</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>

                </ul>
            </div>
        </div>
    </nav>
    <!-- Header -->


    <!-- Content -->
    <div class="container my-5">
        <?= $content ?>
    </div>
    <!-- Content -->

    <script src="/assets/bootstrap/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>