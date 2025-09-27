# Proyecto Laravel + Livewire con Admin y Site

Este proyecto está estructurado para manejar **sitio web público (Site)** y **panel de administración (Admin)** usando **Laravel 10** + **Livewire** + **Bootstrap/JS para alerts y modales**.
Incluye comandos Artisan personalizados para generar **componentes Livewire**, **controladores CRUD** y vistas automáticamente.

---

## Estructura principal

```
app/
├── Http/
│   └── Controllers/
│       ├── Admin/
│       └── Site/
├── Livewire/
│   ├── Admin/
│   └── Site/
resources/
├── views/
│   ├── admin/
│   └── site/
```

-   **Admin**: panel de administración con layout `layouts.admin`.
-   **Site**: sitio público con layout `layouts.site`.
-   **Vistas y controladores** se generan automáticamente según la sección (`admin` o `site`).

---

## Comandos personalizados

### 1. Crear componente Livewire genérico con CRUD y subida de archivos

```
php artisan make:admin-livewire NombreComponente
php artisan make:site-livewire NombreComponente
```

**Opciones:**

-   `NombreComponente`: nombre del componente, se usa también para la vista y el controlador Livewire.
-   Genera:

    -   Clase Livewire (`App\Livewire\Admin\NombreComponente.php` o `App\Livewire\Site\NombreComponente.php`)
    -   Vista Blade (`resources/views/livewire/admin/nombre_componente.blade.php`)
    -   Funciones básicas: `render`, `store`, `update`, `edit`, `destroy`, `resetUI`
    -   Soporte para **subida de archivos** con `file_path`
    -   Búsqueda dinámica en todos los campos `$fillable` del modelo

---

### 2. Crear un controlador CRUD básico (Admin)

```
php artisan make:admin-controller Nombre
```

Genera:

```
App\Http\Controllers\Admin\NombreController.php
```

Con métodos:

-   `index()`: listar registros
-   `create()`: formulario de creación
-   `store(Request $request)`: guardar registro (con subida de archivos)
-   `edit($id)`: formulario de edición
-   `update(Request $request, $id)`: actualizar registro (con subida de archivos)
-   `destroy($id)`: eliminar registro (con eliminación de archivos)
-   Mensajes flash `with('success')` / `with('error')`
-   Uso de transacciones DB

---

### 3. Crear un controlador CRUD básico (Site)

```
php artisan make:site-controller Nombre
```

-   Igual que el controlador Admin pero apuntando a vistas de `site`.
-   Puede integrarse con **Livewire si se desea**.

---

## Uso de los componentes Livewire

1. **Agregar en la ruta**

```
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/usuarios', \App\Livewire\Admin\UsuariosController::class)->name('admin.usuarios');
});
```

2. **Mostrar componente en Blade**

```
<livewire:admin.usuarios-controller />
```

3. **Eventos de JS y modales** ya vienen integrados:

```js
document.addEventListener("livewire:initialized", function () {
    Livewire.on("cerrar-modal", function () {
        $(".modal").modal("hide");
    });
    Livewire.on("abrir-modal", function () {
        $(".modal").modal("show");
    });
    Livewire.on("refresh", function () {
        window.location.reload();
    });
});

const confirmarEliminar = async (id) => {
    if (
        await window.Confirm(
            "Eliminar",
            "¿Estas seguro de eliminar este registro?",
            "warning",
            "Si, eliminar",
            "Cancelar"
        )
    ) {
        Livewire.dispatch("delete", { id });
    }
};
```

> Nota: `window.Confirm` se puede definir usando **SweetAlert2**:

```js
import Swal from "sweetalert2";

window.Confirm = function (
    title,
    text,
    icon,
    confirmButtonText,
    cancelButtonText
) {
    return Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
    }).then((result) => result.isConfirmed);
};
```

---

## Recomendaciones

1. Ejecutar el enlace simbólico de storage para subir archivos:

```
php artisan storage:link
```

2. Asegurarse de que los modelos tengan `$fillable` correctamente definidos para que Livewire valide automáticamente los campos.

3. Puedes personalizar las **reglas de validación** en cada componente Livewire generado editando `store()` y `update()`.

4. Los layouts (`layouts.admin` y `layouts.site`) deben tener:

```
@stack('scripts')
```

para que funcionen los scripts de Livewire y los modales.

---

## Flujo típico para crear un módulo

1. Crear modelo:

```
php artisan make:model Producto -m
```

2. Crear componente Livewire Admin:

```
php artisan make:admin-livewire Producto
```

3. Crear controlador Admin (opcional si necesitas control clásico):

```
php artisan make:admin-controller Producto
```

4. Crear vistas Blade (si no quieres usar el stub Livewire generado) en:

```
resources/views/admin/producto/index.blade.php
resources/views/admin/producto/create.blade.php
resources/views/admin/producto/edit.blade.php
```

5. Agregar ruta y permisos:

```
Route::get('admin/productos', \App\Livewire\Admin\ProductoController::class)->name('admin.productos');
```

6. Listo para usar CRUD con Livewire, modales y subida de archivos.

---

## Notas finales

-   Livewire maneja automáticamente **eventos y validaciones**.
-   Puedes usar los mismos patrones para **Site** cambiando la carpeta y layout.
-   Los comandos personalizados te permiten **generar módulos completos en segundos** sin repetir código.

---
