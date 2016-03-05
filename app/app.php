<?php

    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/../src/Brand.php';
    require_once __DIR__.'/../src/Store.php';

    $server = 'mysql:host=localhost;dbname=shoes';
    $user = 'root';
    $password = 'root';
    $DB = new PDO($server, $user, $password);

    $app = new Silex\Application();
    $app->register(new Silex\Provider\TwigServiceProvider, array(
        'twig.path' => __DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Debug\Debug;
    Debug::enable();
    Request::enableHttpMethodParameterOverride();

    $app->get("/", function() use($app){
        return $app['twig']->render("index.html.twig");
    });

    // All Stores
    $app->get("/stores", function() use($app){
        return $app['twig']->render("stores.html.twig", array(
            'stores' => Store::getAll()
        ));
    });

    $app->post("/add_store", function() use($app){
        $new_store = new Store($_POST['name']);
        $new_store->save();
        return $app['twig']->render("stores.html.twig", array(
            'stores' => Store::getAll()
        ));
    });

    $app->delete("/store/{id}/delete", function($id) use ($app){
        $current_store = Store::find($id);
        $current_store->delete();
        return $app['twig']->render("stores.html.twig", array(
            'stores' => Store::getAll()
        ));
    });
    // delete all stores

    $app->delete("/delete_stores", function () use($app) {
        Store::deleteAll();
        return $app['twig']->render("stores.html.twig", array(
            'stores' => Store::getAll()
        ));
    });

    // Current Store
    $app->get("/store/{id}", function($id) use($app){
        $current_store = Store::find($id);
        return $app['twig']->render("current_store.html.twig", array(
            'store' => $current_store,
            'brands' => $current_store->getBrands()
        ));
    });

    $app->post("/store/{id}/add_brand", function($id) use($app){
        $current_store = Store::find($id);
        $new_brand = new Brand($_POST['name']);
        $new_brand->save();
        $current_store->addBrand($new_brand);
        return $app['twig']->render("current_store.html.twig", array(
            'store' => $current_store,
            'brands' => $current_store->getBrands()
        ));
    });
    // Edit current store

    $app->get("/store/{id}/edit", function($id) use($app){
        $current_store = Store::find($id);
        return $app['twig']->render("edit_store.html.twig", array(
            'store' => $current_store,
        ));
    });

    $app->patch("/store/{id}/edit", function($id) use($app){
        $current_store = Store::find($id);
        $new_name = ucwords($_POST['name']);
        $current_store->updateName($new_name);
        return $app['twig']->render("current_store.html.twig", array(
            'store' => $current_store,
            'brands' => $current_store->getBrands()
        ));
    });

    // delete current store brand

    $app->delete("/store/{id}/brand/{brand_id}/delete", function($id, $brand_id) use ($app){
        $current_store = Store::find($id);
        $current_brand = Brand::find($brand_id);
        $current_brand->delete();
        return $app['twig']->render("current_store.html.twig", array(
            'store' => $current_store,
            'brands' => $current_store->getBrands()
        ));
    });

    // Brands
    $app->get("/brands", function() use($app){
        return $app['twig']->render("brands.html.twig", array(
            'brands' => Brand::getAll()
        ));
    });

    $app->post("/add_brand", function() use($app){
        $new_brand = new Brand(ucwords($_POST['name']));
        $new_brand->save();
        return $app['twig']->render("brands.html.twig", array(
            'brands' => Brand::getAll()
        ));
    });

    $app->delete("/brand/{id}/delete", function($id) use ($app){
        $current_brand = Brand::find($id);
        $current_brand->delete();
        return $app['twig']->render("brands.html.twig", array(
            'brands' => Brand::getAll()
        ));
    });
    // Delete all brands

    $app->delete("/delete_brands", function () use($app) {
        Brand::deleteAll();
        return $app['twig']->render("brands.html.twig", array(
            'brands' => Brand::getAll()
        ));
    });
    // Current Brand
    $app->get("/brand/{id}", function ($id) use($app) {
        $current_brand = Brand::find($id);
        return $app['twig']->render("current_brand.html.twig", array(
            'brand' => $current_brand,
            'stores' => $current_brand->getStores()
        ));
    });

    $app->post("/brand/{id}/add_store", function($id) use($app){
        $current_brand = Brand::find($id);
        $new_store = new Store(ucwords($_POST['name']));
        $new_store->save();
        $current_brand->addStore($new_store);
        return $app['twig']->render("current_brand.html.twig", array(
            'brand' => $current_brand,
            'stores' => $current_brand->getStores()
        ));
    });
    // delete store from current brand
    $app->delete("/brand/{id}/store/{store_id}/delete", function($id, $store_id) use ($app){
        $current_brand = Brand::find($id);
        $current_store = Store::find($store_id);
        $current_store->delete();
        return $app['twig']->render("current_brand.html.twig", array(
            'brand' => $current_brand,
            'stores' => $current_brand->getStores()
        ));
    });
    $app['debug'] = true;
    $app->post("/search", function() use ($app){
        $search_term = ucwords($_POST['search']);
        $brands = Brand::getAll();
        $stores = Store::getAll();

        $returned_brands = array();
        $returned_stores = array();
        foreach($brands as $brand){
            if($brand->getName() ==  $search_term){
                array_push($returned_brands, $brand);
            }
        }
        foreach($stores as $store){
            if($store->getName() ==  $search_term){
                array_push($returned_stores, $store);
            }
        }

        return $app['twig']->render("results.html.twig", array(
            'brands' => $returned_brands,
            'stores' => $returned_stores
        ));
    });

    return $app;
?>
