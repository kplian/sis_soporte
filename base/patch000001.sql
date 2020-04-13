/***********************************I-SCP-EGS-SOPTE-0-27/02/2019****************************************/
CREATE TABLE sopte.thelp_desk (
  id_help_desk SERIAL,
  nro_tramite VARCHAR,
  id_proceso_wf INTEGER,
  id_estado_wf INTEGER,
  id_funcionario INTEGER,
  fecha DATE,
  estado VARCHAR,
  descripcion VARCHAR,
  prioridad VARCHAR,
  id_tipo INTEGER,
  id_tipo_sub INTEGER,
  CONSTRAINT thelp_desk_pkey PRIMARY KEY(id_help_desk)
) INHERITS (pxp.tbase)

WITH (oids = false);

COMMENT ON COLUMN sopte.thelp_desk.fecha
IS 'Fecha de la Solicitud';

COMMENT ON COLUMN sopte.thelp_desk.descripcion
IS 'descripcion del problema';

COMMENT ON COLUMN sopte.thelp_desk.prioridad
IS 'Se establece la prioridad del problema';

COMMENT ON COLUMN sopte.thelp_desk.id_tipo
IS 'id del tipo de soporte';

COMMENT ON COLUMN sopte.thelp_desk.id_tipo_sub
IS 'El id del subtipo que es un id_tipo esto por la recursividad de la tabla';

---------------------------SQL----------------------------------------
CREATE TABLE sopte.ttipo (
  id_tipo SERIAL,
  codigo VARCHAR,
  descripcion VARCHAR,
  id_tipo_fk INTEGER,
  nombre VARCHAR,
  CONSTRAINT ttipo_pkey PRIMARY KEY(id_tipo)
) INHERITS (pxp.tbase)

WITH (oids = false);

COMMENT ON COLUMN sopte.ttipo.codigo
IS 'codigo del tipo';

COMMENT ON COLUMN sopte.ttipo.descripcion
IS 'Descripcion del tipo';

COMMENT ON COLUMN sopte.ttipo.id_tipo_fk
IS 'Especifica se un subtipo del tipo';

COMMENT ON COLUMN sopte.ttipo.nombre
IS 'nombre del tipo';

/***********************************F-SCP-EGS-SOPTE-0-27/02/2019**********************************************/
/***********************************I-SCP-EGS-SOPTE-1-16/12/2019**********************************************/
ALTER TABLE sopte.thelp_desk
  ADD COLUMN numero_ref INTEGER;

COMMENT ON COLUMN sopte.thelp_desk.numero_ref
IS 'el numero interno del funcionario solicitante';
/***********************************F-SCP-EGS-SOPTE-1-16/12/2019**********************************************/
/***********************************I-SCP-VAN-SOPTE-1-07/04/2020**********************************************/
alter table sopte.thelp_desk
	add numero_correo int;

comment on column sopte.thelp_desk.numero_correo is 'Número referencial al correo importado';

create unique index thelp_desk_numero_correo_uindex
	on sopte.thelp_desk (numero_correo);
/***********************************F-SCP-EGS-SOPTE-1-07/04/2020**********************************************/
