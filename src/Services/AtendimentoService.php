<?php

namespace Fgtas\Services;

use Fgtas\Entities\Atendimento;
use Fgtas\Entities\FormaAtendimento;
use Fgtas\Entities\Publico;
use Fgtas\Entities\TipoAtendimento;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;

class AtendimentoService
{
    private IAtendimentoRepository $atendimentoRepository;

    public function __construct(IAtendimentoRepository $atendimentoRepository)
    {
        $this->atendimentoRepository = $atendimentoRepository;
    }

    public function createAtendimento(array $data)
    {
        $tipoAtendimento = new TipoAtendimento($data['tipoAtendimento']);
        $formaAtendimento = new FormaAtendimento($data['formaAtendimento']);
        $publicoPerfil = new Publico($data['perfilPublico']);
        $publicoPerfil->setExtraFields($data['nomePublico'], $data['documentoPublico'], $data['contatoPublico']);

        $atendimento = new Atendimento($formaAtendimento, $tipoAtendimento, $publicoPerfil);

        $this->atendimentoRepository->add($atendimento, $_SESSION['user']['id']);

//        return $atendimento;
    }

    /** @return Atendimento[] */
    public function all(): array
    {
        $data = $this->atendimentoRepository->findAll();


        return $data;
    }
}