<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Chat API

## Requirements
    - If you want to see the code and download it : https://github.com/abisarirayndra/chat_api.git
    - Programming Language - Framework : PHP - Laravel 8
    - Composer : https://getcomposer.org/download/
    - XAMPP : https://www.apachefriends.org/download.html
    - Editor : https://code.visualstudio.com/download
    - API Tester : https://www.postman.com/downloads/
    
## How Run The Project
    - Install all the requirements
    - Download the code from github
    - Place the project you've downloaded to C:\xampp\htdocs
    - Extract it
    - Open XAMPP (start APACHE and MySQL)
    - Open VScode
    - Open folder C:\xampp\htdocs on VScode
    - Open the VScode terminal
    - Run "composer install"
    - Run "php artisan key:generate"
    - For check the project running well, open the postman then access "http://localhost/chat_api-main/public/" method Get will return the original page of Laravel
    - Open phpmyadmin then create new database "data_chat"
    - Open .env.example, edit configuration "DB_DATABASE = data_chat", "DB_USERNAME=[your phpmyadmin username]", "DB_PASSWORD=[your phpmyadmin password]"
    - Rename .env.example to .env
    - Open VScode Terminal, run "php artisan migrate"
    - Project ready to test
    
## Chat-APIs

- Account Register
    - The first step to access this project is register the account. It's need request(form-data):
        - name
        - email
        - password
        - phone_number
        - photo (file)
- Account Login
    - If the account successfully registered, you need to login for get the access to the project, it's need request (form-data) :
        - email
        - password
- User List (Need Auth)
    - This API can show you about the registered account except you. You will able to see who you will chat with.
- Latest Chat List (Need Auth)
    - This API can show you the information about your list chat history, you can also get information about the latest message, latest conversation with unread count
        information, and the basic information (name, phone number) about the recipient of your chats.
- Chat With (Need Auth)
    - This API informs you abaout the list of message from the spesific user according the request (phone number)
- Send Chat (Need Auth)
    - This API will send the message to recipient according the request
- Account Logout (Need Auth)
    - This API will logout your account and if you want to go back, you need login again.


## User Stories
### User story #0: Register and Login to access the API
    Scenario #1: Register Account
        -Test
            1. Open Postman
            2. hoose the POST method
            3. Fill e url "http://localhost/chat_api-main/public/api/account_register"
            4. Fill the form-data
                - name
                - email
                - password
                - phone_number
                - photo (file)
            5. Send
            6. Refister Success with return the account data
            
     Scenario #2 Loginunt
        -Test
            1. Open Postman
            2. hoose the POST method
            3. Fill e url "http://localhost/chat_api-mainic/api/account_login"
            4. Fill the form-data
                - email
                - password
            5. Send
            6. Login successfull with return data and token
            7. When you want to access API that need to be authentiated, you will need to fill the Bearer Token with the returned token
            
### User story #1: As a user, I want to be able to send message to other user, so that I will be able to share information with others.

    Scenario #1: User send new message
    Given: The user authenticated and choose receiver
    When: Fill the message
    And: Send the message
    Then: The system will creates new conversation between user and receiver
    And: Receiver will receive new message from user
    
    - Test
        1. Open Postman
        2. Login
        3. Choose the POST method
        4. Fill the url "http://localhost/chat_api-main/public/api/chat/send_to"
        5. Fill the form-data (phone_number, message)
        6. Send
        7. The API will return the message as the new conversation
    
    
    Scenario #2: User send empty message
    Given: The user authenticated and choose receiver
    When: Send the message without fill message
    Then: The system not create any conversation and there is no delivered message

    - Test
        1. Open Postman
        2. Login
        3. Choose the POST method
        4. Fill the url "http://localhost/chat_api-main/public/api/chat/send_to"
        5. Send
        6. The API will return the error message and will not to create the new conversation

### User story #2: As a user, I want to be able to reply message in existing conversation, so that I will be able to respond previous message.

    Scenario #1: User reply to existing conversation
    Given: The user authenticated and choose conversation
    When: Fill the message
    And: Send the message
    Then: The system will send new message to current conversation
    And: All user in current conversation able to see new delivered message
    
    - Test
        1. Open Postman
        2. Login
        3. Choose the GET method
        4. Fill the url "http://localhost/chat_api-main/public/api/chat/with"
        5. Fill the params with recipient phone number that you have chatted before
        6. Send
        7. The API will return the list of messages with the spesific user according the phone number
        8. Choose the POST method
        9. Fill the url "http://localhost/chat_api-main/public/api/chat/send_to"
        10. Fill the form-data with phone_number that you have chatted beforeand also the message
        11. Send
        12. The API will will send new message to current conversation
        13. Login with recipient account
        14. Choose the GET method
        15. Fill the url "http://localhost/chat_api-main/public/api/chat/with"
        16. Fill the params with sender phone number that chatted you before
        17. Send
        18. Recipient can see new delivered message also the current conversation

### User story #3: As a user, I want to be able to list messages from specific user, so that I will be able to read our conversation.

    Scenario #1: User listing all messages in specific conversation
    Given: The user authenticated
    When: Choose conversation
    Then: The system will returns list of messages for selected conversation
    
      -Test
        1. Open Postman
        2. Login
        3. Choose the GET method
        4. Fill the url "http://localhost/chat_api-main/public/api/chat/with"
        5. Fill the params with recipient phone number that you have chatted before
        6. Send
        7. The API will return the list of messages with the spesific user according the phone number
        
### User story #4: As a user, I want to be able to list conversations where I involved, so that I will be able to search or find user to chat with.

    Scenario #1: User listing all conversation they involved
    Given: The user authenticated
    When: List all conversation they involved
    Then: The system will returns list of conversations, and each conversation followed by:
        1. Last message
        2. User basic info (e.g name)
        3. Unread count
    
    -Test
        1. Open Postman
        2. Login
        3. Choose the GET method
        4. Fill the url "http://localhost/chat_api-main/public/api/latest_conversation_list"
        5. Send
        6. The API will return the list of conversation history contain (last message, user basic info (name, phone number), unread count

# Unit Test
    1. Open VScode
    2. Open Poject Folder
    3. Open Terminal
    4. Run vendor/bin/phpunit

