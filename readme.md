# Kubovo Toggle Tools

## Usage 

```
docker-compose up -d
docker-compose exec app composer install
```

Now download your Detailed monthly Toggle CSV report
 - toggl: `Reports -> This Month -> Detailed -> Download CSV`)
 - into `/data` folder
 - and run:

```
docker-compose exec app php bin/console toggle-tools:csv2daily
```