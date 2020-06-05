<?php

namespace Osians\DataEntityManager;

interface EntityInterface
{
    /**
     *    Retorna o ID da entidade
     *
     *    @return int
     */
    public function getId();

    /**
     *    Seta o ID da entidade
     *
     *    @param int $id
     *    @return  Entity
     */
    public function setId($id);

    /**
     *    Inicializa a Entidade atual com dados Vindos de um Objeto
     *    StdClass. Normalmente enviado peloa EntityManager
     *
     *    @param Stdclass $object
     *    @return void
     */
    public function init(\StdClass $object);

    /**
     *    Retorna o Nome da tabela a qual a Entidade se refere.
     *    Se não for setado na entidade filho, sera usado o nome da
     *    classe como nome da tabela por padrão.
     *
     *    @return String
     */
    public function getTableName();

    /**
     *    Retorna nome da coluna que guarda Chave Primaria da Entidade.
     *    Por padrao o nome e' composto de "id_ + nome_da_classe" em lowercase.
     *
     *    @return String
     */
    public function getPrimaryKeyName();
}
