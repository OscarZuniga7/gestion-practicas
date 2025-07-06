# Sistema de Gestión de Prácticas

Este proyecto es una aplicación web desarrollada en **PHP**, **MySQL** y **Bootstrap**, diseñada para gestionar de manera organizada la información relacionada con **prácticas profesionales de estudiantes universitarios**.

---

## 🧩 Funcionalidades actuales

- CRUD completo de:
  - Estudiantes
  - Empresas
  - Supervisores externos

- Relaciones con integridad referencial:
  - Cada estudiante está vinculado a una empresa (`empresa_id`)
  - Cada supervisor externo también está vinculado a una empresa (`empresa_id`)

---

## 🗂️ Estructura del Proyecto

gestion-practicas/
├── index.php
├── includes/
│ └── db.php
├── estudiantes/
│ ├── listar.php
│ ├── crear.php
│ ├── editar.php
│ └── eliminar.php
├── empresas/
│ ├── listar.php
│ ├── crear.php
│ ├── editar.php
│ └── eliminar.php
├── supervisores/
│ ├── listar.php
│ ├── crear.php
│ ├── editar.php
│ └── eliminar.php
├── sql/
│ └── base_datos_inicial.sql
└── README.md


---

## 🧪 Caso real de ejemplo incluido

En el archivo `base_datos_inicial.sql` se incluye el siguiente caso real modelado en el sistema:

### Estudiante:
- **Nombre:** Nicolás Andrés Baeza Pereira
- **RUT:** 20269725-9
- **Programa:** UNAB12100
- **Asignatura:** Práctica I
- **Correo:** n.baezapereira@uandresbello.edu
- **Empresa:** Universidad Andrés Bello
- **Fecha Inicio / Fin:** 10-03-2025 a 02-06-2025

### Empresa:
- **Nombre:** Universidad Andrés Bello
- **RUT:** 60803000-0
- **Rubro:** Educación superior
- **Dirección:** Av. República 239, Santiago
- **Teléfono:** 226123456

### Supervisor Externo:
- **Nombre:** Armando Tamponi
- **Cargo:** Docente UNAB / Supervisor Externo
- **Correo:** arm.munoz@uandresbello.edu
- **Teléfono:** +56993997982
- **Empresa asociada:** Universidad Andrés Bello

---

## ⚙️ Cómo usar este sistema localmente

1. Clona o copia este repositorio en:  
   `C:\xampp\htdocs\gestion-practicas`

2. Abre [http://localhost/phpmyadmin](http://localhost/phpmyadmin) y:
   - Crea la base de datos `gestion_practicas`
   - Importa el archivo `sql/base_datos_inicial.sql`

3. Abre XAMPP y activa **Apache** y **MySQL**

4. Accede al sistema en:  
   [http://localhost/gestion-practicas](http://localhost/gestion-practicas)

---

## 📚 Futuras extensiones sugeridas

- Registro de entrevistas y evaluaciones por parte del supervisor externo
- Vinculación estudiante ↔ supervisor directamente (opcional)
- Reportes exportables a PDF o Excel
- Panel resumen tipo dashboard

---

## 👨‍🏫 Autor

Desarrollado por **Oscar Zúñiga** como solución práctica y adaptable para docentes universitarios a cargo de la supervisión de prácticas profesionales.

---

### 📎 Manejo de Archivos en Informes

El sistema permite registrar archivos asociados a informes de práctica bajo dos modalidades:

#### ✅ Opción 1: Archivos locales (modo tradicional)
- Deben ubicarse en la carpeta `/archivos/` dentro del proyecto local.
- En el formulario de creación/edición (`crear.php`, `editar.php`), basta con escribir el nombre del archivo, por ejemplo:
hito1_constanza.pdf
- En la lista de informes (`listar.php`), el archivo se abrirá desde el servidor local al hacer clic.

#### ✅ Opción 2: Archivos externos (SharePoint, OneDrive, etc.)
- Se debe pegar la **URL pública o compartida** del archivo, por ejemplo:

https://uandresbelloedu.sharepoint.com/.../Informe_Practica1_Constanza.pdf

- El sistema detectará automáticamente si se trata de una URL y la abrirá correctamente en una nueva pestaña.
- Ideal para archivos almacenados en OneDrive o SharePoint corporativo.

> 💡 Esta flexibilidad permite integrar almacenamiento local y en la nube, ideal para contextos universitarios o docentes que trabajan con plataformas institucionales.
