<!DOCTYPE html>
<html>
<head>
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Weather Information</h1>
        
        <form method="post" action="/search">
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="city" placeholder="Enter city name">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center">
                    <h2><?= $city ?></h2>
                    <?php if (!empty($icon)): ?>
                        <img src="https://openweathermap.org/img/wn/<?= $icon ?>.png" alt="Weather icon">
                    <?php endif; ?>
                    <h3><?= $temperature ?>°C</h3>
                    <p>Humidity: <?= $humidity ?>%</p>
                    <p><?= ucfirst($description) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>