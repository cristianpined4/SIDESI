# 🧩 SIDESI — Sección de Ingeniería de Sistemas Informáticos (FMO UES)

**SIDESI** (Sitio Web Oficial de la Sección de Ingeniería de Sistemas Informáticos) es una plataforma web institucional desarrollada en **Laravel** con **Livewire** y **Tailwind CSS**, compatible con **PostgreSQL**.  
Su propósito es **centralizar la gestión institucional**, automatizar los procesos de difusión de noticias y eventos, facilitar las **inscripciones en línea**, y ofrecer un repositorio documental y multimedia accesible para toda la comunidad académica de la **Facultad Multidisciplinaria Oriental de la Universidad de El Salvador (FMO UES)**.

---

## 🎯 Objetivo general

Diseñar e implementar el Sitio Web Oficial de SIDESI para centralizar la gestión de contenidos institucionales, eventos e inscripciones; facilitar la difusión académica; generar certificados digitales verificables con código QR; y ofrecer un repositorio de documentos y medios, garantizando seguridad, accesibilidad y alto rendimiento.

---

## 🚀 Funcionalidades principales

-   📰 **Gestión de noticias:** creación, edición y publicación de comunicados y convocatorias oficiales.
-   📅 **Eventos e inscripciones:** organización de talleres, conferencias y actividades con registro en línea y validación automática.
-   📄 **Documentos:** repositorio para reglamentos, guías y materiales académicos descargables.
-   🏆 **Certificados digitales:** generación automática de certificados con **validación QR**.
-   👥 **Gestión de usuarios:** roles definidos (administrador, docente, estudiante) con autenticación segura mediante **Laravel Web Auth**.
-   🔔 **Notificaciones y boletines:** alertas automáticas por correo y suscripción voluntaria a boletines informativos.
-   📊 **Reportes y métricas:** estadísticas automáticas de participación y asistencia, con reportes en PDF.
-   🎥 **Gestión multimedia:** galería institucional con fotografías y videos de eventos.
-   🔐 **Panel administrativo:** control centralizado de usuarios, contenidos y registros.

---

## 🧩 Secciones del sitio

-   🏠 **Inicio:** información general, noticias destacadas y enlaces rápidos.
-   📰 **Noticias:** publicaciones institucionales y comunicados de interés.
-   📅 **Eventos:** calendario académico, talleres y actividades con inscripción en línea.
-   📄 **Documentos:** acceso a reglamentos, informes y recursos institucionales.
-   📞 **Contacto:** formulario de comunicación directa con la Sección.

---

## 🛠️ Tecnologías empleadas

| Componente                | Herramienta                                |
| ------------------------- | ------------------------------------------ |
| **Lenguaje principal**    | PHP 8.2                                    |
| **Framework**             | Laravel 12                                 |
| **Interactividad**        | Livewire                                   |
| **Estilos**               | Tailwind CSS                               |
| **Base de datos**         | PostgreSQL / MySQL (compatibles)           |
| **Autenticación**         | Laravel Web (login, registro y roles RBAC) |
| **Entorno de desarrollo** | Visual Studio Code                         |
| **Gestión de tareas**     | Jira (metodología ágil SCRUM)              |

---

## 🧠 Metodología de desarrollo

El proyecto fue desarrollado bajo la **metodología ágil SCRUM**, utilizando sprints iterativos que permitieron la entrega continua de módulos funcionales, la validación con usuarios reales y la mejora progresiva del sistema.  
Cada iteración incluyó planificación, desarrollo, pruebas y revisión, asegurando calidad, trazabilidad y adaptabilidad a las necesidades institucionales de la FMO UES.

---

## 📦 Requisitos de instalación

### 🔧 Requisitos previos

-   PHP >= 8.2
-   Composer
-   Node.js y NPM
-   PostgreSQL o MySQL
-   Extensiones de PHP (OpenSSL, PDO, Mbstring, Tokenizer, XML, JSON, Ctype, ZIP)

### ⚙️ Pasos de instalación

```bash
# Clonar el repositorio
git clone https://github.com/cristianpined4/SIDESI.git

# Entrar al directorio del proyecto
cd SIDESI

# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node
npm install && npm run dev

# Copiar y configurar el entorno
cp .env.example .env

# Editar el archivo .env con tus credenciales
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=sidesi
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña

# Generar clave de la aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --seed

# Iniciar el servidor local
php artisan serve
```

---

## 🔐 Seguridad y cumplimiento

El sistema implementa **RBAC (Role-Based Access Control)** para la gestión de roles y permisos, asegurando trazabilidad y control de accesos.
Cumple con las normativas de:

-   **Ley de Acceso a la Información Pública (LAIP)**
-   **Ley de Protección de Datos Personales**
-   **Normas ISO/IEC 27001 y 25010**
-   **Pautas WCAG 2.1** para accesibilidad web
-   **Buenas prácticas OWASP Top 10** para seguridad en desarrollo Laravel.

---

## 💾 Infraestructura y alojamiento

El sistema puede alojarse en un **servidor institucional o dedicado**, con:

-   Certificado SSL (HTTPS)
-   Copias de seguridad automáticas
-   Panel de administración remoto
-   Disponibilidad 24/7 para consultas, inscripciones y descarga de documentos.

---

## 🤝 Contribuciones

Las contribuciones al proyecto son bienvenidas.
Realiza un _fork_, crea una rama con tus cambios y envía un _pull request_.

---

## 👥 Equipo de desarrollo

Proyecto desarrollado por estudiantes de **Ingeniería de Sistemas Informáticos** de la **Facultad Multidisciplinaria Oriental — Universidad de El Salvador**, como parte de la materia _Administración de Proyectos Informáticos_, bajo la asesoría del **Ing. César Misael Rodríguez Franco**.

### 👨‍💻 Colaboradores

-   **López Medrano, Gerardo Alexander** — LM20003
-   **Pineda Blanco, Cristian Alberto** — PB20002
-   **Viera Lazo, Edras Ariel** — VL20011
-   **Vásquez Vásquez, Andrés Isaí** — VV18009
-   **Álvarez Pérez, Carlos Vicente** — AP20007
-   **Santos Díaz, Eliseo Santos** — SD20007
-   **Bonilla Cortez, Oscar Alejandro** — BC18010
-   **Conde Salgado, Nelson Numan** — CS21027
-   **García Rivera, Billy Alexander** — GR20036
-   **Parada Barrero, Luis Andrés** — PB19022

---

## 🪪 Licencia

Este proyecto se distribuye bajo la licencia **MIT**.
Consulta el archivo [LICENSE](LICENSE) para más información.

---

## 🏛️ Institución

**Sección de Ingeniería de Sistemas Informáticos — FMO UES**
**Universidad de El Salvador**
📧 Contacto: [correo@ues.edu.sv](mailto:correo@ues.edu.sv)
📍 San Miguel Centro, San Miguel, El Salvador

---

## ⭐ Si este proyecto te fue útil o te inspiró, no olvides dejar una estrella en el repositorio.
