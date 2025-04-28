<?php

namespace Fgtas\Validations;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;

class AtendimentoValidator
{

    /**
     * Aplica as regras de validação nos campos de $data e retorna uma instancia de Respect\Validation\Validator
     * @param array $data
     * @return bool
     */
    public static function validate(array $data): bool
    {
        $dataValidation = Validator::key('identificacaoAtendente', Validator::notEmpty()->stringType())
                           ->key('formaAtendimento', Validator::notEmpty()->stringType())
                           ->key('perfilPublico', Validator::notEmpty())
                           ->key('tipoAtendimento');
//                           ->key('descricao_tipo_atendimento', Validator::optional(Validator::stringType())); // TODO: Tornar a validação opcional

        if (in_array($data['perfilPublico'], ['empregador', 'trabalhador'])) {
            $dataValidation->key('nomePublico', Validator::notEmpty())
                           ->key('contatoPublico', Validator::notEmpty());

            $documentoValidation = $data['perfilPublico'] === 'empregador' ? Validator::notEmpty()->cnpj() : Validator::notEmpty()->cpf();
            $dataValidation->key('documentoPublico', $documentoValidation);
        }

        /** @var Validator $dataValidation */
        return $dataValidation->isValid($data); // ToDo: retornar um array contendo os campos que nao passaram nos testes
    }
}
