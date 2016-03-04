<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disbaled
    */

    require_once 'src/Store.php';

    $server = 'mysql:host=localhost;dbname=shoes_test';
    $user = 'root';
    $password = 'root';
    $DB = new PDO($server, $user, $password);

    class StoreTest extends PHPUnit_Framework_TestCase
    {
        protected function teardown(){
            Store::deleteAll();
        }
        function test_getName(){
            // Arrange
            $name = "indexPDX";
            $new_store = new Store($name);

            // Act
            $result = $new_store->getName();

            // Assert
            $this->assertEquals('indexPDX', $result);
        }

        function test_save(){
            // Arrange
            $name = "indexPDX";
            $new_store = new Store($name);
            $new_store->save();

            // Act
            $result = Store::getAll();

            // Assert
            $this->assertEquals($new_store, $result[0]);
        }

        function test_getAll(){
            // Arrange
            $name1 = "adidas";
            $new_store1 = new Store($name1);
            $new_store1->save();

            $name2 = "indexPDX";
            $new_store2 = new Store($name2);
            $new_store2->save();

            // Act
            $result = Store::getAll();

            // Assert
            $this->assertEquals([$new_store1, $new_store2], $result);
        }

        function test_deleteAll(){
            // Arrange
            $name1 = "nike";
            $new_store = new Store($name1);
            $new_store->save();

            // Act
            Store::deleteAll();

            // Assert
            $result = Store::getAll();
            $this->assertEquals([], $result);
        }

        function test_updateName(){
            // Arrange
            $name1 = "nike";
            $new_store = new Store($name1);
            $new_store->save();

            // Act
            $new_name = 'indexPDX';
            $new_store->updateName($new_name);
            $result = $new_store->getName();

            // Assert
            $this->assertEquals('indexPDX', $result);
        }

    }
?>
