# Background Jobs API

API REST para procesamiento asíncrono de tareas construida con Laravel 11, Redis y MySQL. Permite despachar jobs a una cola de Redis, rastrear su estado en tiempo real y monitorear los workers a través del dashboard de Laravel Horizon.

## Stack

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=purpe)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel)
![Redis](https://img.shields.io/badge/Redis-alpine-DC382D?style=flat&logo=redis&logoColor=white)
![MySQL](https://img.shields.io/badge/MYSQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=flat&logo=docker)
![Nginx](https://img.shields.io/badge/Nginx-1.24-009639?style=flat&logo=nginx=)

| Tecnologia | Uso |
|------------|-----|
| Laravel 11 | Framework Principal |
| Redis | Driver de cola |
| MYSQL 8.0 | Base de Datos |
| Docker | Infraestructura |
| Laravel Horizon | Dashboard de workers |
| Laravel Sanctum | Autenticacion por token |
| Nginx | Servidor Web

## Cómo levantar el proyecto

### Requisitos previos
- Docker y Docker Compose
- Nginx en host
- PHP 8.3

### Instalacion

1. Clona el repositorio
```bash
git clone .
cd job-queue-system
```

2. Rellenar el .env.example
```bash

```