-- Database generated with pgModeler (PostgreSQL Database Modeler).
-- pgModeler  version: 0.9.0-beta2
-- PostgreSQL version: 9.6
-- Project Site: pgmodeler.com.br
-- Model Author: ---


-- Database creation must be done outside an multicommand file.
-- These commands were put in this file only for convenience.
-- -- object: "db-expressive" | type: DATABASE --
-- -- DROP DATABASE IF EXISTS "db-expressive";
-- CREATE DATABASE "db-expressive"
-- ;
-- -- ddl-end --
-- 

-- object: public.pessoa | type: TABLE --
-- DROP TABLE IF EXISTS public.pessoa CASCADE;
CREATE TABLE public.pessoa(
	"ID" serial NOT NULL,
	nome varchar NOT NULL,
	"inscricaoFederal" varchar NOT NULL,
	tipo integer NOT NULL,
	situacao integer NOT NULL,
	"enderecoJson" jsonb,
	CONSTRAINT "ID" PRIMARY KEY ("ID")

);
-- ddl-end --
COMMENT ON COLUMN public.pessoa."ID" IS 'ID - campo incremental único identificador do registro';
-- ddl-end --
COMMENT ON COLUMN public.pessoa.nome IS 'nome - nome do registro pessoa';
-- ddl-end --
COMMENT ON COLUMN public.pessoa."inscricaoFederal" IS 'inscricaoFederal - valor código na receita federal CPF/CNPJ';
-- ddl-end --
COMMENT ON COLUMN public.pessoa.tipo IS 'tipo - valor utilizado para identificar tipo do registro';
-- ddl-end --
COMMENT ON COLUMN public.pessoa.situacao IS 'situacao - valor utilizado para definir a situacao do registro';
-- ddl-end --
COMMENT ON COLUMN public.pessoa."enderecoJson" IS 'enderecoJson - exemplo de relação de endereços utilizada como campo json string';
-- ddl-end --
ALTER TABLE public.pessoa OWNER TO postgres;
-- ddl-end --

-- object: public.endereco | type: TABLE --
-- DROP TABLE IF EXISTS public.endereco CASCADE;
CREATE TABLE public.endereco(
	"ID" serial NOT NULL,
	"pessoaID" integer NOT NULL,
	logradouro varchar NOT NULL,
	numero integer,
	bairro varchar,
	cep varchar,
	cidade varchar NOT NULL,
	estado varchar NOT NULL,
	CONSTRAINT "Endereco_pk" PRIMARY KEY ("ID")

);
-- ddl-end --
COMMENT ON COLUMN public.endereco."ID" IS 'ID - campo incremental único identificador do registro';
-- ddl-end --
COMMENT ON COLUMN public.endereco."pessoaID" IS 'pessoaID - campo incremental único identificador do registro pessoa qual endereço é vinculado';
-- ddl-end --
COMMENT ON COLUMN public.endereco.logradouro IS 'logradouro - campo texto contendo nome do logradouro';
-- ddl-end --
COMMENT ON COLUMN public.endereco.numero IS 'numero - campo inteiro contendo número vinculado ao endereço';
-- ddl-end --
COMMENT ON COLUMN public.endereco.bairro IS 'bairro - campo texto contendo nome do bairro';
-- ddl-end --
COMMENT ON COLUMN public.endereco.cep IS 'cep - campo texto contendo cep vinculado ao endereço';
-- ddl-end --
COMMENT ON COLUMN public.endereco.cidade IS 'cidade - campo texto contendo nome da cidade';
-- ddl-end --
COMMENT ON COLUMN public.endereco.estado IS 'estado - campo texto contendo nome do estado';
-- ddl-end --
ALTER TABLE public.endereco OWNER TO postgres;
-- ddl-end --

-- object: "EnderecoPessoa_fk" | type: CONSTRAINT --
-- ALTER TABLE public.endereco DROP CONSTRAINT IF EXISTS "EnderecoPessoa_fk" CASCADE;
ALTER TABLE public.endereco ADD CONSTRAINT "EnderecoPessoa_fk" FOREIGN KEY ("pessoaID")
REFERENCES public.pessoa ("ID") MATCH FULL
ON DELETE CASCADE ON UPDATE NO ACTION;
-- ddl-end --


