Symfony Bundle to quickly create data referential
=================================================

Installation:
-------------

To install this bundle, simply run the following command:
```
$ composer require mpp/referential-bundle
```

Then load the routes in the `config/routes.yaml:
```
# MppReferentialBundle
mpp_referential_routes:
    resource: .
    type: mpp_referential
    prefix: /v1
```