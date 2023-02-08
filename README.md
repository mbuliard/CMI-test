# Blog api demo

This small provides a blog API with posts, comments, ratings

## Authentication
### API login
To login by API, simply send a POST request to `/api/login_check`, containing :
```json
{
  "username": "username",
  "password": "password"
}
```
If successful, the response will be :
```json
{
  "token": "exampleToken"
}
```

### Facebook login
Alternatively, you can connect with your facebook account with your browser on `/login/facebook`.
If successful, your account will be created if not previously created, 
and you will be redirected and given the token to use in API.

### Google login
You can also connect with your google account with your browser on `/login/google`.
If successful, your account will be created if not previously created,
and you will be redirected and given the token to use in API.

### Demo fixtures,
If fixtures are loaded, the following users can be used :
admin/admin
commentator/commentator
rater/rater

## API actions
### Members
The following actions are available to authenticated members :
* `GET /api/posts/` will list all Posts, 30 per page.
* `GET /apo/posts/{postId}/comments` will list all published Comments related to a Post, 30 per page. 
Comments responding to Comments are include.
* `POST /api/comments` to create a Comment. 
The JSON body must contain `body` and `parentId` (id of the Post or Comment related)
* `PATCH /api/comments/{commentId}` to update your Comment. Author only.
The JSON body must contain `body`.
* `DELETE /api/comments/{commentId}` to delete your Comment. Author only.
* `POST /api/comments/{commentId}/rating` to rate a Comment.
You cannot rate your own Comment or a Comment you already rated.

### Admin
All previous operations, plus :
* `GET /apo/posts/{postId}/comments` will also includes unpublished Comments
* `POST /api/posts` to create Post.
The JSON body must contain `title` and `body`.
* `PATCH /api/posts/{postId}` to update Post.
The JSON body must contain `body` and/or `title`.
* `DELETE /api/posts/{postId}` to delete Post.
* `DELETE /api/comments/{commentId}` to delete Comment.
* `POST /api/comments/{commentId}/publication` to publish a Comment.

### OpenApi doc
An OpenApi documentation is available here : `/api/docs`

## SetUp
### Configuration
1. Configure a database and set env `DATABASE_URL`.
2. Use migrations to create the schema : `bin/console d:m:m`.
3. Load fixtures if you wish : `bin/console d:f:l`.
4. Create Facebook login and set env `OAUTH_FACEBOOK_ID` and `OAUTH_FACEBOOK_SECRET`.
Or disable the login route in `security.yaml`.
5. Create Google SSO and set env `OAUTH_GOOGLE_ID` and `OAUTH_GOOGLE_SECRET`.
Or disable the login route in `security.yaml`.
6. Launch symfony built-in server : `symfony serve`.
7. Launch consumers for async operations : `bin/console messenger:consume`

### Create or update members
* To create new member : `bin/console app:member:create username plainPassword`.
Add `--admin` to give admin role to the new member.
* To update a password : `bin/console app:member:update-password username new PlainPassword`.