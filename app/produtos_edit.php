<?php

// dependecies
require_once('./inc/config.php');
require_once('./inc/api_functions.php');
require_once('./inc/functions.php');

$error_message = '';
$success_message = '';

// STEP 1
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // verificar se o id do produto existe na query string
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: produtos.php');
    }
    // chamar a API para buscar o produto
    $produto = api_request('get_product', 'POST', ['id' => $_GET['id']])['data']['results'][0];
}

// STEP 2
// lógica e regras de negócio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produto = $_POST['id_produto'];
    $produto = $_POST['text_produto'];
    $quantidade = $_POST['text_quantidade'];

    $results = api_request('update_product', 'POST', [
        'id_produto' => $id_produto,
        'produto' => $produto,
        'quantidade' => $quantidade
    ]);

    // show request results
    if ($results['data']['status'] == 'ERROR') {
        $error_message = $results['data']['message'];
    } elseif ($results['data']['status'] == 'SUCCESS') {
        $success_message = $results['data']['message'];
    }

    // reload produto data
    $produto = api_request('get_product', 'POST', ['id' => $id_produto])['data']['results'][0];
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/bootstrap/bootstrap.min.css">

    <title>App consumidora - Editar produto</title>
</head>

<body>

    <?php
    include('inc/nav.php');
    ?>

    <section class="container">

        <div class="row my-5">
            <div class="col-sm-6 offset-sm-3 bg-light p-4">

                <form action="produtos_edit.php" method="post">

                    <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">

                    <div class="mb-3">
                        <label>Nome do produto:</label>
                        <input type="text" name="text_produto" class="form-control" value="<?= $produto['produto'] ?>">
                    </div>
                    <div class="mb-3">
                        <label>Quantidade:</label>
                        <input type="number" name="text_quantidade" class="form-control" value="<?= $produto['quantidade'] ?>">
                    </div>
                    <div class="mb-3 text-center">
                        <a href="produtos.php" class="btn btn-secondary btn-sm">Cancelar</a>
                        <input type="submit" value="Salvar" class="btn btn-primary btn-sm">
                    </div>

                    <?php if (!empty($error_message)) : ?>
                        <div class="alert alert-danger p-2 text-center">
                            <?= $error_message ?>
                        </div>
                    <?php elseif (!empty($success_message)) : ?>
                        <div class="alert alert-success p-2 text-center">
                            <?= $success_message ?>
                        </div>
                    <?php endif; ?>
                </form>

            </div>
        </div>

    </section>

    <script src="./assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>