
Welcome to a small Newsletter-Application API built with Lumen 5.8

To get started, Create a file in the root directory of the project, call it `.env`.

Copy, paste and modify the following into your environment configuration set up.
Your database configuration in the .env file should be as follows:

```APP_ENV=local
   APP_DEBUG=true
   APP_LOG_LEVEL=debug
   APP_URL=http://localhost
```

### Database Configuration for localhost
```
DB_CONNECTION=mysql
DB_HOST=xxx
DB_PORT=xxx
DB_DATABASE=xxx
DB_USERNAME=xxx
DB_PASSWORD=xxx
```

### Firebase JWT configuration
```
APP_SECRET=XXX
SERVERNAME=newsletter
JWT_ALGORITHM=HS256
TOKENID=xxx
```

### Laravel Mail Configuration
```MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=xxx
MAIL_PASSWORD=xxx
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=subscription@newsletter.com
MAIL_FROM_NAME="Newletter app"
```

To be able to send mail, input your username (your gmail) and password (your gmail password) in the Mail configuration.

For you to be able to send email, you will need to turn on third party on your gmail setting.

After the configuration is done, run ```composer install``` in order to install all the dependency used in this project.
Then run ```php artisan migrate``` to migrate the tables.

When all these are done, you can start your server by running `php -S localhost:8000 -t public` while vagrant users should run `vagrant up`.
## Registration

In order to register a user in the application, send a post request to 
`POST v1/api/register` with the payload
```
{
	"name": "John Doe",
	"email": "jd@example.com",
	"password": "password"
}
```
This will return a json response with status `201 - created`
```
{
    "message": "Registration was successful"
}
```
Email and password are required fields, if they are not supplied, the following json response is seen:
```
{
    "email": [
        "The email field is required."
    ],
    "password": [
        "The password field is required."
    ]
}
```

## Authentication
In order to access some of the endpoints, there is a need to authenticate. If authentication is successful, a JWT token is generated and it will be a header to allow for access for the remaining endpoints. Send a post request to `POST api/v1/login` with payload:
```
{
	"email": "jd@example.com",
	"password": "password"
}
```
The following json response is returned:
```
{
	"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJEXXXXXXXXX"
}
```
Email and password are also required fields like in the Register endpoint. Email must be a valid email address. The token should be copied somewhere safe as it will be used as the header for authorization of some of the endpoints.

### Newsletter
In order to create a newsletter on this platform, a user needs to be registered and has to be authorized as an admin user. To create a newsletter, send post request to `POST api/v1/newsletter` with the following payload:
```
{
	"title": "title of newsletter",
	"description": "description of newsletter"
}
```
This endpoint needs the Authorization token for access. If the authorization token is for a regular user, the following json response is returned:
```
{
	"message":"User unauthorized due to access level, only 		
	admin user can perform this action."
}
```
For a successfull access, the following json response is returned:
```
{
	"message":"Newsletter was successful created"
}
```
Note that both title and description are required fields.
To delete a newsletter send a delete request to `DELETE /api/v1/newsletter/{id}` If the user is the owner of the newsletter and is an admin user, the following json response is returned with status `200`

```
{
	"message":"newsletter successfully deleted"
}
```
### Subscription
Any user can subscribe to any newsletter on the platform. In order to subscribe for a newsletter, send a post request to `POST /api/v1/subscription` with payload:
```
{
	"email": "jd@example.com",
	"newsletterId": 1
}
```
If the newsletter is not available in the platform, a status code 404 is returned with the json respose:
```
{
	"message":"Cannot find newsletter"
}
```
If the newsletter is available, a status code `201` is returned with json response:
```
{
	"message":"You have succesfully subscribed for this 	
	newsletter, please check your email and approve for 
	confirmation"
}
```
An email is sent to the user with the confirmation link, when a user clicks on the link it automatically confirmed the subscription. A status code `200` is returned with json resonse:

```
{
	"message":"You have succesfully confirmed your 
	subscription"
}
```
For an expired or already confirmed link, a status code `404` is returned with the following json response:
```
{
	"message":"We cannot confirm your subscription either 
	because you have already confirmed it or the link is 
	broken"
}
```
### Tests
<hr>

if you have phpunit installed globally (recommended), run
`phpunit`
Otherwise, run
`vendor/bin/phpunit`
