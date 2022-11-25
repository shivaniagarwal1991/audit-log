# audit-log

This application is resposible for:
- Creating a type for the event
- Creating an event based on the type
- Searching for events 

#### What do I bring to the table beside the expected assignment requirements?
- Basic API token authentication in request header (using UUID as a token for simplicity)
- Supporting the unicode chracters for detail so that everything can be saved in the logs.
- Returning the keys for the error message which can be localized for the user by the key and language.

#### Steps to run the application

- clone the project
- enter into the root folder of the application
- please run any one of the below commands to run the application
    1. php bin/console server:run 
    2. php -S localhost:8001 -t public

#### Step to setup the database
- **Option 1** run the below commands from the project root folder to set up a new database
        1. php bin/console doctrine:database:create
        2. php bin/console doctrine:migration:migrate
- **Option 2** Import the PROJECT_ROOT/audit_log.sql file to the database with some test data. 

#### Endpoints

**Note: we are using standard versioning technique by adding /v1/, /v2/ in uri for the API versioning.**

    **Possible Responses:**
       1. **400** - for bad requests where user didn't pass the required parameters or passed with invalid values etc.
       2. **404** - when we don't find any entity such as type, the user fetches the type but it doesn't exist
       3. **409** - when there is conflict let's say client wants to create a type that already exists.
       4. **200** - success response
       5. **201** - when entity is created successfully
       6. **401** - when the user is sending an invalid token
       7. **500** - when something went wrong but i wish we never see it :)

1. To add a type:
    POST       http://localhost:8001/v1/audit-log/event/type

    **Header Parameters:**
             1. x-api-key: we would need to pass the authentication key (sample: c0a062b7-b225-c294-b8a0-06b98931a45b1123)

    **Request Parameters (As JSON body):**
       1. type (mandatory): type of the event (example: info)
       2. status (optional): status of the event 1 - active, 0 - inactive (default: 1)

2. To add a event:
    GET       http://localhost:8001/v1/audit-log/event

    **Header Parameters:**
       1. x-api-key: we would need to pass the authentication key (sample: c0a062b7-b225-c294-b8a0-06b98931a45b1123)

    **Request Parameters (As JSON body):**
       1. type (mandatory): type of the event (example: info)
       2. detail (mandatory): detail of the event which can be string/JSON etc
       3. timestamp (optional): event timestamp (default: current DateTime)

3. To search events:
    GET       http://localhost:8001/v1/audit-log/search

    **Header Parameters:**
             1. x-api-key: we would need to pass the authentication key (sample: c0a062b7-b225-c294-b8a0-06b98931a45b1123)

    **Query Parameters:**
       1. type (optional): array of the types which needs to be searched (default: all)
       2. page_size (optional): number of events in each response (default: 10)
       3. page_no (optional): page number which needs to be fetched (default: 1)

#### What can we improve?

- We can create a docker-compose file to run the application. 
- We can write more detailed unit and integration test cases which will cover all corner cases. I have written some but not all.
- I agree that there are still some of the opportunities to reflector & clean the code along with custom exception handling etc.
