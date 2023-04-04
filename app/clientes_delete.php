<?php

// dependecies
require_once('./inc/config.php');
require_once('./inc/api_functions.php');
require_once('./inc/functions.php');

// lógica e regras de negócio

// check if id exists or id is empty
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: clientes.php');
    exit;
}

$id_cliente = $_GET['id'];

// check delete confirmation
if ((isset($_GET['confirm']) || !empty($_GET['confirm'])) && $_GET['confirm'] == 'true') {
    $results = api_request('delete_client', 'GET', ['id' => $id_cliente]);
    header("Location: clientes.php");
    exit;
}


// get client
$results = api_request('get_client', 'GET', ['id' => $id_cliente]);

// if client not found
if (count($results['data']['results']) == 0) {
    header('Location: clientes.php');
    exit;
}

if ($results['data']['status'] == 'SUCCESS') {
    $cliente = $results['data']['results'][0];
} else {
    $cliente = [];
}

if (empty($cliente)) {
    header('Location: clientes.php');
    exit;
}

//printData($cliente);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/bootstrap/bootstrap.min.css">

    <title>App consumidora - clientes</title>
</head>

<body>

    <?php
    include('inc/nav.php');
    ?>

    <section class="container">
        <div class="col p-5">
            <h5 class="text-center">
                Deseja excluir o cliente <strong> <?= $cliente['nome'] ?></strong>?
            </h5>
            <div class="text-center mt-3">
                <a href="clientes.php" class="btn btn-primary btn-sm">Cancelar</a>
                <a href="clientes_delete.php?id=<?= $cliente['id_cliente'] ?>&confirm=true" class="btn btn-danger btn-sm">Excluir</a>
            </div>
        </div>


    </section>

    <script src="./assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>