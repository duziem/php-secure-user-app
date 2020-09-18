### This Application implements a tight and secure user registration and login system that protects against malicious attacks and harmful user actions such as: SQL injection, cross site request forgery(csrf), multiple registration attempts from a single user, unauthenticated access  
#php-secure-user-app  
#This document is a support guide to aid users in running this Application  
#This Application is designed in the PHP Programming Language  
#This Application makes use of an **MVC/OOP PHP pattern**  
#This Application leverages the Bootstrap framework and CSS for styling  
#This Application files and directory are contained in the directory named "Applicant app using mvc and oop pattern"  
#The Application main files are contained in the app directory  
#code has been included in the .htaccess file located in the public folder that points any URL Address entered into the browser to the '/public/index.php' file  
#URL Address entered into the browser is then parsed within the 'Application.php' file located in '/app/core' directory  
#This Application uses routes in navigating to the various parts of the application  

-----------------------FILE LOCATIONS-----------------------------  
#The apply, login and recover files are contained in the 'auth' folder located in: /app/views/auth  
#The confirm, status and detail files are contained in the 'home' folder located in: /app/views/home  
-----------------------FILE LOCATIONS-----------------------------  

  - For development purpose, this application was test run using the XAMPP package
  - Navigate to the htdocs folder located in: xampp\htdocs, then move the folder "Applicant app using mvc and oop pattern" into the htdocs folder
  - Navigate to the location of the folder "Applicant app using mvc and oop pattern" within the htdocs folder
  - Next navigate to the public folder: Applicant app using mvc and oop pattern\public in your terminal
  - Start a server at this location using a custom port e.g php -S localhost:800
  - The predefined Access code used to register Applicants is **10859**
  - Apply by clicking on the register link provided at the top of the home page
  - Upon successful application, you can login at anytime using the login link provided at the top of the home page
  - The root url of this application points to the home/homepage route:
    - This is the root page that is rendered when you enter the URI of your running server, e.g loclahost:800 into your preferred browser
  - Endeavour to use an updated browser to run this application
  
