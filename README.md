# Gif Browsing APP (Backend)

## Project setup
```
composer install
```

### Start development server
```
php -S localhost:3000 -t public
```

### Build image using docker
In case you want to use docker, you can build the image typing
```
docker build -t gif_browsing_app_api:1.0.0 .
```

### Run docker image

You need to set the enviroment variables, I recommend use a .env file, you can copy the example one and set the paramters. To start the image type
```
docker run --restart unless-stopped --env-file .env -d -p 3000:3000 --name gif_browsing_app_api gif_browsing_app_api:1.0.0
```
