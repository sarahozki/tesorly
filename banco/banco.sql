-- =========================================================
-- Schema corrigido - Sistema de Agendamento (Cabelereiro)
-- =========================================================

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- usuarios (clientes / login)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`usuarios` (
  `idusuarios` INT NOT NULL AUTO_INCREMENT,
  `user_nome` VARCHAR(255) NULL,
  `user_email` VARCHAR(255) NULL,
  `user_senha` VARCHAR(255) NULL,
  `user_telefone` CHAR(14) NULL,
  `user_fotos` VARCHAR(45) NULL,
  PRIMARY KEY (`idusuarios`),
  UNIQUE INDEX `user_email_UNIQUE` (`user_email` ASC) VISIBLE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- empresario (o salão / profissional)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`empresario` (
  `idempresario` INT NOT NULL AUTO_INCREMENT,
  `usuarios_idusuarios` INT NOT NULL,
  `empre_tipo` VARCHAR(255) NOT NULL,
  `empre_nicho` VARCHAR(50) NOT NULL DEFAULT 'cabeleireiro',
  `empre_dias_trab` VARCHAR(255) NOT NULL DEFAULT 'A definir',
  `empre_cidade` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idempresario`),

  UNIQUE INDEX `usuarios_idusuarios_UNIQUE` (`usuarios_idusuarios` ASC) VISIBLE,

  INDEX `fk_empresario_usuarios_idx` (`usuarios_idusuarios` ASC) VISIBLE,

  CONSTRAINT `fk_empresario_usuarios`
    FOREIGN KEY (`usuarios_idusuarios`)
    REFERENCES `mydb`.`usuarios` (`idusuarios`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

) ENGINE = InnoDB;

-- -----------------------------------------------------
-- produtos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`produtos` (
  `idprodutos` INT NOT NULL AUTO_INCREMENT,
  `prod_nome` VARCHAR(255) NULL,
  `prod_marca` VARCHAR(255) NULL,
  `prod_fotos` VARCHAR(45) NULL,
  `empresario_idempresario` INT NOT NULL,
  PRIMARY KEY (`idprodutos`),
  INDEX `fk_produtos_empresario1_idx` (`empresario_idempresario` ASC) VISIBLE,
  CONSTRAINT `fk_produtos_empresario1`
    FOREIGN KEY (`empresario_idempresario`)
    REFERENCES `mydb`.`empresario` (`idempresario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- servicos (catálogo de serviços do empresário - NÃO depende de agenda)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`servicos` (
  `idservicos` INT NOT NULL AUTO_INCREMENT,
  `serv_nome` VARCHAR(255) NOT NULL,
  `serv_valor` DECIMAL(10,2) NOT NULL,
  `serv_descricao` VARCHAR(255) NOT NULL,
  `serv_tempo` TIME NOT NULL,
  `serv_fotos` VARCHAR(45) NULL,
  `empresario_idempresario` INT NOT NULL,
  PRIMARY KEY (`idservicos`),
  INDEX `fk_servicos_empresario1_idx` (`empresario_idempresario` ASC) VISIBLE,
  CONSTRAINT `fk_servicos_empresario1`
    FOREIGN KEY (`empresario_idempresario`)
    REFERENCES `mydb`.`empresario` (`idempresario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- horarios (grade de funcionamento do empresário por dia da semana)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`horarios` (
  `idhorarios` INT NOT NULL AUTO_INCREMENT,
  `horarios_dia_semana` VARCHAR(45) NOT NULL,
  `horarios_hora_inicio` TIME NOT NULL,
  `horarios_hora_fim` TIME NOT NULL,
  `empresario_idempresario` INT NOT NULL,
  PRIMARY KEY (`idhorarios`),
  INDEX `fk_horarios_empresario1_idx` (`empresario_idempresario` ASC) VISIBLE,
  CONSTRAINT `fk_horarios_empresario1`
    FOREIGN KEY (`empresario_idempresario`)
    REFERENCES `mydb`.`empresario` (`idempresario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- agenda (o agendamento em si: quem, com quem, qual serviço, quando)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`agenda` (
  `idagenda` INT NOT NULL AUTO_INCREMENT,
  `agend_horario` DATETIME NOT NULL,
  `agend_status` ENUM('pendente','confirmado','cancelado','concluido') NOT NULL DEFAULT 'pendente',
  `agend_idusuarios` INT NOT NULL,
  `agend_idempresario` INT NOT NULL,
  `agend_idservicos` INT NOT NULL,
  `agend_criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idagenda`),
  INDEX `fk_agenda_usuarios1_idx` (`agend_idusuarios` ASC) VISIBLE,
  INDEX `fk_agenda_empresario1_idx` (`agend_idempresario` ASC) VISIBLE,
  INDEX `fk_agenda_servicos1_idx` (`agend_idservicos` ASC) VISIBLE,
  CONSTRAINT `fk_agenda_usuarios1`
    FOREIGN KEY (`agend_idusuarios`)
    REFERENCES `mydb`.`usuarios` (`idusuarios`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_agenda_empresario1`
    FOREIGN KEY (`agend_idempresario`)
    REFERENCES `mydb`.`empresario` (`idempresario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_agenda_servicos1`
    FOREIGN KEY (`agend_idservicos`)
    REFERENCES `mydb`.`servicos` (`idservicos`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- comentarios
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comentarios` (
  `idcomentarios` INT NOT NULL AUTO_INCREMENT,
  `coment_texto` VARCHAR(255) NULL,
  `coment_idusuarios` INT NOT NULL,
  `comentarios_estrela` TINYINT NULL,
  PRIMARY KEY (`idcomentarios`),
  INDEX `fk_comentarios_usuarios1_idx` (`coment_idusuarios` ASC) VISIBLE,
  CONSTRAINT `fk_comentarios_usuarios1`
    FOREIGN KEY (`coment_idusuarios`)
    REFERENCES `mydb`.`usuarios` (`idusuarios`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- emp_user (relação empresario <-> usuarios, ex: favoritos/vínculo)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`emp_user` (
  `empresario_idempresario` INT NOT NULL,
  `usuarios_idusuarios` INT NOT NULL,
  PRIMARY KEY (`empresario_idempresario`, `usuarios_idusuarios`),
  INDEX `fk_emp_user_usuarios1_idx` (`usuarios_idusuarios` ASC) VISIBLE,
  INDEX `fk_emp_user_empresario1_idx` (`empresario_idempresario` ASC) VISIBLE,
  CONSTRAINT `fk_emp_user_empresario1`
    FOREIGN KEY (`empresario_idempresario`)
    REFERENCES `mydb`.`empresario` (`idempresario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_emp_user_usuarios1`
    FOREIGN KEY (`usuarios_idusuarios`)
    REFERENCES `mydb`.`usuarios` (`idusuarios`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
