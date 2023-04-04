<?php

// dependecies
require_once('./inc/config.php');
require_once('./inc/api_functions.php');
require_once('./inc/functions.php');

// lógica e regras de negócio
$totais = api_request('get_totals', 'GET')['data']['results'];


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/bootstrap/bootstrap.min.css">

    <title>App consumidora</title>
</head>

<body>

    <?php
    include('inc/nav.php');
    ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-sm-6 text-center">
                <h5>Clientes: <strong><?= $totais[0]['Total'] ?></strong></h5>
            </div>
            <div class="col-sm-6 text-center">
                <h5>Produtos: <strong><?= $totais[1]['Total'] ?></strong></h5>
            </div>
        </div>
    </div>

    <script src="./assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>