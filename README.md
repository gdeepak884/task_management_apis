## Task

## Set up:

1. Clone the repository
2. Run `composer install`
3. Run `php artisan key:generate`
4. Run `php artisan migrate` to create the database tables, select yes when prompted if database is not created manually
5. Run `php artisan db:seed --class=UserSeeder` to seed the database with a user
6. Run `php artisan serve` to start the server on `http://127.0.0.1:8000`
7. Use the postman collection to test the endpoints
 
## Endpoints:

### Auth Collection

- api/login - POST - Login

- api/me - GET - Get user details (requires token)

- api/logout - POST - Logout (requires token)

- api/refresh - POST - Refresh token (requires token)

### Task Collection

- api/tasks?name={name}&date={date}&status={status} - GET - Get all tasks with optional filters

- api/task/{id} - GET - Get a task by id

- api/add_task - POST - Add a task

- api/update_task/{id} - POST - Update a task

- api/delete_task/{id} - DELETE - Delete a task

- api/assign_task - POST - Assign a task to a user

- api/unassign_task - POST - Unassign a task from a user

- api/user_task/{id} - GET - Get all tasks assigned to a user by user id

- api/update_status - POST - Update the status of a task (requires token)

- api/user_tasks - GET - Get all tasks assigned to the logged in user (requires token)

## APIs Collection

[![Run in Postman](https://run.pstmn.io/button.svg)](https://api.postman.com/collections/13401788-ddf183a4-d83c-4074-a761-bd03c7a58bf7?access_key=PMAT-01HQAY4R9X77N5XQNCBV2SKZ4W)