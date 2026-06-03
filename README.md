# Laboratorio Practico N2 - Stack Web con Docker

Aplicacion web dinamica CRUD desarrollada con PHP, MariaDB y Docker Compose.

## Arquitectura

- `app`: contenedor PHP 8.3 con Apache y extension PDO MySQL.
- `db`: contenedor MariaDB oficial.
- `db_data`: volumen persistente para la base de datos.
- `laboratorio_net`: red virtual interna para comunicar app y base de datos.

## Requisitos

- Docker
- Docker Compose

## Ejecucion

Copiar el archivo de variables:

```bash
cp .env.example .env
```

Construir la imagen:

```bash
docker compose build
```

Levantar los contenedores:

```bash
docker compose up -d
```

Verificar estado:

```bash
docker ps
```

Ver logs de la base de datos:

```bash
docker logs laboratorio_db
```

Abrir la aplicacion:

```text
http://localhost:8080
```

Si se accede desde otra maquina de la red, usar la IP del servidor Debian:

```text
http://192.168.100.67:8080
```

## Operaciones CRUD

- Crear registro: boton "Crear registro".
- Leer registros: tabla principal.
- Modificar registro: boton "Editar".
- Borrar registro: boton "Borrar".

## Detener el proyecto

```bash
docker compose down
```

Para borrar tambien el volumen de datos:

```bash
docker compose down -v
```
