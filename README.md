# php-rest-api
Simple php library for using as REST api
In config.php you can: 
  - define link connection to MySQL database 
  - define routes. An array with each items having  properties: 
    - uri
    - file - file which contain model class
    - class - model class name
    - authorized - true (default) is url must be authorized
  - adding menu options if you want to create application menu on server side.
  - define a method for authorizing each uri.
  
For each controller you need to create only one file with contain model definition as well. (Yes I know: the controller must be an separate file - you can change it if you didn't like mine solution).
All actions methods of model are named with ending action.
The base model has methods for all crud operations: create, update, delete, new. All operations are inside of transaction db system, validate data before create,update.


