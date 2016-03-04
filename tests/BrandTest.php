<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disbaled
    */

    require_once 'src/Brand.php';

    $server = 'mysql:host=localhost;dbname=shoes_test';
    $user = 'root';
    $password = 'root';
    $DB = new PDO($server, $user, $password);

    class BrandTest extends PHPUnit_Framework_TestCase
    {
        protected function teardown(){
            Brand::deleteAll();
        }
        function test_getName(){
            // Arrange
            $name = "yeezy";
            $new_brand = new Brand($name);

            // Act
            $result = $new_brand->getName();

            // Assert
            $this->assertEquals('yeezy', $result);
        }

        function test_save(){
            // Arrange
            $name = "yeezy";
            $new_brand = new Brand($name);
            $new_brand->save();

            // Act
            $result = Brand::getAll();

            // Assert
            $this->assertEquals($new_brand, $result[0]);
        }

        function test_getAll(){
            // Arrange
            $name1 = "adidas";
            $new_brand1 = new Brand($name1);
            $new_brand1->save();

            $name2 = "jordan";
            $new_brand2 = new Brand($name2);
            $new_brand2->save();

            // Act
            $result = Brand::getAll();

            // Assert
            $this->assertEquals([$new_brand1, $new_brand2], $result);
        }

        function test_deleteAll(){
            // Arrange
            $name1 = "nike";
            $new_brand = new Brand($name1);
            $new_brand->save();

            // Act
            Brand::deleteAll();

            // Assert
            $result = Brand::getAll();
            $this->assertEquals([], $result);
        }

        function test_updateName(){
            // Arrange
            $name1 = "nike";
            $new_brand = new Brand($name1);
            $new_brand->save();

            // Act
            $new_name = 'jordan';
            $new_brand->updateName($new_name);
            $result = $new_brand->getName();

            // Assert
            $this->assertEquals('jordan', $result);
        }

        function test_delete(){

            $brand_name = "adidas";
            $new_brand = new Brand($brand_name);
            $new_brand->save();

            // Act
            $new_brand->delete();
            $result = Brand::getAll();
            // Assert
            $this->assertEquals([], $result);
        }

    }
?>
