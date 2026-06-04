1. Need to add required detail in following files which files are located at application/config folder
- constants.php
- email.php
- google.php
- facebook.php
- config.php [GOOGLE_CAPTCHA_SITE_KEY, GOOGLE_CAPTCHA_SECRET_KEY]

2. From left bar menu 'General Management' see the system option menu in this section need to fill the require details like as below
- Social Media Configurations links
- 'Google Maps API key' under the Admin Configurations
- 'Google Web Client Id' under the Admin Configurations
- 'Default system Currency' under the Admin Configurations

3. Set cron path in cron setting in your cpnal
 - wget https://yourdomain.com/cron_file/onlinerestaurant -O /dev/null -o /dev/null
 - wget https://yourdomain.com/cron_file/restaurant_orderschedule -O /dev/null -o /dev/null
 - wget https://yourdomain.com/cron_file/autocallreminder -O /dev/null -o /dev/null
 - wget https://yourdomain.com/cron_file/autoCancelOrders -O /dev/null -o /dev/null
 - wget https://yourdomain.com/cron_file/deletelogs_olderthan_twomonths -O /dev/null -o /dev/null
 - wget https://yourdomain.com/cron_file/EventBookingReminder -O /dev/null -o /dev/null
 - wget https://yourdomain.com/cron_file/scheduledOrdersNoti -O /dev/null -o /dev/null
 - wget https://yourdomain.com/cron_file/scheduledOrdersNoti -O /dev/null -o /dev/null

4. Create one database on server and find the sql file from database folder.Import this file in your created database.

5. Download and install postman if you want. after download the postman and import the collection file from postman collection and environment form postman_collection folder in root.

6. If you want changes in language label, then find the language excel file from language_excel folder in root and make require changes if require then upload from system option >> Language File Configurations >> Language File (Website) and upload from here. Same for mobile app text

7. In your stripe account open the "https://dashboard.stripe.com/webhooks" and go on "https://dashboard.stripe.com/webhooks/create" this url and create your endpoint your like "https://yourdomain.com/v1/api/stripe_status_callback". And set the charge 'charge.refunded' and 'charge.refund.updated' events both are mandatory. Also you can other event if you want

8. Admin login
user name : support@hausdesdoeners.com
password : Error@123