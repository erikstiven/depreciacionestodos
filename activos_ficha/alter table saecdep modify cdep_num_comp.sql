-- DATE: 13/OCT/2018
-- XAVIER REYES
-- CODIGO DE ASIENTO CONTABLE CON EL QUE FUE MAYORIZADO
alter table saecdep modify cdep_num_comp varchar(13);
-- CODIGO DE REVALORIZACION
alter table saecdep add cdep_cod_reva integer;
alter table saemet add metd_cod_reva integer;

 
 -- CTAS PARA REVALORIZACION
  alter table saegact add gact_cta_reva VARCHAR(35); -- Debito cta. de revalorizacion
  alter table saegact add gact_cta_supe VARCHAR(35); -- Credito cta. superavit (+)
  
   -- CTAS PARA DEPRECIACION DE ACTIVOS REVALORIZADOS
  alter table saegact add gact_gto_reva VARCHAR(35); -- Debito cta. de gasto revalorizacion
  alter table saegact add gact_dep_reva VARCHAR(35); -- Credito cta. depreciacion revalorizacion

  
  -- CTAS PARA DESVALORIZACION
  alter table saegact add gact_cta_desv VARCHAR(35); -- Debito cta. de desvalorizacion
  alter table saegact add gact_cta_defi VARCHAR(35); -- Credito cta. deficit (-) 
  
 -- CTAS PARA DEPRECIACION DE ACTIVOS DESVALORIZADOS
  alter table saegact add gact_gto_desv VARCHAR(35); -- Debito cta. de gasto revalorizacion
  alter table saegact add gact_dep_desv VARCHAR(35); -- Credito cta. depreciacion revalorizacion
  
  -- CLAVE DEL PADRE PARA ACTIVOS REVALORIZADOS
alter table saeact add act_clave_padr varchar(13);
    
-- Table: saereva
-- Description: Registro de revalorizaciones de activos fijos
-- Date: 14-OCT-2018
-- XAVIER REYES

create table saereva  (
  reva_cod_reva        SERIAL                          not null,
  reva_cod_acti        INTEGER,
  reva_cod_empr        INTEGER,
  reva_cod_sucu        INTEGER,
  reva_fec_reva        DATE,
  reva_val_reva        DEC(18,6),
  reva_vid_util        INTEGER,
  reva_val_comp        DEC(18,6),
  reva_cod_asto        VARCHAR(13),
  reva_fec_serv        DATETIME YEAR TO SECOND default current year to second,
  reva_cod_usua        VARCHAR(10),
primary key (reva_cod_reva)
      constraint PK_SAEREVA,
unique (reva_cod_acti, reva_cod_empr, reva_cod_sucu, reva_fec_reva)
      constraint CU_SAEREVA
);

alter table saereva
   add constraint foreign key (reva_cod_acti, reva_cod_empr, reva_cod_sucu)
      references saeact (act_cod_act, act_cod_empr, act_cod_sucu) 
      constraint FK_SAER_SAEACT;

-- 19-Dic-2018
-- BANDERA PARA INDICAR SI LA DEPRECIACION SE HACE A PARTIR DE LA FECHA DE COMPRA,
-- LA TABLA DE INICES (SAEMET) SE GENERA A PARTIR DEL MES DE COMPRA SOLO POR VALOR 
-- CORRESPONDIENTE A LOS DIAS QUE FALTA DEL MES EN CURSOR

 alter table saetdep add tdep_dep_fcom char(1) default "N" 
	check (tdep_dep_fcom in ("S","N")) constraint ck_saetdep5;

-- 06-ENE-2019	
-- BANDERA PARA DIFERENCIAR SI UNA CTA ES PARA DEPRECICACION NORMAL O DEPRECIACION POR REVALORIZACION
 alter table saegasd add gasd_rev_sn char(1) default "N" 
	check (gasd_rev_sn in ("S","N")) constraint ck_saegasd5;

-- BANDERA PARA VER SI UN ACTIVO FIJO ES NUEVO, REVALORIOZADO O DESVALORIZADO	
 alter table saeact add act_est_reva char(1) default "N" 
	check (act_est_reva in ("R","D","N")) constraint ck_saeact42;
	


alter table saeact add act_cod_rela integer;

