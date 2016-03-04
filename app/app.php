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


    // Brands
    $app->get("/brands", function() use($app){
        return $app['twig']->render("brands.html.twig", array(
            'brands' => Brand::getAll()
        ));
    });

    $app->post("/add_brand", function() use($app){
        $new_brand = new Brand($_POST['name']);
        $new_brand->save();
        return $app['twig']->render("brands.html.twig", array(
            'brands' => Brand::getAll()
        ));
    });

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
        $new_store = new Store($_POST['name']);
        $new_store->save();
        $current_brand->addStore($new_store);
        return $app['twig']->render("current_brand.html.twig", array(
            'brand' => $current_brand,
            'stores' => $current_brand->getStores()
        ));
    });

    $app->delete("/brand/{id}/store/{store_id}/delete", function($id, $store_id) use ($app){
        $current_brand = Brand::find($id);
        $current_store = Store::find($store_id);
        $current_store->delete();
        return $app['twig']->render("current_brand.html.twig", array(
            'store' => $current_store,
            'brands' => $current_store->getBrands()
        ));
    });


    return $app;
?>
