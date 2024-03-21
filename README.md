How to use:

1, Clone the repository && cd into directory

2, Run: composer install 

3, Create an .env file with the following added / modified:

- set DB connection params e.g. name and credentials                 

- set queue connection
    QUEUE_CONNECTION=database  (maybe it would work with redis as well /not tested/)

- setup and run Mailhog or similar to test email sending
  and configure realted params:
    E.g.
    MAIL_HOST=mailhog
    MAIL_PORT=1025
    MAIL_DASHBOARD_PORT=8025

- set from address for the no stock product email
    E.g. PRODUCT_MAIL_TO=products@test.com

5, Run: sail up

6, Run: sail artisan migrate 

7, For using queued jobs run: sail artisan queue:work

8, Using the web intrface: 
   access the /product route in a web broweser 
   e.g. localhost/products to upload products.xml 
   and see the imported products in a data table

9, Using the command line to import products:
   sail artisan xml:import [path-to-xml-file]

10, Check mailhog web interface for emails sent
