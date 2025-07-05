# Sistema de Gestión de Prácticas

Este proyecto es una aplicación web básica desarrollada en **PHP**, **MySQL** y **Bootstrap**, orientada a la **gestión de prácticas profesionales de estudiantes**. Su propósito inicial es facilitar el control personal de los procesos de práctica, con la opción de ampliarlo y compartirlo con otros docentes en el futuro.

---

## 📦 Estructura del Proyecto

gestion-practicas/
├── index.php # Redirige al módulo principal
├── includes/
│ └── db.php # Conexión a la base de datos (PDO)
├── estudiantes/
│ ├── crear.php # Formulario para agregar estudiante
│ ├── editar.php # Formulario para editar estudiante
│ ├── eliminar.php # Elimina estudiante por ID
│ └── listar.php # Tabla con todos los estudiantes
└── sql/
└── base_datos_inicial.sql # Script para crear la BD y tabla


---

## ⚙️ Instalación local (con XAMPP)

1. Clona o descarga este repositorio en la carpeta:
C:\xampp\htdocs\

2. Abre XAMPP y asegúrate de que **Apache** y **MySQL** estén activos.

3. Entra a [http://localhost/phpmyadmin](http://localhost/phpmyadmin) y ejecuta el script SQL ubicado en `sql/base_datos_inicial.sql`.

4. Abre tu navegador y accede a:
http://localhost/gestion-practicas


---

## 🧪 Funcionalidades actuales

✅ CRUD completo para estudiantes:

- Crear estudiante  
- Editar estudiante  
- Eliminar estudiante  
- Ver todos los estudiantes registrados

---

## 🚀 Próximos módulos sugeridos

- Gestión de empresas y supervisores  
- Registro de prácticas asignadas  
- Seguimiento de informes (Hito 1, Hito 2, Evaluación final)  
- Rúbricas y retroalimentación  
- Exportación de datos

---

## 💡 Licencia

Este proyecto es de uso libre para fines educativos. Si lo modificas y mejoras, ¡siéntete libre de compartirlo!

---

