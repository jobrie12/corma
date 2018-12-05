<?php
namespace DataObject\TableConvention;

use Corma\DataObject\TableConvention\DefaultTableConvention;
use Corma\Test\Fixtures\ExtendedDataObject;
use Corma\Util\Inflector;
use PHPUnit\Framework\TestCase;

class DefaultTableConventionTest extends TestCase
{
    public function testGetTable()
    {
        $convention = new DefaultTableConvention(new Inflector());
        $this->assertEquals('extended_data_objects', $convention->getTable(ExtendedDataObject::class));
    }
}
