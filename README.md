# php-rest-api
Simple php library for using as REST api to use with php Mysqli driver.

In config.php you can: 
  - define link connection to MySQL database 
  - define routes. An array with each items having  properties: 
    - uri
    - file - file which contain model class
    - class - model class name
    - authorized - true (default) is url must be authorized
  - adding menu options if you want to create application menu on server side.
  - define a method for authorizing each uri.
  
Api calls can look like zis: city/getList, city/create, city/update?id=1, city/delete?id=2.

For each controller you need to create only one file which contain model definition as well. (Yes I know: the controller must be a separate file - you can change it if you didn't like this solution). 

All actions methods of model are named with ending action word (if you want to create other actions).

The base model has methods for all crud operations: create, update, delete, new. All operations are encapsulated of db system transaction, validate data before create,update. Also has actions for retrive data: getListAction($where, $order) si getItemAction($where). Response is sended as JSON with structure:

 - status = true/false, true if the action was completed without errors
 - data = contaion the response expected (item, list)
 - errors = contain error message if status is false

In samples folder, you can see how you can define models.

This php library and files from samples directory are used in my project:
http://bogdanim36.asuscomm.com:5018/#!/ax-frmk/features


