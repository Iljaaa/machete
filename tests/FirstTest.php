<?php


class FirstTest extends \PHPUnit\Framework\TestCase
{
    public function testInstantiationOfMyLibrary() {
        $obj = new stdClass();
        $this->assertInstanceOf('\VendorNamespace\LibraryNamespace\MyLibrary', $obj);
    }
}
