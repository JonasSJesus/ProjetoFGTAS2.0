<?php

namespace Fgtas\Validations;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class AtendimentoValidator
{

    /**
     * Aplica as regras de validação nos campos de $data e retorna uma instancia de Respect\Validation\Validator
     * @param array $data
     * @return bool
     */
    public static function validate(array $data): bool
    {
        $dataValidation = v::key('identificacaoAtendente', v::notEmpty()->stringType())
                           ->key('formaAtendimento', v::notEmpty()->stringType())
                           ->key('perfilPublico', v::notEmpty())
                           ->key('tipoAtendimento')
                           ->key('descricao_tipo_atendimento', v::optional(v::stringType()));

        if (in_array($data['perfilPublico'], ['empregador', 'trabalhador'])) {
            $dataValidation->key('nomePublico', v::notEmpty())
                           ->key('contatoPublico', v::notEmpty());

            $documentoValidation = $data['perfilPublico'] === 'empregador' ? v::notEmpty()->cnpj() : v::notEmpty()->cpf();
            $dataValidation->key('documentoPublico', $documentoValidation);
        }

        /** @var v $dataValidation */
        return $dataValidation->isValid($data);
    }
}
