# Sistema de Gestión de Prácticas

Este proyecto es una aplicación web desarrollada en **PHP**, **MySQL** y **Bootstrap**, diseñada para gestionar de manera organizada la información relacionada con **prácticas profesionales de estudiantes universitarios**.

---

## 🧩 Funcionalidades actuales

- CRUD completo de:
  - Estudiantes
  - Empresas
  - Supervisores externos
  - Informes por hito
  - Evaluaciones por estudiante
  - Hitos (catálogo editable)

- Relaciones con integridad referencial:
  - Cada estudiante está vinculado a una empresa (`empresa_id`)
  - Cada supervisor externo también está vinculado a una empresa (`empresa_id`)
  - Cada informe se vincula a un estudiante y a un hito
  - Cada evaluación se vincula a un estudiante (y opcionalmente a un hito)

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
├── hitos/
│ ├── listar.php
│ ├── crear.php
│ ├── editar.php
│ └── eliminar.php
├── informes/
│ ├── listar.php
│ ├── crear.php
│ ├── editar.php
│ └── eliminar.php
├── evaluaciones/
│ ├── listar.php
│ ├── crear.php
│ ├── editar.php
│ └── eliminar.php
├── sql/
│ ├── base_datos_inicial.sql
│ └── base_datos_hitos_informes_evaluaciones.sql
└── README.md


---

## 🧪 Caso real de ejemplo incluido

En el archivo `sql/base_datos_inicial.sql` se incluye el siguiente caso real modelado en el sistema:

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

## 🧱 Base de datos

Este proyecto utiliza dos archivos de configuración inicial:

- `sql/base_datos_inicial.sql` → estructura y datos base de estudiantes, empresas y supervisores.
- `sql/base_datos_hitos_informes_evaluaciones.sql` → estructura y datos iniciales para hitos, informes y evaluaciones.

Incluye integridad referencial con claves foráneas y eliminación en cascada (`ON DELETE CASCADE`).

---

## 📄 Manejo de Archivos en Informes y Evaluaciones

El sistema permite registrar archivos asociados a informes o evaluaciones bajo dos modalidades:

### ✅ Opción 1: Archivos locales
- Se guardan en una carpeta como `/archivos/`
- Solo es necesario escribir el nombre del archivo (ej: `hito1_constanza.pdf`)
- Se abrirán desde el mismo servidor local (`localhost`)

### ✅ Opción 2: Archivos externos en la nube (OneDrive / SharePoint)
- Copiar la URL pública o compartida desde OneDrive
- El sistema detecta si la URL comienza con `http` y genera automáticamente un enlace externo
- Ideal para universidades que trabajan con SharePoint / OneDrive institucional

---

## ⚙️ Cómo usar este sistema localmente

1. Clona o copia este repositorio en:  
   `C:\xampp\htdocs\gestion-practicas`

2. Abre [http://localhost/phpmyadmin](http://localhost/phpmyadmin) y:
   - Crea la base de datos `gestion_practicas`
   - Importa ambos archivos:
     - `sql/base_datos_inicial.sql`
     - `sql/base_datos_hitos_informes_evaluaciones.sql`

3. Inicia **Apache** y **MySQL** desde XAMPP

4. Accede al sistema en:  
   [http://localhost/gestion-practicas](http://localhost/gestion-practicas)

---

## 🧠 Evaluaciones según directriz UNAB

La evaluación de estudiantes se basa en hitos establecidos por la universidad, incluyendo:

- Informe Hito 1 (plan de trabajo)
- Informe Hito 2 (avance o cierre)
- Evaluación final por parte del supervisor

El módulo `evaluaciones/` permite registrar la nota, observaciones, fecha y un enlace a la rúbrica o acta en formato PDF.

---

## 📚 Futuras extensiones sugeridas

- Registro de entrevistas por parte del supervisor
- Asociación directa estudiante ↔ supervisor (además de empresa)
- Dashboard de resumen por periodo académico
- Exportar informes y notas a PDF o Excel
- Notificaciones por correo

---

## 👨‍🏫 Autor

Desarrollado por **Oscar Zúñiga** como solución práctica y adaptable para docentes universitarios a cargo de la supervisión de prácticas profesionales.
