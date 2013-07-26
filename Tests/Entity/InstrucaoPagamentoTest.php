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

namespace BFOS\PagamentoBundle\Tests\Entity;


use BFOS\PagamentoBundle\Entity\InstrucaoPagamento;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class InstrucaoPagamentoTest extends WebTestCase
{
    /** @var ObjectManager $manager  */
    private $manager;

    public function testPersisteERecuperaComSucesso()
    {
        $instrPagto = new InstrucaoPagamento();
        $instrPagto->setGatewayPagamento('pagseguro');
        $instrPagto->getDadosAdicionais()->adicionar('pagseguro_emailVendedor', 'teste@gmail.com');
        $instrPagto->getDadosAdicionais()->adicionar('pagseguro_tokenVendedor', 'TESTEUTndke&$20dksdsf', true);
        $instrPagto->getDadosAdicionais()->adicionar('pagseguro_notificationUrl', 'http://www.site.com.br/pagseguro/pedido/1/notificacao');
        $instrPagto->getDadosAdicionais()->adicionar('pagseguro_redirectUrl', 'http://www.site.com.br/pagseguro/pedido/1/final');
        $this->manager->persist($instrPagto);
        $this->manager->flush();

        $this->manager->flush($instrPagto);

        $this->assertEquals('pagseguro', $instrPagto->getGatewayPagamento());
        $this->assertEquals('teste@gmail.com', $instrPagto->getDadosAdicionais()->obter('pagseguro_emailVendedor'));
    }

    protected function setUp()
    {

        $client = static::createClient();

        $classes = array(
        );

        $this->loadFixtures($classes);

        /** @var Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');
        $this->manager =  $doctrine->getManager();

    }


}
 