   
 PhpMyExplorer 1.2.1 (Classic)
 

Contents


	1- Presentation of PhpMyExplorer 1.2.1 
	2- Evolution compared to PhpMyExplorer 1.2 
	3- Script PHP and accountancies 
	4- To install PhpMyExplorer 1.2.1 
	5- To use PhpMyExplorer 1.2.1 in multi-user mode 
	6- To lock PhpMyExplorer 1.2.1 
	7- FAQ : frequently asked questions 
	8- Versions history 
 

1- Presentation of PhpMyExplorer 1.2.1

PhpMyExplorer 1.2.1 is a PHP application which allows you to easily update your site online without any FTP access. Like an explorer it allows you : 

to copy, move, delete, erase, or rename files and directories, 
to create directories, 
to upload files, 
to download files, 
to visualize files contents, edit and test files, 
customize the aspect of the application (language, colors ...). 

2- Evolution compared to PhpMyExplorer 1.2


For this new version of PhpMonExplorateur some new functionalities were brought :

Possibility to uload several files at the same time 
Possibility to edit and test files 
Improvement of the download function 
Improvement of various small functions (messages, files's size ...) 
New translation (German, Italian and Japanese) 
3- Script PHP and accountancies

PhpMonExplorateur 1.2.1 is a PHP script, consequently, it need that your shelterer Web server accepts PHP script execution. This script exists with extensions php3 or php. It is compatible with php3 and php4.
Server side :PhpMyExplorer 1.2.1 is compatible php3 and php4. It works with a Apache server under Windows 9x/NT/2000 and Linux environments, but it doesn't with Mac server.
Customer side : it wasn't tested with all current navigators : nevertheless it works with Internet Explorer 5 and superior, and Netscape 4.5 and 4.7.
If you encounter compatibility problems, don't hesitate to contact me.

4- To install PhpMyExplorer 1.2.1

There are two way to install PhpMyExplorer 1.2.1 : 

with installation procedure 
without installation procedure 
with installation procedure 

- Download PhpMyExplorer 1.2.1 and unzip it,
- Upload files PhpMyExplorer.elg and installfrancais.php3 on your web site root,
- Execute the script installenglish.php3 et follow instructions,

Script will extract the application and deal with all : it extracts the files, creates necessary directories and crushes previous version of PhpMyExplorer which installed in the same directory.
To download the two files, it is possible to pass from FTP connection. For that, you have to connect to your FTP account via your Browser : change the URL of your Browser and type this :


ftp://login@ftpserver

Where login is your ftp server access login and ftpserver is your FTP server name. Then, to upload the two files, you have to drag and drop them from a explorer (like explorer of Windows) to your browser.
Be carefull, a malfunctioning of installation process have been detected in rare cases for few configurations of PHP. Here a configuration which correctly works. In the file php3.ini (or php.ini) ensure you of the value of the following variables :


magic_quotes_gpc = On
magic_quotes_runtime = Off
magic_quotes_sybase = Off  

If you are not a Master of the configuration of your PHP server and if you have problems for installation or using application, So use the other version without procedure of installation. 


without installation procedure

- Download PhpMyExplorer 1.2.1 et unzip it,
- Upload theapplication on your web site with a FTP software

5- To use PhpMyExplorer 1.2.1 in multi-user mode

For the mode Multi-user, it is necessary to use PhpMyExplorer Multi-Users. Indeed, this mode isn't any more managed by this version. This other version is entirely dedicated to this mode.

6- To lock PhpMyExplorer 1.2.1

If you don't limit the access of PhpMonExplorateur 1.2.1, this application becomes a true hole of security. Indeed, any person who takes note of the presence of this application on your site can modify the contents or even erase the totality of your site. In order to avoid that, it is necessary to use the files access limitation of your Web server. For example, I present to you how to make access limitation for Apache web server. You must create the three files (in text format) : 

a .htaccess file in the directory of the application to limit the access to this directory, 
a password file in the secret directory, 
a .htaccess file in the same directory as the password file to prohibit the access to this file. 
Contents of the .htaccess file in the application directory :

AuthUserFile /secret/password
AuthName "Acces restreint"
AuthType Basic
<Limit GET POST>
   require valid-user
</Limit>

 

The password file is composed of the list of the users and their encrypted password with the Unix encoding.


login1:encrypted_password1
login2:encrypted_password2
login3:encrypted_password3
 

Contents of the .htaccesspassword


deny from all  

If you have problems don't hesitate to contact me : elegac@free.fr
For more information consult Apache directives.


7- FAQ : frequently asked questions


How to change the maximum upload file size ?

The maximun upload file size isn't fixed by the application but by the PHP configuration file (php.ini or php3.ini). In this file there is a variable which definite this limit :

upload_max_filesize = 2097152

By default, this variable is worth 2097152 bytes what represent aproximativement 2 Mb. 


How to prohibit the access to the application except for me ?

To prohibit the application access except for you or for other person, it is necessary to set up the access limitation of your Web server. For that, it is necessary to follow the instructions section To lock PhpMyExplorer of the manual. 



8- Versions history

PhpMyExplorer 1.0 - May 9th 2000 

Creation of the application. 

PhpMyExplorer 1.1.0 - June 1st 2000 

Creation of the installation procedure. 

PhpMyExplorer 1.1.1 - June 15th 2000 

Improvement of the installation procedure. 

PhpMyExplorer 1.1.2 - June 28th 2000 

Improvement of navigation in the application, 
Addition of a Portuguese translation. 

PhpMyExplorer 1.1.3 - August 24th 2000 

Possibility of using PhpMonExplorateur in mode multi-user, 
Possibility to modify the explorateur directory path, 
Improvement of copy function. 

PhpMyExplorer 1.1.4 - September 4th 2000 

Possibility to modify the application aspect (background color, title color, text color, links color ...), 
Possibility to hide options like the hour or the link "return to the site", 
Application heading and bottom of page are in files. 

PhpMyExplorer 1.1.5 - October 9th 2000 

application is entirely included in a same directory, 
Dynamic personalization of the application, 
Possibility of visualizing the files contents, 
Addition of Spanish and Dutch translations. 

PhpMyExplorer 1.2 - December 5th 2000 

the design was completely re-examined, 
using of Cascading Style Sheets (CSS), 
the file explorateur.php3 was replaced by the file index.php3, 
possibility to copy and move directories, 
possibility to download files, 
reverse sorting for name, date et size's file, 
improvement of the visualization of the files, 
improvement of the personalization of the application. 

PhpMyExplorer 1.2.1 - March 3th 2001 

Possibility to uload several files at the same time 
Possibility to edit and test files 
Improvement of the download function 
Improvement of various small functions (messages, files's size ...) 
New translation (German, Italian and Japanese) 


Author       : Erlé LE GAC
Site         : http://elegac.free.fr
email        : elegac@free.fr
Mailing list : phpmyexplorer-request@ml.free.fr?subject=subscribe