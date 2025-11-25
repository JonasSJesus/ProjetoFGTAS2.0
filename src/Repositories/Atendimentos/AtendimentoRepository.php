<?php

namespace Fgtas\Repositories\Atendimentos;

use DateTime;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
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
    public function findAll(array $filters = []): ?array
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
                'pi.nome AS nome_publico',
                'pi.email',
                'pi.documento',
            )
            ->from('atendimento', 'a')
            ->innerJoin('a', 'forma_atendimento', 'fa', 'a.forma_atendimento_id = fa.id')
            ->innerJoin('a', 'tipo_atendimento', 'ta', 'a.tipo_atendimento_id = ta.id')
            ->innerJoin('a', 'usuario', 'u', 'a.usuario_id = u.id')
            ->innerJoin('a', 'publico', 'p', 'a.publico_id = p.id')
            ->leftJoin('a', 'pessoa', 'pi', 'p.pessoa_id = pi.id')
            ->orderBy('data_de_registro', 'DESC');

        if (!empty($filters)) {
            $this->applyFilters($queryBuilder, $filters);
        }

        $resultSet = $queryBuilder->executeQuery();

        $data = $resultSet->fetchAllAssociative();

        return array_map(Atendimento::fromArray(...), $data);
    }


    public function findById(int $id): ?Atendimento
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select(
                'a.id',
                'a.data_de_registro',
                'fa.forma',
                'ta.tipo',
                'ta.descricao',
                'u.nome AS nome_atendente',
                'p.perfil_cliente',
                'pi.nome AS nome_publico',
                'pi.email',
                'pi.documento',
            )
            ->from('atendimento', 'a')
            ->innerJoin('a', 'forma_atendimento', 'fa', 'a.forma_atendimento_id = fa.id')
            ->innerJoin('a', 'tipo_atendimento', 'ta', 'a.tipo_atendimento_id = ta.id')
            ->innerJoin('a', 'usuario', 'u', 'a.usuario_id = u.id')
            ->innerJoin('a', 'publico', 'p', 'a.publico_id = p.id')
            ->leftJoin('a', 'pessoa', 'pi', 'p.pessoa_id = pi.id')
            ->where("a.id = :atendimento_id")
            ->setParameter("atendimento_id", $id)
            ->executeQuery();
        $data = $resultSet->fetchAssociative();

        if (!$data) {
            return null;
        }

        return Atendimento::fromArray($data);
    }


    public function findForeignKeys(int $id): array|null
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $resultSet = $queryBuilder
            ->select("*")
            ->from('atendimento')
            ->where("id = :atendimento_id")
            ->setParameter("atendimento_id", $id)
            ->executeQuery();
        $data = $resultSet->fetchAssociative();

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function update(int $idTipo, int $idPublico, int $idForma, int $idAtendimento): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $queryBuilder
            ->update('atendimento')
            ->set('publico_id', ':publico_id')
            ->set('forma_atendimento_id', ':forma_atendimento_id')
            ->where('id = :id')
            ->setParameters([
                'publico_id' => $idPublico,
                'forma_atendimento_id' => $idForma,
                'id' => $idAtendimento
            ]);

        return $queryBuilder->executeStatement();
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



    private function applyFilters(QueryBuilder $qb, array $filters = []): void
    {
        $filterMap = [
            "formaAtendimento" => [
                "row" => "fa.forma",
                "param" => ":forma",
                "paramValue" => "forma"
            ],
            "publico" => [
                "row" => "p.perfil_cliente",
                "param" => ":publico",
                "paramValue" => "publico"
            ],
            "tipoAtendimento" => [
                "row" => "ta.tipo",
                "param" => ":tipo",
                "paramValue" => "tipo"
            ],
        ];

        if (!isset($filters)) {
            return;
        }

        // Filtros gerais
        foreach ($filters as $key => $value) {
            if (array_key_exists($key, $filterMap)){
                $qb->andWhere("{$filterMap[$key]['row']} = {$filterMap[$key]['param']}")
                    ->setParameter("{$filterMap[$key]['paramValue']}", $value);
            }
        }

        // Filtro por data de inicio
        if (!empty($filters['dataInicio'])) {
            $dataInicio = new DateTime($filters['dataInicio']);

            $dataInicioFormatted = $dataInicio
                ->setTime(0, 0, 0)
                ->format('Y-m-d H:i:s');

            $qb->andWhere('a.data_de_registro >= :dataInicio')
                ->setParameter('dataInicio', $dataInicioFormatted);
        }

        // Filtro por data de fim
        if (!empty($filters['dataFim'])) {
            $dataFim = new DateTime($filters['dataFim']);

            $dataFimFormatted = $dataFim
                ->modify('+1 day')
                ->setTime(0, 0, 0)
                ->format('Y-m-d H:i:s');

            $qb->andWhere('a.data_de_registro <= :dataFim')
                ->setParameter('dataFim', $dataFimFormatted);
        }
    }
}