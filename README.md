# Background Jobs API

API REST para procesamiento asíncrono de tareas construida con Laravel 11, Redis y MySQL. Permite despachar jobs a una cola de Redis, rastrear su estado en tiempo real y monitorear los workers a través del dashboard de Laravel Horizon.

## Stack

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=purpe)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel)
![Redis](https://img.shields.io/badge/Redis-alpine-DC382D?style=flat&logo=redis&logoColor=white)
![MySQL](https://img.shields.io/badge/MYSQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=flat&logo=docker)
![Nginx](https://img.shields.io/badge/Nginx-1.24-009639?style=flat&logo=nginx&logoColor=green)

| Tecnologia | Uso |
|------------|-----|
| Laravel 11 | Framework Principal |
| Redis | Driver de cola |
| MYSQL 8.0 | Base de Datos |
| Docker | Infraestructura |
| Laravel Horizon | Dashboard de workers |
| Laravel Sanctum | Autenticacion por token |
| Nginx | Servidor Web |

## Cómo levantar el proyecto

### Requisitos previos
- Docker y Docker Compose
- Nginx en host
- PHP 8.3

### Instalacion

1. Clona el repositorio
```bash
git clone https://github.com/EddyDS23/job_queue_system
cd job_queue_system
```

2. Rellenar el .env.example
```bash
cp .env.example .env
vim .env
```

3. Levantar el contenedor docker y generar clave
```bash
sudo docker compose up -d --build
sudo docker compose exec app php artisan key:generate
```

4. Correr Migraciones
```bash
sudo docker compose exec app php artisan migrate
```

5. Levantar Horizon
```bash
sudo docker compose exec app php artisan horizon
```

>El dashboard estara disponible en http://localhost/horizon

## Estado de un Job

| Estado | Descripción |
|--------|-------------|
| `pending` | Job despachado esperando ser procesado |
| `processing` | Worker tomó el job y lo está ejecutando |
| `completed` | Job ejecutado exitosamente |
| `failed` | Job falló durante la ejecución |
| `cancelled` | Job cancelado por el usuario antes de procesarse |

### Flujo normal
```
pending → processing → completed
```

### Flujo con cancelación
```
pending → cancelled
```

### Flujo con error
```
pending → processing → failed
```

## Endpoints API

## Publicos

| Metodo | Ruta | Descripcion | Auth |
|--------|------|-------------|------|
| **POST** | `/api/register` | Registar usuario nuevo |  ❌ |
| **POST** | `/api/login` | Iniciar Sesion con usuario existente |  ❌ |

### POST `/api/register`

Registrar un nuevo usuario y retorno de token

**Body**
| Campo | Tipo | Requerido | 
|-------|------|-----------|
| name | string | ✔ |
| email | string | ✔ |
|password | string (min 8) | ✔ |

**Ejemplo**
```bash
curl -X POST http://localhost/api/register \
    -H "Content-Type: application/json" \
    -d '{"name":"test","email":"test@test.com","password":"12345678"}'
```

**Respuesta**
```json
{
    "user":{
        "name":"test",
        "email":"test@test.com",
        "updated_at": "2026-04-12T05:25:21.000000Z",
        "created_at": "2026-04-12T05:25:21.000000Z",
        "id": 1
    },
    "token":"1|ARe12AZq@SXa..."
}
```

### POST `/api/login`

Iniciar Sesion con usuario existente y retornar token

**Body**
| Campo | Tipo | Requerido | 
|-------|------|-----------|
| email | string | ✔ |
|password | string (min 8) | ✔ |

**Ejemplo** 
```bash
curl -X POST http://localhost/api/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"12345678"}'
```

**Respuesta**
```json
{
    "user":{
        "id": 1,
        "name": "test",
        "email": "test@etest.test",
        "email_verified_at": null,
        "created_at": "2026-04-12T05:25:21.000000Z",
        "updated_at": "2026-04-12T05:25:21.000000Z"
    },
    "token":"1|ARe12AZq@SXa..."
}
```


## Protegidos

> Los endpoints protegidos requieren el token obtenido en `/api/login` o `/api/register` en el header `Authorization: Bearer TU_TOKEN`.

| Metodo | Ruta | Descripcion | Auth |
|--------|------|-------------|------|
| **POST** | `/api/email` | Hacer un job de envio de email | ✔ |
| **GET** | `/api/jobs/{jobId}` | Ver estatus de job | ✔ |
| **DELETE** | `/api/jobs/{jobId}` | Cancelacion de un job | ✔ |

### POST `/api/email`

Procesar un job y despacharlo a redis

**Body**
| Campo | Tipo | Requerido | 
|-------|------|-----------|
| email | string | ✔ |

**Ejemplo**
```bash
curl -X POST http://localhost/api/email \
    -H "Authorization: Bearer ARe12AZq@SXa..." \
    -d '{"email":"test@test.com"}'
```

**Respuesta `200`**
```json
{
    "message":"Email en cola",
    "job_id":"4d7b35ab-fbc6-42aa-aa72-f87d7593884e"
}
```

**Respuesta sin token o invalido`401`**
```json
{
    "message":"Unauthenticated"
}
```

### GET `/api/jobs/{jobId}`

Observar estatus de un job (pending, processing, completed, cancelled)

**Ejemplo**
```bash
curl http://localhost/api/jobs/6b565fb9-2e1c-4f36... \
    -H "Authorization: Bearer KM12@K11m..."
```

**Respuesta `200`**
```json
{
    "job": {
        "id": 1,
        "job_id": "6b565fb9-2e1c-4f36-a80e-abdd2d9b5cab",
        "email": "test@test.comm",
        "status": "completed",
        "created_at": "2026-04-11T04:04:08.000000Z",
        "updated_at": "2026-04-11T04:04:18.000000Z"
  }
}
```

**Respuesta Sin token o invalido`401`**
```json
{
    "message":"Unauthenticated"
}
```

**Respuesta `404`**
```json
{
    "message":"Job not found"
}
```


### DELETE `/api/jobs/6b565fb9-2e1c-4f36...`

Cancelacion de un job mientras su estatus sea pendiente

**Ejemplo**
```bash
curl -X DELETE http://localhost/api/jobs/6b565fb9-2e1c-4f36... 
```

**Respuesta `200`**
```json
//Cancelacion por primera vez
{
    "message":"Job has been canceled"
}

//Job ya cancelado
{
    "message":"Job has already been canceled"
}
```

**Respuesta `401`**
```json
{
    "message":"Unauthenticated"
}
```

**Respuesta `404`**
```json
{
    "message":"Job not found"
}
```

**Respuesta `422`**
```json
//Job ya fue despachado o se encuentra en progreso
{
    "message":"You cannot delete job in processing"
}
```

## Informativos

| Metodo | Ruta | Descripcion | Auth |
|--------|------|-------------|------|
| **GET** | `/api/health` | Revision de estado operativo de la api |  ❌ |
| **GET** | `/api/info` | Informacion de nombre y entorno activo |  ❌ |

### GET `/api/health`

Verificar el estado de la api 

**Ejemplo**
```bash
curl http://localhost/api/health
```

### Valores posibles por campos
| Servicio | Estatus(Posibles) |
|----------|-------------------|
| **database** | `connected` / `disconnected` |
| **redis** | `connected` / `disconnected` |
| **horizon** | `running` / `paused` / `inactive` |


**Respuesta `200`**
```json
//Todos los servicios corriendo
{
  "status": "ok",
  "database": "connected",
  "redis": "connected",
  "horizon": "running",
  "timestamp": "2026-04-12T22:55:40.501595Z"
}
```

**Respuesta `503`**
```json
//Algun servicio caido
"status": "ok",
  "database": "disconnected",
  "redis": "connected",
  "horizon": "running",
  "timestamp": "2026-04-12T22:55:40.501595Z"
```

### GET `/api/info`

Retorno de nombre de la app y el entorno actual

**Ejemplo**
```bash
curl http://localhost/api/info
```

**Respuesta `200`**
```json
{
    "name_app": "JOB-QUEUE-SYSTEM",
    "environment_current": "local"
}
```

## Autenticacion

el token se obtiene cuando se inicia sesion o se registra un nuevo cliente este token se guarda en la base de datos y en cada request se compara el que se mando en el header `Authorization` con el de la bd que esta hasheado

**Obtener Token**
```bash
curl -X POST http://localhost/api/login
```
