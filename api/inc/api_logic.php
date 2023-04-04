<?php
class api_logic
{
    private $endpoint;
    private $params;

    // ------------------------------------------------------
    public function __construct($endpoint, $params = null)
    {
        // define the object/class properties
        $this->endpoint = $endpoint;
        $this->params = $params;
    }

    // ------------------------------------------------------
    public function endpoint_exists()
    {
        // check if endpoint is a valid method
        return method_exists($this, $this->endpoint);
    }
    // ------------------------------------------------------
    public function error_response($message)
    {
        return [
            'status' => 'ERROR',
            'message' => $message,
            'results' => []
        ];
    }






    // ------------------------------------------------------
    // ENDPOINTS
    // ------------------------------------------------------

    public function status()
    {
        return [
            'status' => 'SUCCESS',
            'message' => 'API is running ok',
            'results' => [],
        ];
    }

    public function get_totals()
    {
        // return total clients and products
        $db = new database();
        $results = $db->EXE_QUERY("
            SELECT 'Clientes', COUNT(*) Total FROM clientes WHERE deleted_at IS NULL UNION ALL
            SELECT 'Produtos', COUNT(*) Total FROM produtos WHERE deleted_at IS NULL");

        return [
            'status' => 'ERROR',
            'message' => '',
            'results' => $results
        ];
    }

    // ############################################################################
    // ------------------------------------------------------
    // CLIENTES
    // ------------------------------------------------------
    public function get_all_clients()
    {
        $db = new database();
        $results = $db->EXE_QUERY('SELECT * FROM clientes');

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------
    public function get_all_active_clients()
    {
        $db = new database();
        $results = $db->EXE_QUERY('SELECT * FROM clientes WHERE deleted_at IS NULL');

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------
    public function get_all_inactive_clients()
    {
        $db = new database();
        $results = $db->EXE_QUERY('SELECT * FROM clientes WHERE deleted_at IS NOT NULL');

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------
    public function get_client()
    {
        $sql = "SELECT * FROM clientes WHERE  1";

        if (key_exists('id', $this->params)) {
            if (filter_var($this->params['id'], FILTER_VALIDATE_INT)) {
                $sql .= " AND id_cliente = " . intval($this->params['id']);
            }
        } else {
            return $this->error_response('ID client is required');
        }

        $db = new database();
        $results = $db->EXE_QUERY($sql);

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------
    public function create_new_client()
    {
        // create a new client
        // check if all data are provided
        if (
            !key_exists('nome', $this->params) ||
            !key_exists('email', $this->params) ||
            !key_exists('telefone', $this->params)
        ) {
            return $this->error_response('All client data are required');
        }


        $params = [
            ':email' => $this->params['email'],
        ];

        $db = new database();
        // check if client already exists
        $results = $db->EXE_QUERY("SELECT id_cliente FROM clientes WHERE
                                    1 
                                    AND email = :email
                                    AND deleted_at IS NULL", $params);

        // if client already exists
        if (count($results) > 0) {
            return $this->error_response('Email already exists');
        }

        // --------------------------------------------------------
        $params = [
            ':nome' => $this->params['nome'],
            ':email' => $this->params['email'],
            ':telefone' => $this->params['telefone']
        ];
        $db->EXE_QUERY('INSERT INTO clientes VALUES (0, :nome, :email, :telefone, NOW(), NOW(), NULL)', $params);

        return [
            'status' => 'SUCCESS',
            'message' => 'Client created successfully',
            'results' => []
        ];
    }

    // ------------------------------------------------------
    public function update_client()
    {
        // update client
        // check if all data are provided
        if (
            !key_exists('id_cliente', $this->params) ||
            !key_exists('nome', $this->params) ||
            !key_exists('email', $this->params) ||
            !key_exists('telefone', $this->params)
        ) {
            return $this->error_response('All client data are required');
        }

        $params = [
            ':id_cliente' => $this->params['id_cliente'],
            ':email' => $this->params['email'],
        ];


        $db = new database();
        // check if client already exists
        $results = $db->EXE_QUERY("SELECT id_cliente FROM clientes
                                    WHERE 1 
                                    AND id_cliente <> :id_cliente
                                    AND email = :email
                                    AND deleted_at IS NULL", $params);

        // if client already exists
        if (count($results) > 0) {
            return $this->error_response('Another client is already using this email');
        }

        // --------------------------------------------------------
        $params = [
            ':id_cliente' => $this->params['id_cliente'],
            ':nome' => $this->params['nome'],
            ':email' => $this->params['email'],
            ':telefone' => $this->params['telefone']
        ];

        $db->EXE_NON_QUERY('UPDATE clientes 
                            SET nome = :nome,
                                email = :email,
                                telefone = :telefone,
                                updated_at = NOW()
                            WHERE id_cliente = :id_cliente', $params);

        return [
            'status' => 'SUCCESS',
            'message' => 'Client updated successfully',
            'results' => []
        ];
    }

    // ------------------------------------------------------    
    public function delete_client()
    {
        // delete client
        // check if all data are provided
        if (
            !key_exists('id', $this->params)
        ) {
            return $this->error_response('Id is required');
        }

        // make soft delete
        $params = [
            ':id_cliente' => $this->params['id'],
        ];
        $db = new database();
        $db->EXE_NON_QUERY("UPDATE clientes SET deleted_at = NOW() WHERE id_cliente = :id_cliente", $params);

        return [
            'status' => 'SUCCESS',
            'message' => 'Client deleted successfully',
            'results' => []
        ];
    }

    // ############################################################################
    // ------------------------------------------------------
    // PRODUTOS
    // ------------------------------------------------------    
    public function get_all_products()
    {
        // returns all products from our database
        $db = new database();
        $results = $db->EXE_QUERY("SELECT * FROM produtos");

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------    
    public function get_all_active_products()
    {
        // returns all active products from our database
        $db = new database();
        $results = $db->EXE_QUERY("SELECT * FROM produtos WHERE deleted_at IS NULL");

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------    
    public function get_all_inactive_products()
    {
        // returns all inactive products from our database
        $db = new database();
        $results = $db->EXE_QUERY("SELECT * FROM produtos WHERE deleted_at IS NOT NULL");

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------    
    public function get_all_products_withouth_stock()
    {
        // returns all products with quantidade <= 0 from our database
        $db = new database();
        $results = $db->EXE_QUERY("SELECT * FROM produtos WHERE deleted_at IS NULL AND quantidade <= 0");

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------
    // ------------------------------------------------------
    public function get_product()
    {
        $sql = "SELECT * FROM produtos WHERE  1";

        if (key_exists('id', $this->params)) {
            if (filter_var($this->params['id'], FILTER_VALIDATE_INT)) {
                $sql .= " AND id_produto = " . intval($this->params['id']);
            }
        } else {
            return $this->error_response('ID produto is required');
        }

        $db = new database();
        $results = $db->EXE_QUERY($sql);

        return [
            'status' => 'SUCCESS',
            'message' => '',
            'results' => $results
        ];
    }

    // ------------------------------------------------------
    public function create_new_product()
    {
        // create a new product
        // check if all data are provided
        if (
            !key_exists('produto', $this->params) ||
            !key_exists('quantidade', $this->params)
        ) {
            return $this->error_response('All product data are required');
        }


        $params = [
            ':produto' => $this->params['produto']
        ];

        $db = new database();
        // check if product already exists
        $results = $db->EXE_QUERY('SELECT id_produto 
                                   FROM produtos 
                                   WHERE produto = :produto
                                   AND deleted_ad IS NULL', $params);

        // if product already exists
        if (count($results) > 0) {
            return $this->error_response('Product already exists');
        }

        // --------------------------------------------------------
        $params = [
            ':produto' => $this->params['produto'],
            ':quantidade' => $this->params['quantidade']
        ];
        $db->EXE_QUERY('INSERT INTO produtos VALUES (0, :produto, :quantidade, NOW(), NOW(), NULL)', $params);

        return [
            'status' => 'SUCCESS',
            'message' => 'Product created successfully',
            'results' => []
        ];
    }

    // ------------------------------------------------------
    public function update_product()
    {
        // update product
        // check if all data are provided
        if (
            !key_exists('id_produto', $this->params) ||
            !key_exists('produto', $this->params) ||
            !key_exists('quantidade', $this->params)
        ) {
            return $this->error_response('All product data are required');
        }

        $params = [
            ':id_produto' => $this->params['id_produto'],
            ':produto' => $this->params['produto'],
        ];

        $db = new database();
        // check if product already exists
        $results = $db->EXE_QUERY("SELECT id_produto FROM produtos
                                    WHERE 1 
                                    AND id_produto <> :id_produto
                                    AND produto = :produto
                                    AND deleted_at IS NULL", $params);

        // if product already exists
        if (count($results) > 0) {
            return $this->error_response('Product already exists');
        }

        // --------------------------------------------------------
        $params = [
            ':id_produto' => $this->params['id_produto'],
            ':produto' => $this->params['produto'],
            ':quantidade' => $this->params['quantidade']
        ];

        $db->EXE_NON_QUERY('UPDATE produtos 
                            SET produto = :produto,
                                quantidade = :quantidade,
                                updated_at = NOW()
                            WHERE id_produto = :id_produto', $params);


        return [
            'status' => 'SUCCESS',
            'message' => 'Produto updated successfully',
            'results' => []
        ];
    }

    // ------------------------------------------------------
    public function delete_product()
    {
        // delete product
        // check if all data are provided
        if (
            !key_exists('id', $this->params)
        ) {
            return $this->error_response('Id is required');
        }

        // make soft delete
        $params = [
            ':id_produto' => $this->params['id'],
        ];
        $db = new database();
        $db->EXE_NON_QUERY("UPDATE produtos SET deleted_at = NOW() WHERE id_produto = :id_produto", $params);

        return [
            'status' => 'SUCCESS',
            'message' => 'Product deleted successfully',
            'results' => []
        ];
    }
}
