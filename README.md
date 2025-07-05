# Sistema de Gestión de Prácticas

Este es un sistema web desarrollado en **PHP**, **MySQL** y **Bootstrap** para gestionar estudiantes en práctica, empresas, y sus relaciones. Permite registrar, editar y eliminar información de manera sencilla y estructurada.

---

## 🧱 Estructura del Proyecto

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
├── sql/
│ └── base_datos_inicial.sql
└── README.md

---

## 🔗 Relaciones e integridad referencial

- Cada **estudiante** puede estar asociado a **una empresa** mediante el campo `empresa_id`
- Se utiliza **clave foránea** con `ON DELETE SET NULL` y `ON UPDATE CASCADE`
- Las empresas se gestionan de forma independiente desde el módulo `empresas/`

---

## ⚙️ Funcionalidades actuales

✅ CRUD completo de:
- Estudiantes (con vínculo a empresas)
- Empresas

🧩 Relaciones:
- Listar estudiantes con nombre de empresa
- Seleccionar empresa desde menú desplegable al crear/editar estudiante

---

## 🛠 Cómo instalar localmente (XAMPP)

1. Clona este repositorio en: `C:\xampp\htdocs\`
2. Abre `http://localhost/phpmyadmin`
3. Crea la base de datos `gestion_practicas`
4. Importa el archivo: `sql/base_datos_inicial.sql`
5. Inicia Apache y MySQL desde XAMPP
6. Abre en tu navegador: `http://localhost/gestion-practicas/`

---

## 💬 Créditos

Desarrollado por Oscar Zúñiga como proyecto de gestión de prácticas profesionales para docentes.
