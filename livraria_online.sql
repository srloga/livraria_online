-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16-Set-2025 às 00:32
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `livraria_online`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `price`, `category_id`, `subcategory_id`, `image_path`, `stock`, `created_at`, `featured`) VALUES
(3, 'O Senhor dos Anéis', 'J.R.R. Tolkien', 'Clássico da fantasia épica.', 89.90, 1, 1, 'assets/uploads/lotr.jpg', 5, '2025-09-13 16:44:16', 0),
(4, 'Harry Potter e a Pedra Filosofal', 'J.K. Rowling', 'O início da jornada de Harry Potter no mundo da magia.', 69.90, 1, 1, 'assets/uploads/harrypotter1.jpg', 12, '2025-09-13 16:44:16', 0),
(5, 'As Crônicas de Nárnia', 'C.S. Lewis', 'Aventuras em um mundo mágico acessado por um guarda-roupa.', 64.90, 1, 1, 'assets/uploads/narnia.jpg', 8, '2025-09-13 16:44:16', 0),
(6, 'A Roda do Tempo - O Olho do Mundo', 'Robert Jordan', 'Primeiro volume da saga A Roda do Tempo.', 84.50, 1, 1, 'assets/uploads/rodadotempo.jpg', 6, '2025-09-13 16:44:16', 0),
(7, 'Mistborn: O Império Final', 'Brandon Sanderson', 'Um mundo onde cinzas caem do céu e a magia é feita com metais.', 79.00, 1, 1, 'assets/uploads/mistborn.jpg', 7, '2025-09-13 16:44:16', 0),
(8, 'Duna', 'Frank Herbert', 'Ficção científica com política e ecologia.', 74.50, 1, 2, 'assets/uploads/dune.jpg', 4, '2025-09-13 16:44:16', 0),
(9, 'Neuromancer', 'William Gibson', 'Um marco do cyberpunk, explorando IA e realidade virtual.', 72.00, 1, 2, 'assets/uploads/neuromancer.jpg', 6, '2025-09-13 16:44:16', 0),
(10, 'Fundação', 'Isaac Asimov', 'A queda e renascimento de impérios galácticos.', 85.00, 1, 2, 'assets/uploads/fundacao.jpg', 5, '2025-09-13 16:44:16', 0),
(11, '1984', 'George Orwell', 'Um clássico distópico sobre vigilância e controle.', 58.90, 1, 2, 'assets/uploads/1984.jpg', 10, '2025-09-13 16:44:16', 0),
(12, 'Eu, Robô', 'Isaac Asimov', 'Coleção de contos que definiu as leis da robótica.', 62.00, 1, 2, 'assets/uploads/eurobo.jpg', 9, '2025-09-13 16:44:16', 0),
(13, 'Aprendendo PHP', 'Anderson Hofelmann', 'Livro introdutório sobre PHP.', 59.90, 2, 3, 'assets/uploads/php.jpg', 10, '2025-09-13 16:44:16', 0),
(14, 'Clean Code', 'Robert C. Martin', 'Práticas para escrever código limpo.', 79.90, 2, 3, 'assets/uploads/cleancode.jpg', 7, '2025-09-13 16:44:16', 0),
(15, 'JavaScript: The Good Parts', 'Douglas Crockford', 'Os melhores recursos e práticas do JavaScript.', 65.90, 2, 3, 'assets/uploads/js_goodparts.jpg', 9, '2025-09-13 16:44:16', 0),
(16, 'Estruturas de Dados e Algoritmos com JavaScript', 'Loiane Groner', 'Abordagem prática de algoritmos para devs.', 78.50, 2, 3, 'assets/uploads/estruturas_js.jpg', 7, '2025-09-13 16:44:16', 0),
(17, 'Design Patterns: Elements of Reusable Object-Oriented Software', 'Erich Gamma et al.', 'Clássico dos padrões de projeto.', 92.00, 2, 3, 'assets/uploads/designpatterns.jpg', 5, '2025-09-13 16:44:16', 0),
(18, 'Engenharia de Software Moderna', 'Ian Sommerville', 'Fundamentos de engenharia de software.', 69.90, 2, 4, 'assets/uploads/engsoft.jpg', 6, '2025-09-13 16:44:16', 0),
(19, 'Introdução à Engenharia de Software', 'Sérgio Guerreiro', 'Conceitos básicos de hardware e software.', 82.00, 2, 4, 'assets/uploads/engcomp.jpg', 5, '2025-09-13 16:44:16', 0),
(20, 'Sistemas Operacionais Modernos', 'Andrew S. Tanenbaum', 'Funcionamento de sistemas operacionais.', 94.90, 2, 4, 'assets/uploads/sisop.jpg', 4, '2025-09-13 16:44:16', 0),
(21, 'Arquitetura de Computadores', 'John L. Hennessy', 'Projetos e fundamentos da arquitetura de computadores.', 88.00, 2, 4, 'assets/uploads/arqcomp.jpg', 5, '2025-09-13 16:44:16', 0),
(22, 'Banco de Dados: Projeto e Implementação', 'Felipe Nery Rodrigues Machado', 'Fundamentos de bancos de dados relacionais.', 91.00, 2, 4, 'assets/uploads/bancodados.jpg', 5, '2025-09-13 16:44:16', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`) VALUES
(1, 3, '2025-09-13 17:49:02'),
(2, 4, '2025-09-14 10:53:41'),
(3, 5, '2025-09-15 20:56:34');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `book_id`, `quantity`) VALUES
(1, 1, 3, 1),
(2, 1, 4, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Ficção', 'Livros de ficção científica e fantasia', '2025-09-13 16:22:41'),
(2, 'Técnicos', 'Livros técnicos e de programação', '2025-09-13 16:22:41');

-- --------------------------------------------------------

--
-- Estrutura da tabela `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Lucas', 'lucas123@test.com', 'Hello World!', '2025-09-14 13:44:32');

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `address`, `phone`, `payment_method`, `created_at`) VALUES
(1, 4, 159.80, 'enviado', 'Rua do Vau, Nº21', '999777555', 'cartao', '2025-09-14 14:49:48'),
(2, 4, 89.90, 'cancelado', '1', '1', 'cartao', '2025-09-14 16:32:58'),
(3, 4, 225.50, 'pendente', 'Rua da Fontella, º112', '123456789', 'cartao', '2025-09-15 20:01:26'),
(4, 5, 124.80, 'enviado', 'Rua do Centenário Nº 1789', '654789321', 'paypal', '2025-09-15 21:36:21');

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(1, 1, 3, 1, 89.90),
(2, 1, 4, 1, 69.90),
(3, 2, 3, 1, 89.90),
(4, 3, 9, 1, 72.00),
(5, 3, 8, 1, 74.50),
(6, 3, 7, 1, 79.00),
(7, 4, 5, 1, 64.90),
(8, 4, 13, 1, 59.90);

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `gateway` varchar(50) NOT NULL,
  `status` varchar(32) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(128) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `reviews`
--

INSERT INTO `reviews` (`id`, `book_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 4, 5, 'Melhor livro!!', '2025-09-14 11:00:52');

-- --------------------------------------------------------

--
-- Estrutura da tabela `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`, `description`, `created_at`) VALUES
(1, 1, 'Fantasia', 'Livros de fantasia e mundos mágicos', '2025-09-13 16:44:16'),
(2, 1, 'Ficção Científica', 'Futuros distópicos e tecnologia', '2025-09-13 16:44:16'),
(3, 2, 'Programação', 'Linguagens e desenvolvimento', '2025-09-13 16:44:16'),
(4, 2, 'Engenharia', 'Tópicos de engenharia moderna', '2025-09-13 16:44:16');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_admin`, `created_at`) VALUES
(1, 'admin', 'admin@livraria.com', '$2y$10$lTYgIfSpeOrrbFWGiNSbPOsWutyUT5kDHEJCOI5qsc0Wkypt5euea', 1, '2025-09-13 16:22:41'),
(3, 'usuario1', 'usuario1@test.com', '123456', 0, '2025-09-13 17:48:35'),
(4, 'usuario2', 'usuario2@test.com', '$2y$10$aFgCzUnumFyv3xI2hPYAl.bBz3xMa3c/4u/eLPxtkhNUrvwi2W/tW', 0, '2025-09-14 10:49:51'),
(5, 'usuario3', 'usuario3@test.com', '$2y$10$MABQMKEidLy5lPFBspdTbuwUGEqOw9uc6b/7m1k2DrOmYjNVuJ3Dq', 0, '2025-09-15 20:44:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `book_id`, `created_at`) VALUES
(1, 5, 3, '2025-09-15 20:54:41');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Índices para tabela `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Índices para tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Índices para tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token_hash` (`token_hash`);

--
-- Índices para tabela `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_book` (`user_id`,`book_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limitadores para a tabela `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Limitadores para a tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
