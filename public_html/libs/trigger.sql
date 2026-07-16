DELIMITER //

CREATE TRIGGER cupom_after_update
AFTER UPDATE
   ON anuncio_cupons FOR EACH ROW
BEGIN
   CALL atualiza_pechinchometro (new.Cupom_Status, new.Cupom_Tipo, new.Cupom_Qtd, new.Cupom_Valor, new.Cupom_ValorFinal, new.Cupom_Entrega, new.Cupom_EntregaStatus);
END; //

DELIMITER ;

# CALL `atualiza_pechinchometro`(1, 1, 1, 500, 475, 1, 4);

DELIMITER //
CREATE PROCEDURE atualiza_pechinchometro(status int, tipo int, qtd int, valor decimal(10,2), valor_final decimal(10,2), entrega int, entrega_status int)
   BEGIN

      DECLARE contador int;
      DECLARE atualizar BOOLEAN DEFAULT NULL;
      DECLARE saldoAnteriorAnunciante DECIMAL(10,2) DEFAULT 0;
      DECLARE saldoAnteriorUsuario DECIMAL(10,2) DEFAULT 0;
      DECLARE valorAnunciante DECIMAL(10,2);
      DECLARE valorUsuario DECIMAL(10,2);

      SET TIME_ZONE = '-03:00';

      SET valorAnunciante = qtd * valor_final;

      IF tipo = 1 THEN /*produto*/
         SET valorUsuario = qtd * (valor - valor_final);
      ELSEIF tipo = 2 THEN /*servico*/
         IF !valor THEN
            SET valorUsuario = qtd * valor_final;
         ELSE
            SET valorUsuario = qtd * (valor - valor_final);
         END IF;
      ELSEIF tipo = 3 THEN /*desconto*/
         SET valorUsuario = qtd * valor_final;
      END IF;

      IF entrega THEN
         IF entrega_status = 4 AND status = 1 THEN
            SET atualizar = TRUE;
         END IF;
      ELSE
         IF status = 1 THEN
            SET atualizar = TRUE;
         END IF;
      END IF;

      IF atualizar THEN
         SELECT count(*) into contador FROM pechinchometro WHERE Pechinchometro_Data = curdate();

         IF contador < 1 THEN
            SELECT Pechinchometro_ValorAnunciante into saldoAnteriorAnunciante FROM pechinchometro ORDER BY Pechinchometro_ID DESC LIMIT 1;
            SELECT Pechinchometro_ValorUsuario into saldoAnteriorUsuario FROM pechinchometro ORDER BY Pechinchometro_ID DESC LIMIT 1;

            INSERT INTO pechinchometro (Pechinchometro_Data, Pechinchometro_ValorAnunciante, Pechinchometro_ValorUsuario) values (curdate(), saldoAnteriorAnunciante + valorAnunciante, saldoAnteriorUsuario + valorUsuario);
         ELSE
            UPDATE pechinchometro SET Pechinchometro_ValorAnunciante=Pechinchometro_ValorAnunciante + valorAnunciante, Pechinchometro_ValorUsuario=Pechinchometro_ValorUsuario + valorUsuario WHERE Pechinchometro_Data = curdate();
         END IF;
      END IF;

   END; //

DELIMITER ;