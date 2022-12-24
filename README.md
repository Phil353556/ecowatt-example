# ecowatt-example
Example of how to use the API made available by french compagny RTE

This php script accepts and needs two parameters:
first can be   prod or  sandbox
second can be  local or web 

$ ./ecowatt.php
 ---------------------------------------------------------------------- 
 Usage:  php ecowatt.php { prod | sandbox ] [ local | web ]             
                                                                        
 first argument:                                                        
 prod: url for production information will be used                      
 sandbox: url for test information will be used                         
                                                                        
 second argument:                                                       
 local: information will be only displayed localy                       
 web: information will be displayed localy, generation for web done     
                                                                        
 otherwise usage is displayed                                         
 ---------------------------------------------------------------------- 
