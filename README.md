# Gestión de Prácticas — Reportes 202420

Aplicación web en **PHP**, **MySQL** y **Bootstrap** para gestionar **prácticas profesionales** de estudiantes y generar reportes **Consolidado**, **Por Hito** y **Completo (hecho + pendientes)** con exportación a **XLS/CSV**.

> Esta versión incluye las vistas SQL y mejoras de dashboard para el período **202420**.

---

## 📚 Tabla de contenidos
- [Características](#-características)
- [Esquema de base de datos](#-esquema-de-base-de-datos)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Instalación](#-instalación)
- [Vistas SQL de reportes](#-vistas-sql-de-reportes)
- [Endpoints de reportes](#-endpoints-de-reportes)
- [Dashboard](#-dashboard)
- [Buenas prácticas del repo](#-buenas-prácticas-del-repo)
- [Tags / Topics del repositorio](#-tags--topics-del-repositorio)
- [Versionado (tags de git)](#-versionado-tags-de-git)
- [Solución de problemas](#-solución-de-problemas)
- [Autor y licencia](#-autor-y-licencia)

---

## ✨ Características

- CRUD de **Estudiantes**, **Empresas**, **Supervisores**, **Hitos**, **Informes**, **Evaluaciones** y **Entrevistas**.
- **Alertas** automáticas para vencimientos y pendientes.
- **Reportes**:
  - **Consolidado** (último por estudiante)
  - **Por Hito** (todas las evaluaciones)
  - **Completo** (hecho + pendientes por hito)
  - Exportación a **XLS** y **CSV**.
- Cálculo de rúbrica con corrección para **rúbricas 6 y 7** (LOGRO% × PONDERADOR).
- Dashboard con **chips de pendientes** por periodo.

---

## 🧩 Esquema de base de datos

Diagrama general (resumen):  

```
estudiantes ──┬─< informes >── hitos
              ├─< evaluaciones >── hitos
              ├─< entrevistas >── hitos
              └── empresas

evaluaciones ──< evaluaciones_criterios >── criterios ──< criterios_niveles
criterios ──└── rubricas ──> hitos
niveles_logro  (tabla catálogo)
supervisores   (externos, opcional en entrevistas)
actas_entrevista (detalle/actas por entrevista) 
```

**Tablas principales**  
- `estudiantes(id, nombre, email, rut, asignatura, empresa_id, fecha_inicio, fecha_fin, ...)`
- `empresas(id, nombre, rubro, direccion, email_contacto, ...)`
- `hitos(id, nombre, descripcion)`
- `informes(id, estudiante_id, hito_id, fecha_entrega, archivo, comentarios, ...)`
- `evaluaciones(id, estudiante_id, hito_id, nota, fecha_registro, supervisor, archivo, ...)`
- `evaluaciones_criterios(id, evaluacion_id, criterio_id, nivel_logro_id, puntaje_obtenido, comentario)`
- `rubricas(id, nombre, hito_id, tipo_practica)`
- `criterios(id, rubrica_id, nombre, orden, puntaje_max)`
- `criterios_niveles(id, criterio_id, nivel_logro_id, puntaje)`
- `niveles_logro(id, nombre, descripcion)`
- `entrevistas(id, estudiante_id, hito_id, fecha, modalidad, comentarios, supervisor_id, evidencia_url)`
- `actas_entrevista(id, entrevista_id, tipo_entrevista, eval_general, fortalezas, mejoras, ...)`
- `supervisores(id, nombre, cargo, email, telefono, tipo, empresa_id)`

> El PDF `gestion_practicas.pdf` contiene el diagrama más detallado.  

**Índices recomendados (extracto)**  
```
-- consultas por último registro
CREATE INDEX idx_inf_est_fech  ON informes(estudiante_id, fecha_entrega);
CREATE INDEX idx_eval_est_fech ON evaluaciones(estudiante_id, fecha_registro);
CREATE INDEX idx_ent_est_fech  ON entrevistas(estudiante_id, fecha);

-- detalle evaluación
CREATE INDEX idx_det_eval      ON evaluaciones_criterios(evaluacion_id);
CREATE INDEX idx_critniv_crit  ON criterios_niveles(criterio_id, nivel_logro_id);
```

---

## 🗂️ Estructura del proyecto

```
gestion-practicas/
├─ index.php                     # Dashboard con accesos a reportes
├─ includes/
│  ├─ db.php                     # NO versionado (local)
│  └─ db.example.php             # plantilla de conexión (.env opcional)
├─ reportes/
│  ├─ exportar_informe.php              # Consolidado (último por estudiante)
│  ├─ exportar_informe_hitos.php        # Por hito (todas las evaluaciones)
│  └─ exportar_informe_completo.php     # Hecho + pendientes (por hito)
├─ sql/
│  ├─ base_datos_inicial.sql
│  ├─ base_datos_hitos_informes_evaluaciones.sql
│  ├─ base_datos_entrevistas.sql
│  └─ vistas/
│     ├─ vw_informe_supervision_ultimo.sql
│     ├─ vw_informe_supervision_por_hito.sql
│     └─ vw_informe_supervision_completo.sql
└─ ...
```

---

## 🛠️ Instalación

1. **Clonar**
   ```bash
   git clone https://github.com/<usuario>/gestion-practicas.git
   cd gestion-practicas
   ```

2. **Configurar BD**
   - Copia `includes/db.example.php` → `includes/db.php` y completa credenciales **o** crea un `.env`:
     ```ini
     DB_HOST=localhost
     DB_PORT=3306
     DB_NAME=gestion_practicas
     DB_USER=root
     DB_PASS=
     DB_CHARSET=utf8mb4
     DB_TZ=-03:00
     ```

3. **Base de datos**
   - Crea la BD `gestion_practicas` e importa los SQL base (carpeta `sql/`).  
   - Crea las **VIEWS** (phpMyAdmin → SQL) pegando en este orden los archivos de `sql/vistas/`:
     1. `vw_informe_supervision_ultimo.sql`
     2. `vw_informe_supervision_por_hito.sql`
     3. `vw_informe_supervision_completo.sql`

   > Notas: en el consolidado se toma el **último por estudiante** (tiebreak por fecha y `id`).  
   > Las rúbricas **6 y 7** calculan nota como **LOGRO% × PONDERADOR**.

4. **Servidor**
   - XAMPP: carpeta en `htdocs/` → `http://localhost/gestion-practicas/`

---

## 🧮 Vistas SQL de reportes

- `vw_informe_supervision_ultimo` → Consolidado (una fila por estudiante, último hito evaluado).  
- `vw_informe_supervision_por_hito` → Una fila por **cada evaluación** (hito).  
- `vw_informe_supervision_completo` → **Todo por hito** (realizado + **pendiente** en informes/evaluaciones/entrevistas).

Cada archivo `.sql` contiene `DROP VIEW IF EXISTS ...; CREATE VIEW ...;` y comentarios.

---

## 🔗 Endpoints de reportes

### 1) Consolidado (último por estudiante)
```
/reportes/exportar_informe.php?ini=YYYY-MM-DD&fin=YYYY-MM-DD
  [&practica=PRACTICA%20I|PRACTICA%20II]
  [&formato=xls|csv]
```

### 2) Por Hito (todas las evaluaciones)
```
/reportes/exportar_informe_hitos.php?ini=YYYY-MM-DD&fin=YYYY-MM-DD
  [&practica=...]
  [&estudiante_id=ID]
  [&formato=xls|csv]
```

### 3) Completo (hecho + pendientes) por hito
```
/reportes/exportar_informe_completo.php?ini=YYYY-MM-DD&fin=YYYY-MM-DD
  [&practica=...]
  [&pendientes=1]
  [&formato=xls|csv]
```

---

## 📊 Dashboard

- Tarjetas de conteo (Estudiantes, Empresas, Informes, Evaluaciones, Entrevistas).
- **Sección Reportes** con accesos a Ver/XLS/CSV.
- **Chips** con pendientes (informes/evaluaciones/entrevistas) calculados desde `vw_informe_supervision_completo`.  
  Período por defecto configurable en `index.php` (`$iniDefault` / `$finDefault`).

---

## ✅ Buenas prácticas del repo

- **No versionar credenciales**: `includes/db.php`, `.env` están en `.gitignore`.
- **Evidencias**: carpeta `documentos/` des-trackeada (solo local).
- **SQL**: versionar todas las vistas en `sql/vistas/*.sql`.
- (Opcional) conservar estructura con `documentos/.gitkeep` y regla:
  ```
  documentos/**
  !documentos/.gitkeep
  ```

---

## 🏷️ Tags / Topics del repositorio

Sugeridos para GitHub (Settings → *Topics*):  
`php`, `mysql`, `pdo`, `bootstrap`, `university`, `internships`, `educational`, `reporting`, `xlsx`, `csv`, `dashboard`, `rubrics`, `practicas`, `unab`

---

## 🔖 Versionado (tags de git)

Crear un tag de release para esta versión de reportes:

```bash
git tag -a v202420-reportes -m "Reportes 202420: vistas SQL, exportadores XLS/CSV, dashboard y corrección rúbricas 6-7"
git push origin v202420-reportes
```

> Recomendación: proteger la rama `main` para requerir PRs.

---

## 🛟 Solución de problemas

- **Illegal mix of collations**: se evitó comparando por `IS NULL` en fechas. Asegurar `SET NAMES utf8mb4` en `db.php`.
- **Duplicados en consolidado**: resuelto con desempate por `id` cuando hay misma fecha.
- **Falta `includes/db.php`**: copiar desde `includes/db.example.php` o usar `.env`.
- **Pull borra documentos locales**: asegurar `/documentos/` en `.gitignore` y ejecutar  
  `git rm -r --cached documentos/` (mantiene archivos en disco).

---

## 👤 Autor y licencia

**Oscar Zúñiga**.  
Licencia: **MIT**.

