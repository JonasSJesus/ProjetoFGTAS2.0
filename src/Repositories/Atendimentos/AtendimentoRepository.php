<?php

namespace Fgtas\Repositories\Atendimentos;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception;
use Fgtas\Database\Connection;
use Fgtas\Entities\Atendimento;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IFormaAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IPublicoRepository;
use Fgtas\Repositories\Interfaces\ITipoAtendimentoRepository;

class AtendimentoRepository implements IAtendimentoRepository
{
    private DBALConnection $conn;
    private IFormaAtendimentoRepository $formaRepo;
    private IPublicoRepository $publicoRepository;
    private ITipoAtendimentoRepository $tipoRepository;

    public function __construct(
        Connection $conn,
//        IFormaAtendimentoRepository $formaRepository,
        ITipoAtendimentoRepository $tipoRepository,
        IPublicoRepository $publicoRepository
    ) {
        $this->conn = $conn->getConnection();
//        $this->formaRepo = $formaRepository;
        $this->tipoRepository = $tipoRepository;
        $this->publicoRepository = $publicoRepository;
    }


    /**
     * @param Atendimento $atendimento
     * @param int $idUsuario
     * @return bool
     * @throws Exception
     */
    public function add(Atendimento $atendimento, int $idUsuario): bool
    {
        $queryBuilder = $this->conn->createQueryBuilder();

        $this->conn->beginTransaction();
        try {
            // TODO: Em vez de salvar os itens, isso poderia apenas buscar o ID de itens salvos previamente no banco de dados talvez?
            $idPublico = $this->publicoRepository->add($atendimento->publico);
            $idTipoAtend = $this->tipoRepository->add($atendimento->tipoAtendimento);

            // Operacao para salvar os atendimentos na tabela 'atendimento'
            // Talvez mudar para um metodo especifico?
            $queryBuilder
                ->insert('atendimento')
                ->values([
                    'tipo_atendimento_id' => ':tipo_id',
                    'usuario_id' => ':usuario_id',
                    'publico_id' => ':publico_id',
                    'forma_atendimento' => ':forma'
                ])
                ->setParameters([
                    'tipo_id' => $idTipoAtend,
                    'usuario_id' => $idUsuario,
                    'publico_id' => $idPublico,
                    'forma' => $atendimento->formaAtendimento
                ]);
            $queryBuilder->executeStatement();

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }

        return true;
    }

//    private function addAtendimento(int $idTipoAtend, int $idUsuario, int $idPublico, Atendimento $atendimento): bool
//    {
//        $queryBuilder = $this->conn->createQueryBuilder();
//
//        $queryBuilder
//            ->insert('atendimento')
//            ->values([
//                'tipo_atendimento_id' => ':tipo_id',
//                'usuario_id' => ':usuario_id',
//                'publico_id' => ':publico_id',
//                'forma_atendimento' => ':forma'
//            ])
//            ->setParameters([
//                'tipo_id' => $idTipoAtend,
//                'usuario_id' => $idUsuario,
//                'publico_id' => $idPublico,
//                'forma' => $atendimento->formaAtendimento
//            ]);
//        return $queryBuilder->executeStatement();
//    }


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
                'a.forma_atendimento',
                'ta.tipo',
                'ta.descricao',
                'u.nome',
                'p.perfil_cliente'
            )
            ->from('atendimento', 'a')
            ->innerJoin('a', 'tipo_atendimento', 'ta', 'a.tipo_atendimento_id = ta.id')
            ->innerJoin('a', 'usuario', 'u', 'a.usuario_id = u.id')
            ->innerJoin('a', 'publico', 'p', 'a.publico_id = p.id')
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