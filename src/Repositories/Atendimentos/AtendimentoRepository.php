<?php

namespace Fgtas\Repositories\Atendimentos;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception;
use Fgtas\Database\Connection;
use Fgtas\Entities\Atendimento;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;

class AtendimentoRepository implements IAtendimentoRepository
{
    private DBALConnection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn->getConnection();
    }



    public function add(int $idTipoAtend, int $idUsuario, int $idPublico, int $idForma): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->insert('atendimento')
            ->values([
                'tipo_atendimento_id' => ':tipo_id',
                'usuario_id' => ':usuario_id',
                'publico_id' => ':publico_id',
                'forma_atendimento_id' => ':forma'
            ])
            ->setParameters([
                'tipo_id' => $idTipoAtend,
                'usuario_id' => $idUsuario,
                'publico_id' => $idPublico,
                'forma' => $idForma
            ]);
        return $queryBuilder->executeStatement();
    }


    /**
     * @inheritDoc
     * @throws Exception
     */
    public function findAll(): ?array
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->select(
                'a.id',
                'a.data_de_registro',
                'fa.forma',
                'ta.tipo',
                'ta.descricao',
                'u.nome AS nome_atendente',
                'p.perfil_cliente',
                'i.nome AS nome_publico',
                'i.contato',
                'i.documento',
            )
            ->from('atendimento', 'a')
            ->innerJoin('a', 'forma_atendimento', 'fa', 'a.forma_atendimento_id = fa.id')
            ->innerJoin('a', 'tipo_atendimento', 'ta', 'a.tipo_atendimento_id = ta.id')
            ->innerJoin('a', 'usuario', 'u', 'a.usuario_id = u.id')
            ->innerJoin('a', 'publico', 'p', 'a.publico_id = p.id')
            ->leftJoin('a', 'informacoes_pessoais', 'i', 'p.id = i.publico_id')
            ->orderBy('id', 'DESC');
        $resultSet = $queryBuilder->executeQuery();

        $data = $resultSet->fetchAllAssociative();

        return array_map(Atendimento::fromArray(...), $data);
    }

    public function findById(int $id): ?Atendimento
    {
        // TODO: Implement findById() method.
    }

    public function update(Atendimento $atendimento, int $id): bool
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->delete('atendimento')
            ->where('id = :id')
            ->setParameter('id', $id);

        return $queryBuilder->executeStatement();
    }

    private function hydrateAtendimentos(array $data): Atendimento
    {
        // TODO: implement hydrateAtendimentos() method.
    }
}