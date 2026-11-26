# Sistema de Venta de Medicamentos - San Juan (SJB)

Sistema web hecho con Laravel que incluye:

- CRUDs completos: roles, categorías, usuarios, clientes, medicamentos y ventas
- Dashboard con gráficos (Chart.js)
- Reportes de ventas en PDF (DomPDF) y Excel (Maatwebsite)
- PWA instalable

## Usuarios de prueba

Contraseña de todos los usuarios: **ESCRIBE_AQUI_TU_CONTRASEÑA**

| Rol | Correo |
|---|---|
| Administrador | admin@sanjuan.com |
| Químico farmacéutico | rquispe@sanjuan.com |
| Cajero | cmendoza@sanjuan.com |
| Cajero | ltorres@sanjuan.com |

## Instalación

1. Crear una base de datos MySQL llamada `ESCRIBE_AQUI_EL_NOMBRE_DE_TU_BASE`.
2. Copiar `.env.example` como `.env` y configurar en él los datos de la base de datos.
3. Ejecutar estos comandos en la carpeta del proyecto:

```
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

4. Abrir http://127.0.0.1:8000 en el navegador.

Opcional: en la carpeta `database/` hay un archivo `farmacia.sql` con la base de datos lista para importar.

## PWA (Progressive Web App)

Es una aplicación web que se comporta como una app instalada. Se implementó con tres piezas:

- `public/manifest.json`: nombre, iconos, colores y modo de pantalla completa.
- `public/sw.js`: service worker que guarda en caché los archivos estáticos y muestra la página `public/offline.html` cuando no hay internet.
- El registro del service worker en el layout principal.

Funciona sobre localhost o HTTPS y permite instalar el sistema en el escritorio o en el celular. Las páginas con datos de sesión no se guardan en caché por seguridad.
