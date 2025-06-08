document.addEventListener('DOMContentLoaded', () => {
    const selectTipo = document.getElementById('tipoAtendimento');
    const containerExtra = document.getElementById('tipoAtendimentoExtraContainer');
    const publicoSelect = document.getElementById("perfilAtendido");

    const opcoesExtras = {
        carteira_de_trabalho: [
            "Informações sobre carteira de trabalho física e digital",
            "Informações sobre direito e ao benefício SD",
            "Informações sobre vagas - cadastramento, registro de retorno, administração",
            "Informações sobre outros serviços (carteira de identidade, serviços de outros órgãos, atestado de bons antecedentes, Fundo de Garantia por Tempo de Serviço - FGTS, benefícios previdenciários)",
            "Outra"
        ],
        programa_gaucho_artesanato: [
            "Orientação carteira do artesão PGA e PAB",
            "Orientações sobre participação em feiras de artesanato",
            "Orientação participação em Casas do Artesão",
            "Orientação para inscrição e acesso ao portal do artesanato gaúcho",
            "Outra"
        ],
        vida_centro_humanistico: [
            "Orientações para atividades lúdicas e pedagógicas",
            "Orientações para atividades esportivas e de recreação",
            "Orientação para entidades parceiras (privadas)",
            "Orientação para entidades parceiras (públicas)",
            "Orientações para eventos, festas e mutirões sociais",
            "Outra"
        ],
        mercado_trabalho: [
            "Estatísticas da intermediação de mão de obra no âmbito do Sine/RS",
            "Estatísticas do seguro-desemprego no âmbito do Sine/RS",
            "Relação de vagas abertas no Sine/RS",
            "Resultados do Novo CAGED",
            "Resultados da PNAD Contínua",
            "Outra"
        ]

    };

    const campos = [
        {
            id: 'nomeAtendido'
        },
        {
            id: 'contatoAtendido'
        },
        {
            id: 'documentoAtendido'
        }
    ];


    // Opcoes dinamicas em Tipo de atendimento
    selectTipo.addEventListener('change', () => {
        // Pega a <option> selecionada
        const selectedOption = selectTipo.options[selectTipo.selectedIndex];
        const optionId = selectedOption.id;

        containerExtra.innerHTML = "";

        if (opcoesExtras[optionId]) {
            const label = document.createElement('label');
            label.classList.add('form-label', 'mt-3');
            label.innerText = "Detalhes do tipo de Atendimento";

            const select = document.createElement('select');
            select.className = "form-select mt-1";
            select.required = true;
            select.name = "descricao_tipo_atendimento";

            const defaultOption = document.createElement('option');
            defaultOption.text = "Selecione uma opção...";
            defaultOption.disabled = true;
            defaultOption.selected = true;
            select.appendChild(defaultOption);

            opcoesExtras[optionId].forEach(op => {
                const option = document.createElement('option');
                option.value = op.toLowerCase();
                option.text = op;
                select.appendChild(option);
            });

            containerExtra.appendChild(label);
            containerExtra.appendChild(select);
        }
    });


    // Opcoes dinamicas em publico
    publicoSelect.addEventListener('change', function () {
        const valor = publicoSelect.value;

        campos.forEach(campo => {
            const input = document.getElementById(campo.id);

            if (valor === 'Empregador' || valor === 'Trabalhador') {
                input.disabled = false;
            } else {
                input.disabled = true;
            }
        });
    });
});
