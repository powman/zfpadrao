
--
-- Estrutura da tabela `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `data` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `usuario_nome` varchar(255) NOT NULL,
  `descricao` varchar(400) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=420 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `modulo`
--

CREATE TABLE IF NOT EXISTS `modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `modulo` varchar(255) NOT NULL,
  `awsome` varchar(40) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `ordem` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Extraindo dados da tabela `modulo`
--

INSERT INTO `modulo` (`id`, `nome`, `modulo`, `awsome`, `status`, `ordem`) VALUES
(2, 'Controle de acesso', 'painel', 'users', 1, 1),
(3, 'Controle Geral', 'painel', 'code', 1, 1),
(11, 'Controle do Site', 'painel', 'suitcase', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `modulo_menu_sub`
--

CREATE TABLE IF NOT EXISTS `modulo_menu_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  `ctrl` varchar(45) DEFAULT NULL,
  `action` varchar(45) DEFAULT NULL,
  `modulo_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `ordem` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_modulo_menu_sub_modulo_menu1_idx` (`modulo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Extraindo dados da tabela `modulo_menu_sub`
--

INSERT INTO `modulo_menu_sub` (`id`, `nome`, `ctrl`, `action`, `modulo_id`, `status`, `ordem`) VALUES
(1, 'Usuário', 'ca-usuario', 'index', 2, 1, 0),
(2, 'Grupo', 'ca-usuario-grupo', 'index', 2, 1, 0),
(3, 'Modulo', 'cg-modulo', 'index', 3, 1, 0),
(4, 'Menu', 'cg-modulo-submenu', 'index', 3, 1, 1),
(7, 'Template', 'cs-template', 'index/id/1', 11, 1, 1),
(9, 'Paulo Henrique Pereira', 'modulo-submenu', 'index', 2, 1, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissao`
--

CREATE TABLE IF NOT EXISTS `permissao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `permissao` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_id` (`role_id`,`resource_id`,`permissao`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8086 ;

--
-- Extraindo dados da tabela `permissao`
--

INSERT INTO `permissao` (`id`, `role_id`, `resource_id`, `permissao`) VALUES
(8043, 2, 176, 'cadastrar-acao'),
(8042, 2, 176, 'cadastro-rapido'),
(8045, 2, 176, 'editar-acao'),
(8044, 2, 176, 'editar-rapido'),
(8041, 2, 176, 'excluir'),
(8039, 2, 176, 'index'),
(8040, 2, 176, 'listar'),
(8052, 2, 177, 'cadastrar-acao'),
(8051, 2, 177, 'cadastro-form'),
(8050, 2, 177, 'cadastro-rapido'),
(8055, 2, 177, 'editar-acao'),
(8054, 2, 177, 'editar-form'),
(8053, 2, 177, 'editar-rapido'),
(8049, 2, 177, 'excluir'),
(8046, 2, 177, 'index'),
(8047, 2, 177, 'listar'),
(8048, 2, 177, 'listar-permissao'),
(8061, 2, 178, 'cadastrar-acao'),
(8060, 2, 178, 'cadastro-form'),
(8059, 2, 178, 'cadastro-rapido'),
(8064, 2, 178, 'editar-acao'),
(8063, 2, 178, 'editar-form'),
(8062, 2, 178, 'editar-rapido'),
(8058, 2, 178, 'excluir'),
(8056, 2, 178, 'index'),
(8057, 2, 178, 'listar'),
(8070, 2, 179, 'cadastrar-acao'),
(8069, 2, 179, 'cadastro-form'),
(8072, 2, 179, 'editar-acao'),
(8071, 2, 179, 'editar-form'),
(8068, 2, 179, 'excluir'),
(8065, 2, 179, 'index'),
(8066, 2, 179, 'listar'),
(8067, 2, 179, 'pesquisar'),
(8082, 2, 180, 'cadastrar-acao'),
(8081, 2, 180, 'cadastro-form'),
(8080, 2, 180, 'cadastro-rapido'),
(8085, 2, 180, 'editar-acao'),
(8084, 2, 180, 'editar-form'),
(8083, 2, 180, 'editar-rapido'),
(8079, 2, 180, 'excluir'),
(8076, 2, 180, 'index'),
(8077, 2, 180, 'listar'),
(8078, 2, 180, 'pesquisar'),
(8075, 2, 181, 'cadastrar-acao'),
(8074, 2, 181, 'css'),
(8073, 2, 181, 'index'),
(7806, 3, 181, 'index');



-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo`
--

CREATE TABLE IF NOT EXISTS `grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `grupo`
--

INSERT INTO grupo (id, nome) VALUES (1, 'Anonimo');
INSERT INTO grupo (id, nome) VALUES (2, 'Registered');
INSERT INTO grupo (id, nome) VALUES (3, 'Admin');

-- --------------------------------------------------------

--
-- Estrutura da tabela `acl`
--

CREATE TABLE `acl` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `controller` varchar(100) NOT NULL,
  `action` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `controller` (`controller`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 
  CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;


-- --------------------------------------------------------

--
-- Estrutura da tabela `acl`
-- 
  
CREATE TABLE `acl_to_grupo` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `acl_id` int(10) NOT NULL,
  `grupo_id` tinyint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `acl_id` (`acl_id`),
  KEY `grupo_id` (`grupo_id`),
  CONSTRAINT `acl_to_grupo_ibfk_1` FOREIGN KEY (`acl_id`) 
     REFERENCES `acl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `acl_to_grupo_ibfk_2` FOREIGN KEY (`grupo_id`) 
     REFERENCES `grupo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL DEFAULT '',
  `senha` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `grupo_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `grupo_id` (`grupo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `senha`, `email`, `img`, `status`, `role_id`) VALUES
(2, 'Paulo', '0de8b41d3fa97119a9057e72601a9bcb', 'paulo@netsuprema.com.br', '', 1, 2),
(6, 'Lucas', '0de8b41d3fa97119a9057e72601a9bcb', 'lucas@netsuprema.com.br', '', 1, 3);


--
-- Limitadores para a tabela `modulo_menu_sub`
--
ALTER TABLE `modulo_menu_sub`
  ADD CONSTRAINT `modulo_menu_sub_ibfk_1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `permissao`
--
ALTER TABLE `permissao`
  ADD CONSTRAINT `permissao_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permissao_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `template_opcao`
--
ALTER TABLE `template_opcao`
  ADD CONSTRAINT `template_opcao_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
