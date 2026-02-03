CREATE DATABASE sistema_vendas;

USE sistema_vendas;

-- Tabela de usuários
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT,
    nome VARCHAR(50),
    email VARCHAR(50) UNIQUE,
    senha VARCHAR(255),
    tipo_usuario ENUM('admin', 'operador') DEFAULT 'operador',
    data_cadastro DATETIME,
    PRIMARY KEY (id_usuario)
);

-- Tabela de sessões
CREATE TABLE sessoes (
    id_sessao INT AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_expiracao DATETIME,
    estado ENUM('ativa', 'desativada'),
    PRIMARY KEY (id_sessao),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabela de vendas
CREATE TABLE vendas (
    id_venda INT AUTO_INCREMENT,
    id_usuario INT,
    data_venda DATETIME,
    valor_total DECIMAL(8, 2),
    PRIMARY KEY (id_venda),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabela de logs
CREATE TABLE logs (
    id_log INT AUTO_INCREMENT,
    id_usuario INT,
    data_hora DATETIME,
    tipo_actividade VARCHAR(30),
    descricao TEXT,
    PRIMARY KEY (id_log),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabela de produtos
CREATE TABLE produtos (
    id_produto INT AUTO_INCREMENT,
    nome VARCHAR(30),
    categoria VARCHAR(30),
    preco DECIMAL(6, 2),
    quantidade_estoque INT,
    descricao TEXT,
    PRIMARY KEY (id_produto)
);

-- Tabela de itens da venda
CREATE TABLE itens_venda (
    id_item INT AUTO_INCREMENT,
    id_venda INT,
    id_produto INT,
    quantidade INT,
    preco_unitario DECIMAL(6, 2),
    PRIMARY KEY (id_item),
    FOREIGN KEY (id_venda) REFERENCES vendas(id_venda) ON DELETE CASCADE,
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE CASCADE
);

INSERT INTO produtos (nome, categoria, preco, quantidade_estoque, descricao) VALUES
-- Alimentos básicos
('Arroz China', 'Alimentos básicos', 2500.00, 500, 'Tio Antonio 25kg'),
('Arroz Superfino', 'Alimentos básicos', 2800.00, 400, 'Royal Umbrella 25kg'),
('Arroz Perfumado', 'Alimentos básicos', 2700.00, 100, 'Royal Aroma 25kg'),
('Farinha de Milho', 'Alimentos básicos', 1800.00, 300, 'Top Score 12.5kg'),
('Farinha de Trigo', 'Alimentos básicos', 1500.00, 350, 'Mariate 10kg'),
('Açúcar Branco', 'Alimentos básicos', 100.00, 600, 'Nacional 11kg'),
('Açúcar Castanho', 'Alimentos básicos', 100.00, 500, 'Nacional 1kg'),
('Massa Espaguete', 'Alimentos básicos', 40.00, 450, 'Bella 500g'),
('Óleo de Cozinha', 'Alimentos básicos', 350.00, 400, 'Fula 1L'),
('Óleo Vegetal', 'Alimentos básicos', 780.00, 350, 'Somol 5L'),
('Azeite', 'Alimentos básicos', 250.00, 120, 'Oliva Vir 750ml'),
('Sal Refinado', 'Alimentos básicos', 100.00, 700, 'Cisne 1kg'),
('Aromat Knorr 75g', 'Alimentos básicos', 65.00, 100, '75g Garrafa'),
('Aromat Knorr 200g', 'Alimentos básicos', 190.00, 100, '200g Garrafa'),
('Vinagre de Álcool', 'Alimentos básicos', 200.00, 250, 'Mariza 750ml'),

-- Laticínios e Ovos
('Leite Gordo', 'Laticínios e Ovos', 120.00, 200, 'Ultramel 1L'),
('Leite em Pó', 'Laticínios e Ovos', 240.00, 150, 'Cremora 1kg'),
('Queijo', 'Laticínios e Ovos', 120.00, 100, 'Parmalate 12 unid'),
('Manteiga', 'Laticínios e Ovos', 75.00, 120, 'Rama 250g'),
('Iogurte Natural', 'Laticínios e Ovos', 70.00, 150, 'Nestlé 500ml'),
('Ovos Brancos', 'Laticínios e Ovos', 400.00, 50, 'Cartela 30 unid'),
('Ovos Vermelhos', 'Laticínios e Ovos', 300.00, 50, 'Cartela 30 unid'),

-- Enlatados e Conservas
('Sardinha em Lata', 'Enlatados e Conservas', 350.00, 250, 'Ramirez 125g'),
('Atum em Lata', 'Enlatados e Conservas', 95.00, 180, 'Bom Amigo 120g'),
('Salsicha Enlatada', 'Enlatados e Conservas', 150.00, 120, 'Nacional 340g'),
('Ervilha em Conserva', 'Enlatados e Conservas', 150.00, 200, 'Compal 300g'),
('Milho em Conserva', 'Enlatados e Conservas', 150.00, 200, 'Green Giant 300g'),
('Molho de Tomate', 'Enlatados e Conservas', 180.00, 350, 'Guloso 520g'),
('Extrato de Tomate', 'Enlatados e Conservas', 120.00, 250, 'Compal 500g'),

-- Pães e Biscoitos
('Pão de Forma', 'Pães e Biscoitos', 140.00, 100, 'Panco 500g'),
('Pão de Forma Integral', 'Pães e Biscoitos', 160.00, 100, 'Panco 500g'),
('Biscoito Cream Cracker', 'Pães e Biscoitos', 500.00, 250, 'Bauducco 200g'),
('Biscoito de Chocolate e coco', 'Pães e Biscoitos', 130.00, 250, 'Romany Creams 200g'),
('Biscoito Maria', 'Pães e Biscoitos', 400.00, 300, 'DanCake 200g'),
('Biscoito Recheado', 'Pães e Biscoitos', 600.00, 200, 'Oreo 150g'),

-- Bebidas
('Refrigerante 500ml', 'Bebidas', 35.00, 1000, 'Coca-Cola company'),
('Refrigerante 1l', 'Bebidas', 60.00, 1000, 'Coca-Cola company'),
('Refrigerante 2l', 'Bebidas', 100.00, 1000, 'Coca-Cola company'),
('Sumo 350ml', 'Bebidas', 35.00, 1000, 'Cappy 350ml'),
('Sumo 1l', 'Bebidas', 70.00, 1000, 'Cappy 1l'),
('Água mineral 500ml', 'Bebidas', 25.00, 600, 'Namahacha 500ml'),
('Água mineral 1.5l', 'Bebidas', 60.00, 600, 'Namahacha 1.5l'),
('Água mineral 5l', 'Bebidas', 100.00, 600, 'Namahacha 5l'),
('Café Solúvel', 'Bebidas', 120.00, 150, 'Nescafé 200g'),
('Chá Preto', 'Bebidas', 180.00, 200, 'Five Rose 20 saquetas'),
('Cerveja 2M', 'Bebidas', 55.00, 500, 'Cervejas de Moçambique 550ml'),
('Cerveja Txilar', 'Bebidas', 50.00, 500, 'Cervejas de Moçambique 500ml'),

-- Doces e Guloseimas
('Chocolate Amargo', 'Doces e Guloseimas', 100.00, 200, 'Nestlé 100g'),
('Chocolate de Leite', 'Doces e Guloseimas', 120.00, 180, 'Milka 100g'),
('Chicletes Trident', 'Doces e Guloseimas', 90.00, 300, 'Trident 12 unid'),
('Achocolatado em Pó', 'Doces e Guloseimas', 240.00, 250, 'Milo 400g'),
('Leite Condensado', 'Doces e Guloseimas', 130.00, 200, 'Nestlé 395g'),

-- Produtos de Limpeza
('Sabão em Pó', 'Produtos de Limpeza', 260.00, 300, 'OMO 2kg'),
('Detergente Líquido', 'Produtos de Limpeza', 180.00, 400, 'Sunlight 750ml'),
('Amaciante de Roupas', 'Produtos de Limpeza', 250.00, 60, 'Staysoft 2l'),
('Desinfetante', 'Produtos de Limpeza', 200.00, 200, 'Pinho Sol 1L'),
('Água Sanitária', 'Produtos de Limpeza', 500.00, 250, 'Candida 1L'),
('Esponja de Limpeza', 'Produtos de Limpeza', 150.00, 500, 'EsfreBom 2 unid'),
('Sabão em Barra', 'Produtos de Limpeza', 300.00, 400, 'Minerva 200g'),

-- Produtos de Higiene
('Sabonete Lux', 'Produtos de Higiene', 150.00, 500, 'Lux 90g'),
('Sabonete Dove', 'Produtos de Higiene', 300.00, 400, 'Dove 90g'),
('Shampoo Anticaspa', 'Produtos de Higiene', 900.00, 200, 'Head & Shoulders 400ml'),
('Pasta de Dente', 'Produtos de Higiene', 55.00, 350, 'Colgate 90g'),
('Lenços Umedecidos', 'Produtos de Higiene', 150.00, 60, 'Wipes 50 unids'),
('Papel Higiênico', 'Produtos de Higiene', 45.00, 300, 'Fofinho 12 rolos'),
('Desodorante', 'Produtos de Higiene', 180.00, 250, 'Nivea 50ml'),
('Desodorante Aerosol', 'Produtos de Higiene', 900.00, 250, 'Rexona 150ml');