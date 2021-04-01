# Kubovo Toggle Tools

## Usage 

```
docker-compose up -d
docker-compose exec app composer install
```

Now paste your Toggle CSV report into `/data` folder
and run:
```
docker-compose exec app php bin/console toggle-tools:csv2daily
```