<?php
// Credenciais do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'livraria_online');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL', 'http://localhost/livraria_online/');
define('SITE_NAME', 'Livraria Online');

// Função para criar a conexão PDO
function db_connect() {
    try {
        return new PDO(
            'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        die('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }
}
