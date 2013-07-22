<?php
/*
* This file is part of the Duo Criativa software.
* Este arquivo é parte do software da Duo Criativa.
*
* (c) Paulo Ribeiro <paulo@duocriativa.com.br>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace BFOS\PagamentoBundle\FormaPagamento;


use BFOS\PagamentoBundle\Model\FormaPagamentoInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class GerenteFormaPagamento implements GerenteFormaPagamentoInterface
{
    /**
     * @var ObjectManager $manager
     */
    protected $manager;

    protected $formaRepo;

    protected $outros;

    /**
     * @param ObjectManager $manager
     * @param array         $outros
     */
    public function __construct(ObjectManager $manager, $outros)
    {
        $this->manager = $manager;
        $this->outros = $outros;
    }

    /**
     * @return ObjectRepository
     */
    private function getFormaPagamentoRepository()
    {
        if (is_null($this->formaRepo)) {
            $this->formaRepo = $this->manager->getRepository($this->outros['forma_pagamento_class']);
        }
        return $this->formaRepo;
    }

    /**
     * @inheritdoc
     */
    public function getAtivas()
    {
        return $this->getFormaPagamentoRepository()->findBy(array('ativo'=>true));
    }

    /**
     * @inheritdoc
     */
    public function getTodas()
    {
        return $this->getFormaPagamentoRepository()->findAll();
    }


    /**
     * @inheritdoc
     */
    public function getFormaPagamentoById($id)
    {
        try {
            return $this->getFormaPagamentoRepository()->find($id);
        } catch (NonUniqueResultException $e) {
            return null;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
        public function getByGatewayPagamento($gatewayPagamento, $somenteAtivas = true, $somentePrimeiro = true)
    {
        $resultado = array();
        $formas = null;
        if ($somenteAtivas) {
            $formas = $this->getAtivas();
        } else {
            $formas = $this->getTodas();
        }
        /** @var FormaPagamentoInterface $forma */
        foreach ($formas as $forma) {
            if ($forma->getGatewayPagamento() == $gatewayPagamento) {
                if ($somentePrimeiro) {
                    return $forma;
                }
                $resultado[] = $forma;
            }
        }
        return $resultado;
    }


}
