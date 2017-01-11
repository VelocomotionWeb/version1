<?php
class PriceTest extends PHPUnit_Framework_TestCase
{
    // root@best-kit:/var/www/admin/www/[censored]/alex/presta1614/modules# /opt/php56/bin/php phpunit.phar --bootstrap bestkit_booking/tests/bootstrap.php tests/PriceTest

    public function testSecondsByBillablePeriod()
    {
        // Arrange
        //$a = new BestkitBookingProduct(1);

        // Act
        //$b = $a->negate();

        // Assert
        $this->assertEquals(86400, BestkitBookingProduct::getSecondsByBillablePeriod('days'));
    }

    // ...
}
