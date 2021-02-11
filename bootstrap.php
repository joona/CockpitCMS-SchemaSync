<?php

/* This addon expects that CloudStorage plugin is used.
 * CloudStorage addon sets up required dependencies for Cloud filesystem
 */


$this->module('schemasync')->extend([
  'list' => function() {
    $map = array();

    $collections = $this->app->filestorage->listContents("schemas://collections/"); 
    $singleton = $this->app->filestorage->listContents("schemas://singleton/"); 


    $map['collections'] = array();
    foreach ($collections as &$item) {
      $schema = $this->app->filestorage->read("schemas://".$item['path']);
      $map['collections'][$item['filename']] = json_decode($schema);
    }

    $map['singleton'] = array();
    foreach ($singleton as &$item) {
      $schema = $this->app->filestorage->read("schemas://".$item['path']);
      $map['singleton'][$item['filename']] = json_decode($schema);
    }

    return $map;
  },

  'syncCollection' => function($name, $schema) {
    $metapath = $this->app->path("#storage:collections/{$name}.collection.php");
  
    if (!$metapath) {
      $collection = $this->app->module('collections')->createCollection($name, $schema);
    } else {
      $collection = $this->app->module('collections')->updateCollection($name, $schema);
    }

    return $collection;
  },

  'syncSingleton' => function($name, $schema) {
    $metapath = $this->app->path("#storage:singleton/{$name}.singleton.php");

    if (!$metapath) {
      $singleton = $this->app->module('singletons')->createSingleton($name, $schema);
    } else {
      $singleton = $this->app->module('singletons')->updateSingleton($name, $schema);
    }

    return $singleton;
  },

  'syncSchemas' => function() {
    $rval = array();

    $collections = $this->app->filestorage->listContents("schemas://collections/"); 
    $singletons = $this->app->filestorage->listContents("schemas://singleton/"); 

    foreach ($collections as &$item) {
      $raw = $this->app->filestorage->read("schemas://".$item['path']);
      $schema = json_decode($raw, true);
      $collection = $this->syncCollection($item['filename'], $schema);
      array_push($rval, "collections:{$item['filename']}");
    }

    foreach ($singletons as &$item) {
      $raw = $this->app->filestorage->read("schemas://".$item['path']);
      $schema = json_decode($raw, true);
      $singleton = $this->syncSingleton($item['filename'], $schema);
      array_push($rval, "singleton:{$item['filename']}");
    }

    return array(
      'synced' => $rval
    );
  }
]);

if (COCKPIT_API_REQUEST) {
  $app->on('cockpit.rest.init', function($routes) {
    $routes['schema-sync'] = 'SchemaSync\\Controller\\RestApi';
  });
}
