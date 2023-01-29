# Extended Symfony Request
## Description
Extends [symfony/http-foundation](https://github.com/symfony/http-foundation) Request class to add parameters from url to request as $_GET based on string pattern.
## Example
New parameter will be added to Request::query  with **name** `sku` and **value** `xp-213`  
Url path: `/product/xp-213`  
```
Request::setUrlPatterns([
    "/product/{{sku}}"
]);
$request = Request::createFromGlobals();

$request->query->get("sku"); //xp-213
```