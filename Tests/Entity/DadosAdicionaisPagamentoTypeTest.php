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

use BFOS\PagamentoBundle\Criptografia\MCryptServicoDeCriptografia;
use BFOS\PagamentoBundle\Entity\DadosAdicionais;
use Doctrine\DBAL\Types\Type;
use BFOS\PagamentoBundle\Entity\DadosAdicionaisPagamentoType;

class DadosAdicionaisPagamentoTypeTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (Type::hasType(DadosAdicionaisPagamentoType::NAME)) {
            Type::overrideType(DadosAdicionaisPagamentoType::NAME, 'BFOS\PagamentoBundle\Entity\DadosAdicionaisPagamentoType');
        } else {
            Type::addType(DadosAdicionaisPagamentoType::NAME, 'BFOS\PagamentoBundle\Entity\DadosAdicionaisPagamentoType');
        }
    }

    public function testStaticSetGetEncryptionService()
    {
        $service = new MCryptServicoDeCriptografia('foo');

        $this->assertNull(DadosAdicionaisPagamentoType::getServicoDeCriptografia());
        DadosAdicionaisPagamentoType::setServicoDeCriptografia($service);
        $this->assertSame($service, DadosAdicionaisPagamentoType::getServicoDeCriptografia());
    }

    public function testGetName()
    {
        $type = Type::getType(DadosAdicionaisPagamentoType::NAME);

        $this->assertEquals(DadosAdicionaisPagamentoType::NAME, $type->getName());
        $this->assertNotEmpty($type->getName());
    }

    public function testConversion()
    {
        DadosAdicionaisPagamentoType::setServicoDeCriptografia(new MCryptServicoDeCriptografia('foo'));

        $dadosAdicionais = new DadosAdicionais();
        $dadosAdicionais->adicionar('foo', 'foo', false);
        $dadosAdicionais->adicionar('foo2', 'secret', true);
        $dadosAdicionais->adicionar('dont_persist', 'nono', false, false);
        $dadosAdicionais->adicionar('foo3', 'foo', false);

        $type = Type::getType(DadosAdicionaisPagamentoType::NAME);

        $serialized = $type->convertToDatabaseValue($dadosAdicionais, $this->getPlatform());
        $this->assertTrue(false !== $unserialized = unserialize($serialized));
        $this->assertInternalType('array', $unserialized);
        $this->assertEquals('secret', $dadosAdicionais->obter('foo2'), 'ExtendedData object is not affected by encryption.');
        $this->assertEquals('foo', $dadosAdicionais->obter('foo'), 'ExtendedData object is not affected by conversion.');
        $this->assertEquals('foo', $unserialized['foo'][0]);
        $this->assertNotEquals('secret', $unserialized['foo2'][0]);
        $this->assertEquals('foo', $unserialized['foo3'][0]);
        $this->assertTrue(!isset($unserialized['dont_persist']));

        /** @var DadosAdicionais $dadosAdicionais */
        $dadosAdicionais = $type->convertToPHPValue($serialized, $this->getPlatform());
        $this->assertEquals('foo', $dadosAdicionais->obter('foo'));
        $this->assertEquals('secret', $dadosAdicionais->obter('foo2'));
        $this->assertEquals('foo', $dadosAdicionais->obter('foo'));
    }

    protected function getPlatform()
    {
        return $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');
    }
}
