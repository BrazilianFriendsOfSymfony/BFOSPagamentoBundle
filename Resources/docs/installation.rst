Adicione o mapeamento das interfaces ao config.yml

doctrine:
    # ....
    orm:
        # ....
        resolve_target_entities:
            BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface: BFOS\PagamentoBundle\Entity\InstrucaoPagamento
            BFOS\PagamentoBundle\Model\PagamentoInterface: BFOS\PagamentoBundle\Entity\Pagamento
            BFOS\PagamentoBundle\Model\TransacaoFinanceiraInterface: BFOS\PagamentoBundle\Entity\TransacaoFinanceira
