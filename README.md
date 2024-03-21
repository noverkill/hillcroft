How to use:

1, Create a database for the app (or use existing one)

2, Install and run Mailhog locally

3, Create an .env file with the following added / modified:

- set DB connection params e.g. name and credentials

- setup Mailhog configuration in your .env file
    E.g.
    MAIL_HOST=127.0.0.1
    MAIL_FROM_ADDRESS="${APP_NAME}@localhost"
    set from address for the no stock product email

- set from email address for the no stock products email:
    E.g.
    PRODUCT_MAIL_TO=products@test.com                    

- set queue connection
    QUEUE_CONNECTION=database  (maybe it would work with redis as well /not tested/)

4, Run: sail artisan migrate

5, For using queued jobs run: sail artisan queue:work

6, Using the web intrface: 
   access the /product route in a web broweser 
   e.g. localhost/products to upload products.xml 
   and see the imported products in a data table

7, Using the command line to import products:
   sail artisan xml:import <path to xml>

8, Check mailhog web interface for emails sent
    
