BFOSPagamentoBundle
===================

Este bundle tem por objetivo agregar funcionalidades comuns a vários métodos de pagamento.

Observacao
----------
A parte de Meios de Pagamentos teve seu conceito traduzido para a realidade brasileira do
http://jmsyst.com/bundles/JMSPaymentCoreBundle

Requisitos
----------

    - jQuery
    - RequireJS

Instalando
----------

- crie o parâmetro para servir

    bfos_pagamento.servico_de_criptografia.secret: e37secret-e37secret-e37secret-fim

TWIG Funções e Filtros
----------------------

bfos_pagamento_opcoes_parcelamento() :

Esta função exibe as opções de parcelamento a partir de um objeto de configuração das regras de parcelamento
e do valor a ser parcelado. Veja o exemplo a seguir.

    $config = new ParcelamentoConfiguracao();
    $config->setParcelamentoHabilitado(true);
    $config->setJurosParcelamento(1.99);
    $config->setQuantidadeMaximaParcelas(12);
    $config->setQuantidadeMaximaParcelasSemJuros(3);
    $config->setValorMinimoParcela(50);

    $parcelamento = ParcelamentoUtils::obterOpcoesDeParcelamento($config, 500);

No caso acima será gerada uma array com 10 elementos representando as 10 opções de parcelamento do valor.

Esta função ainda aceita um terceiro parâmetro com opções de controlar a exibição dos itens.

    OPÇÕES:

        colunas:
            o número de colunas nas quais as opções de parcelamento serão exibidas.
        mostrarParcelas:
            uma array onde cada elemento é o número da parcela que deseja exibir.
        mostrarLinkVerTudo:
            este link trabalha em conjunto com a opção mostrarParcelas. Se for definida a opção mostrarParcelas
            e mostrarLinkVerTudo for true, será adicionado um link para que sejam exibidos todos os parcelamentos
            possíveis ao se clicar no link.
        template:
            possibilita definir qual será a template utilizada para renderizar o parcelamento.



CRIANDO UM FORMULÁRIO PARA ESCOLHER A FORMA DE PAGAMENTO
--------------------------------------------------------

Veja o exemplo abaixo, utilizado em uma loja virtual


    use BFOS\PagamentoBundle\Parcelamento\Form\Type\ParcelamentoType;
    use BFOS\PagamentoBundle\Utils\ParcelamentoConfiguracao;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\FormBuilderInterface;

    class EscolhaFormaPagamentoPedidoType extends AbstractType
    {
        protected $configuracao;
        protected $valor;

        public function __construct(ParcelamentoConfiguracao $configuracao, $valor)
        {
            $this->configuracao = $configuracao;
            $this->valor = $valor;
        }

        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add(
                'formaPagamento',
                'bfos_pagamento_forma_pagamento_checkout_choice',
                array(
                    'label'         => ' ',
                    'configuracoes' => array(
                        'pagseguro' => array(
                            'configuracao_checkout_form' => new ParcelamentoType($this->configuracao, $this->valor)
                        )
                    )
                )
            );
        }

        public function getName()
        {
            return 'escolha_forma_pagamento_pedido_type';
        }
    }

Perceba que é possível passar configurações adicionais ao Type bfos_pagamento_forma_pagamento_checkout_choice .
Com isso, e a utilização do Javascript em Resources/assets, ele exibirá o formulário passado quando a forma de
pagamento do gateway 'pagseguro' for selecionada.

