# VPL
Vpl is an activity module which helps in managing programming assignments in Moodle. I have tried to add the functionality of granting extension to  a vpl assignment. To try and test the changes which I have made you can follow the steps given below:
1. First you have to install moodle. I have tried these changes for moodle version 3.11. You need to install a version greater than 3.5. You can follow this [link](https://docs.moodle.org/400/en/Step-by-step_Installation_Guide_for_Ubuntu) to install Moodle.
2. Then you can download the zip file from this [link](https://drive.google.com/file/d/1znAP1K1fgBIFrH_1tlg2G2WATSHAoGAh/view?usp=sharing). This zip file is achieved by compressing the vpl directory in this repository.
3. After downloading the zip file you have to change the upload_max_filesize to more than 5MB in php.ini file whose location in ubuntu is /etc/php/7.4(php version)/apache2/php.ini. 
4. Restart the server which you are using. For me as I am using the apache server, I ran the following command in the terminal:
```
    sudo service apache2 restart
```  
5. Then you can login as admin into Moodle. Go to Site Administration > Plugins > Install Plugins and use the downloaded zip file to install the plugin.
![Site Administration > Plugins > Install Plugins](/images/plugininstaller.png)
6. If you have a write permission error, then you can use the following command 
```
    sudo chmod -R 0777 /var/www/html/moodle

 ```