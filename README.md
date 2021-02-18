# google_spreadsheet_update
Adding data to google spreadsheet in symfony 5.3
# Requirement: 
1. Docker
2. Symfony 5.2
3. NGINX
4. Google developer access

#Step 1:
1. Login to https://console.developers.google.com
2. Click on Enable APIs and services
3. Select Google Sheets API 
4. Click on Enable API
5. Click on Create credentials
6. Fill the form:
![](create_credentials.png)
![](create_credentials2.png)
7. Download the credential file and save it to
```$xslt
add the file in the path /app/config/google-api-client/credentials.json
```
Add the location in your service.yml file line no. 16 if you are using and different location or file name.
```$xslt
$google_sheet_json: '%kernel.project_dir%/config/google-api-client/credentials.json'
```
#step 2: 
1. Create spreadsheet in your google drive
2. Copy the sheet ID which you will get in the url
#Step 3:
1. Install docker in your system
2. Go to the folder and run following command:
To build
```$xslt
>>> docker-compuse build
```
To run the containers
```$xslt
docker-compose up -d
```
enter the container
```$xslt
docker-compose exec app /bin/bash
```
We are ready to run the console command:
```$xslt
bin/console app:update-sheet <path of you xml file eg: currently: /var/www/html/coffee_feed.xml> <sheet ID, follow step2> <sheet name eg: sheet1>
```
Description:
1. If sheet is empty then if will add header and the records
2. If sheet in not empty then it will follow only the data
3. It use sheet ID and xml file path as a parameter sheet name is optional if its sheet1, it will check for sheet1
4. Exception are taken care
5. Logger are written but currently it is not adding in log file I need to debug that.
