# Wind Service

This code challenge will return a JSON resource with the current wind speed and direction for a given zipcode. The resource will be cached for 15 minutes and there is an included CLI command to bust the cache. Run `php artisan help cachebust` to learn more.

### How to run this code challenge

Clone the repository.
```
git clone https://github.com/therealbrandon/wind-service
```

Install dependencies:
```
composer install
```

Run the built-in web server:
```
php artisan serve
```

The welcome view should now be accessible by navigating to [http://localhost:8000](http://localhost:8000).

The wind resource should now be accessible by navigating to [http://localhost:8000/api/v1/wind/89101](http://localhost:8000/api/v1/wind/89101) or [http://localhost:8000/wind/89101](http://localhost:8000/wind/89101).
