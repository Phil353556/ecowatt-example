# ecowatt-example
Example of how to use the API made available by french compagny RTE

Create an account, then register for this API:
https://data.rte-france.com/catalog/-/api/consumption/Ecowatt/v4.0
![image](https://user-images.githubusercontent.com/64729485/209450163-1126fd15-c855-4bd3-8608-7e46398d42b6.png)

Create an application for this API 

 ![image](https://user-images.githubusercontent.com/64729485/209463945-22ec7214-a722-4826-aee1-bd9e3f1aaa31.png)

Select this application and get the ID client and the ID secret  in base64
This is the only change to make in the script!
 ![image](https://user-images.githubusercontent.com/64729485/209463966-4b4dd73c-cf32-440c-9c28-88b9f5e5c834.png)

 ![image](https://user-images.githubusercontent.com/64729485/209464032-e1c4a30d-3c4f-489f-ab65-3ea9eb526f94.png)


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

Using URL for production:

![image](https://user-images.githubusercontent.com/64729485/209447409-f60b573c-d4d9-44ae-84d8-92b6269fa050.png)
![image](https://user-images.githubusercontent.com/64729485/209447359-9e84cc8d-b35e-4165-8bbf-45904e5df881.png)

One call per quarter is allowed. Meaning:

 -a call at H:01  next call possible at H+15 + 1s  
 -a call at H:10  next call possible at H+15 +1s  
 -a call at H:31  next call possible at H+45 +1s  
 so it is not in elapse time, strange but it is what it is. 

![image](https://user-images.githubusercontent.com/64729485/209450329-d7ef8d8c-5aae-433d-8506-011e2c7e9245.png)


Using URL for sandbox:

![image](https://user-images.githubusercontent.com/64729485/209450069-c2bd2923-6744-4094-872f-52bd777c916a.png)
![image](https://user-images.githubusercontent.com/64729485/209450077-aa5f0f40-4091-4151-80d2-0614980bf910.png)


