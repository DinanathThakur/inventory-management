# Inventory Management

## Technologies used
1. HTML
2. CSS
2. JavaScript
2. PHP
2. MySQL 5.7
3. jQuery library
4. Bootstrap
5. DataTables

## File structure
### index.html
- Frontend related codes
- Table, forms

### app.php
- Main and entry point for php development
- In this file all the required classes are defined
  - Database
    - Main class to handle database connectivity
  - Request
    - Class to handle request related transactions
  - Item
    - Class to handle item related transactions
  - Summary
    - Class to handle summary related transactions
  - App
    - Main class to handle requests (acting as an api controller)
    - Handling all features
      - Create new request
      - List all requests with items and type of items
      - Edit request
      - Delete request

### script.js
- All the script required for this app is present here in this file
- Have created a main class to handle all the functionalities
### style.css
- Styles required for this app

## Features

- User can add new request
  - On click of Add request a bootstrap modal is shown to the user and the user can fill the required details to create new request
- User can view all requests
  - Here with the help of Datatables library loading all the requests
- User can edit request
  - User can click of gear icon and then click of edit option to edit request
    - A form with prefilled values will be displayed, and the user can update items
- User can delete request
  - User can click of gear icon and then click of edit option to delete request
    - A prompt will be shown to the user for the confirmation, upon confirmation the request will be deleted
- Order requests
  - This feature is there to order the requests
  - On click of order request in backend inserting the consolidated order record to summary table


## Screenshots

[Watch the video](https://www.awesomescreenshot.com/video/20765654)

[Uploaded to drive](https://drive.google.com/file/d/1siCy2I6Gqg43dS49gqy1hfYPkXcEDRMr/view?usp=sharing)
