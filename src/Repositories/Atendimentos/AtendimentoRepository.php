<?php

namespace Fgtas\Repositories\Atendimentos;

use Fgtas\Entities\Atendimento;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IFormaAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IPublicoRepository;
use Fgtas\Repositories\Interfaces\ITipoAtendimentoRepository;
use PDO;
use PDOException;

class AtendimentoRepository implements IAtendimentoRepository
{
    private PDO $pdo;
    private IFormaAtendimentoRepository $formaRepo;
    private IPublicoRepository $publicoRepository;
    private ITipoAtendimentoRepository $tipoRepository;

    public function __construct(
        PDO $pdo,
        IFormaAtendimentoRepository $formaRepository,
        ITipoAtendimentoRepository $tipoRepository,
        IPublicoRepository $publicoRepository
    ) {
        $this->pdo = $pdo;
        $this->formaRepo = $formaRepository;
        $this->tipoRepository = $tipoRepository;
        $this->publicoRepository = $publicoRepository;
    }

    public function add(Atendimento $atendimento, int $idUsuario): bool
    {
        $this->pdo->beginTransaction();
        try {
            $idFormaAtend = $this->formaRepo->create($atendimento->formaAtendimento);
            $idPublico = $this->publicoRepository->create($atendimento->publico);
            $idTipoAtend = $this->tipoRepository->create($atendimento->tipoAtendimento);

            $sqlAtendimento = "INSERT INTO atendimento (forma_atendimento_id, tipo_atendimento_id, usuario_id, publico_id) VALUES (:forma_id, :tipo_id, :usuario_id, :publico_id);";
            $stmt = $this->pdo->prepare($sqlAtendimento);
            $stmt->bindValue(':forma_id', $idFormaAtend);
//            $stmt->bindValue(':forma_atendimento, $atendimento->formaAtendimento);
            $stmt->bindValue(':tipo_id', $idTipoAtend);
            $stmt->bindValue(':usuario_id', $idUsuario);
            $stmt->bindValue(':publico_id', $idPublico);
            $stmt->execute();

            if ($this->pdo->commit()) {
                echo "Success on save the data";
            } else {
                echo "Falha";
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): ?array
    {
        $sql = "SELECT a.";

        return [];
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
        // TODO: Implement delete() method.
    }
}