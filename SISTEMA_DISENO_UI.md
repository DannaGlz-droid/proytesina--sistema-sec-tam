# Sistema de diseño UI

Estado: versión 1.0 recomendada, definida con las pantallas piloto de Gestión de usuarios y Registro de usuario.

## 1. Propósito

Esta guía define la identidad visual y las reglas de interacción del sistema. Su objetivo es que cualquier módulo nuevo o rediseñado se vea y se comporte como parte del mismo producto, sin depender de decisiones aisladas por pantalla.

La identidad se divide en dos capas:

1. **Producto permanente:** tipografía, espaciado, controles, tablas, formularios, estados, navegación y accesibilidad.
2. **Tema institucional reemplazable:** logotipos y colores de gobierno. Debe poder cambiarse desde variables sin editar componentes ni vistas.

Las pantallas piloto son:

- Gestión de usuarios: referencia para páginas de consulta, tablas, filtros y paginación.
- Registro de usuario: referencia para formularios de alta y edición.

## 2. Principios

1. **Claridad antes que decoración.** Cada color, icono y superficie debe comunicar una función.
2. **Una acción principal por contexto.** Solo la acción que confirma el objetivo de la pantalla recibe el mayor énfasis.
3. **Marca contenida.** El color institucional no se usa como color general de interacción.
4. **Consistencia funcional.** Elementos que hacen lo mismo deben verse y comportarse igual.
5. **Densidad administrativa legible.** La interfaz puede ser compacta, pero nunca debe sacrificar lectura, foco o separación.
6. **Estados explícitos.** Carga, vacío, error, éxito, selección y deshabilitado deben estar diseñados.
7. **Accesibilidad por defecto.** El teclado, contraste, etiquetas y mensajes no son mejoras opcionales.

## 3. Fundamentos visuales

### 3.1 Tipografía

| Uso | Familia | Tamaño base | Peso | Interlineado |
|---|---|---:|---:|---:|
| Título de página | Lora | 1.32rem (21 px) | 700 | 1.15 |
| Descripción de página | Lora | 0.78rem (12.5 px) | 400 | 1.35 |
| Título de sección | Open Sans | 0.82rem (13 px) | 700 | 1.20 |
| Etiqueta de campo | Open Sans | 0.74rem (12 px) | 700 | 1.20 |
| Controles y botones | Open Sans | 0.78–0.80rem | 500–600 | 1.15 |
| Tabla | Open Sans | 0.75–0.80rem | 400–700 | 1.25 |
| Ayuda y metadatos | Open Sans | 0.68–0.72rem | 400–600 | 1.30 |

Reglas:

- Usar Lora únicamente para el encabezado editorial de la página.
- Usar Open Sans en navegación, formularios, tablas, botones, filtros y mensajes.
- Escribir títulos y secciones en estilo oración: “Gestión de usuarios”, no “Gestión De Usuarios”.
- Las descripciones deben explicar el propósito en una sola oración, con máximo aproximado de 65 caracteres por línea.
- No usar tamaños o familias distintas dentro de un mismo tipo de componente.

### 3.2 Paleta base permanente

Los nombres expresan función, no un color específico.

```css
:root {
  --ui-text: #10233f;
  --ui-text-secondary: #526278;
  --ui-text-muted: #64748b;
  --ui-border: #cbd5e1;
  --ui-border-soft: #dbe3ec;
  --ui-surface: #ffffff;
  --ui-surface-soft: #f8fafc;
  --ui-surface-section: #f3f4f6;
  --ui-surface-hover: #f1f5f9;

  --ui-focus: #475569;
  --ui-focus-ring: rgba(71, 85, 105, 0.18);

  --ui-success: #16a34a;
  --ui-success-text: #166534;
  --ui-warning: #d97706;
  --ui-warning-text: #92400e;
  --ui-danger: #dc2626;
  --ui-danger-text: #b91c1c;
  --ui-info: #2563eb;
  --ui-info-text: #1d4ed8;
}
```

### 3.3 Tema institucional

```css
:root {
  --brand-primary: #611132;
  --brand-primary-hover: #4a0e26;
  --brand-primary-soft: #f8f1f4;
  --brand-shell-secondary: #3a0b1f;
  --brand-shell-accent: #bc955c;
  --brand-on-primary: #ffffff;
}
```

Uso permitido del color institucional:

- Encabezado institucional y navegación principal.
- Acentos pequeños y exclusivos del shell, como separadores e indicadores activos, mediante `--brand-shell-accent`.
- Acción que confirma el objetivo: Guardar, Aplicar, Confirmar.
- Indicadores activos directamente ligados a una acción de marca, cuando sea necesario.

No usar el color institucional en:

- Asteriscos obligatorios.
- Foco de campos.
- Bordes normales o decorativos.
- Texto general, iconos informativos o enlaces comunes.
- Opciones activas de selects generales.
- Errores, advertencias o estados de éxito.

Cambiar de administración debe requerir modificar únicamente las variables `--brand-*` y los recursos de logotipo.

### 3.4 Colores semánticos y categóricos

- Rojo: error, peligro y campo obligatorio.
- Verde: éxito, activo, coincidencia o requisito cumplido.
- Ámbar: advertencia o atención.
- Azul: información y selección funcional cuando no represente marca.
- Gris: inactivo, pendiente, sin evaluar o dato secundario.

Los roles son categorías, no estados. Mantener un mapeo estable:

- Administrador: índigo suave.
- Coordinador: verde suave.
- Operador: ámbar suave.

Nunca cambiar el color de una categoría entre pantallas.

### 3.5 Espaciado, bordes y elevación

Escala oficial: `4, 8, 12, 16, 24, 32 px`.

- Separación entre icono y texto: 6–8 px.
- Separación entre campos relacionados: 12 px.
- Separación entre grupos: 16–24 px.
- Padding horizontal de página: 16 px móvil; 40–44 px escritorio.
- Radio de controles y botones: 7 px.
- Radio de menús y paneles flotantes: 8–10 px.
- Contenedores estructurales grandes: radio 0–10 px según jerarquía; no redondear todo por defecto.

### 3.6 Shell institucional y navegación

El encabezado global forma parte del sistema de diseño y no se ajusta de manera independiente por módulo.

- Barra institucional: 56 px en escritorio, color `--brand-primary`, contenido alineado al padding horizontal de página.
- Navegación secundaria: 41 px en escritorio, color `--brand-shell-secondary`.
- Logotipos y colores del gobierno viven únicamente en el shell y se sustituyen mediante recursos y variables de marca.
- Navegación, perfil, notificaciones y menús usan Open Sans; Lora queda reservada al encabezado editorial de cada página.
- Enlaces del shell: 12–13 px, peso 600, área interactiva mínima de 36 px.
- El elemento activo se comunica con texto blanco, fondo blanco translúcido tenue y un indicador inferior de 2.5 px en `--brand-shell-accent`; no depende sólo del hover.
- En el encabezado superior, el hover de los enlaces de texto —por ejemplo, Reportes y Estadísticas— usa texto blanco y un subrayado blanco de 1.5 px, sin crear bloques ni pestañas.
- En la navegación secundaria, el hover no altera el fondo ni el texto: muestra únicamente el indicador inferior dorado de 2.5 px. La sección activa conserva el mismo indicador y además utiliza texto blanco para distinguirse de una opción sólo apuntada.
- Los controles del encabezado que sólo contienen un icono se aclaran al pasar el cursor, sin círculos ni fondos decorativos; su foco de teclado permanece visible.
- El acento dorado no se propaga a formularios, tablas, botones, estados, modales ni contenido editorial.
- Los paneles de notificaciones, cuenta y submenús usan superficie blanca, borde neutral, radio de 8–10 px y la misma elevación que los demás paneles flotantes.
- Dentro de paneles desplegables no se usa el color institucional para hover, foco o texto general; se usan superficies y foco neutrales.
- Los paneles se abren y cierran con una transición breve de opacidad y desplazamiento vertical; no escalan ni rebotan.
- Las notificaciones usan títulos en escritura normal, acciones neutrales y un punto azul pequeño para distinguir las no leídas; el fondo completo no se colorea con la marca.
- El menú de cuenta muestra una identidad compacta y opciones con iconos simples, sin recuadros decorativos. Sólo “Cerrar sesión” utiliza el color semántico de peligro.
- Los pies de estos paneles describen el destino con precisión —por ejemplo, “Ver todas las notificaciones”— y no mezclan términos de otros módulos.
- Notificaciones y perfil tienen botones con nombre accesible, `aria-expanded` y foco visible.
- En móvil se conservan las rutas principales en una segunda línea del encabezado; no se ocultan destinos sin ofrecer una alternativa equivalente.
- La barra secundaria permite desplazamiento horizontal cuando no caben sus opciones, sin truncarlas.
- Toda animación dura 120–200 ms y se elimina con `prefers-reduced-motion`.
- Sombra de controles: mínima, `0 1px 2px`.
- Sombra de menús flotantes: visible pero fría, basada en azul grisáceo; no usar negro puro.

## 4. Estructura de página

### 4.1 Encabezado de página

Orden oficial:

1. Enlace de regreso, solo en páginas hijas.
2. Título.
3. Descripción.
4. Acción de navegación de la página, alineada a la derecha cuando exista.
5. Separador inferior.

Reglas:

- Listados raíz no llevan enlace de regreso.
- Altas, ediciones y detalles llevan “← Volver a [nombre del módulo]” encima del título.
- Al volver desde edición o detalle, los listados restauran durante la sesión la página, búsqueda, filtros, orden y cantidad de filas anteriores.
- No repetir el regreso en el pie del formulario.
- Una acción como “Crear usuario” puede ir a la derecha del encabezado.
- Título y descripción deben usar siempre el componente compartido, no clases locales.

### 4.2 Ancho de contenido

- Páginas de datos: ancho fluido para aprovechar la tabla.
- Formularios: ancho máximo oficial de 1440 px, centrados dentro de la página.
- Si un formulario no funciona correctamente en 1440 px, debe reorganizarse por secciones o pasos; no debe ensancharse.
- Texto informativo: máximo 65 caracteres por línea.

## 5. Botones y acciones

### 5.1 Tamaños

| Tamaño | Alto | Uso |
|---|---:|---|
| Compacto | 30 px | Acciones dentro de tablas o ayudas contextuales |
| Estándar | 34 px | Formularios, toolbars y encabezados |
| Móvil | 40 px mínimo | Acciones principales en pantallas táctiles |

Todos usan Open Sans, 0.78rem, peso 600, radio de 7 px y padding horizontal de 13–14 px. El énfasis del botón primario proviene del color y la posición, no de aumentar el texto a 700.

Excepciones:

- Controles compuestos como “Mostrar 10” usan peso 500 en la etiqueta y 700 únicamente en el valor seleccionado.
- Opciones de menú usan 500; la opción activa puede usar 700.
- Títulos, etiquetas de formulario y encabezados de sección conservan 700.

### 5.2 Jerarquías

**Primario de confirmación**

- Fondo `--brand-primary`, texto blanco.
- Para Guardar, Aplicar filtros, Confirmar o completar una operación.
- Solo uno por grupo de acciones.

**Secundario neutral**

- Fondo blanco, borde neutral, texto azul grisáceo oscuro.
- Para Limpiar, Cancelar, Generar contraseña, Exportar o acciones alternativas.

**Enlace de navegación**

- Sin contenedor visual cuando el propósito es regresar o cambiar de sección.
- Debe incluir texto; una flecha sola no es suficiente.
- Al regresar sin guardar desde un formulario abierto en un listado, preferir navegación por historial para restaurar inmediatamente la tabla, su página, filtros, búsqueda y datos ya renderizados.
- Preparar esa restauración desde el momento de entrar al formulario, no únicamente al pulsar su enlace interno de regreso, para que funcione también con el botón Atrás del navegador. Mantener la instantánea visual hasta 30 minutos; si los datos almacenados ya no son recientes, mostrarla mientras se revalida la tabla en segundo plano.
- Cuando el navegador no conserve la página en memoria, reutilizar un caché de sesión breve del último bloque renderizado y revalidarlo silenciosamente, sin sustituir las filas por un loader.
- Para evitar cambios de estructura durante la rehidratación al volver desde un formulario, conservar además una instantánea visual inerte del componente de tabla y retirarla en cuanto la tabla real termine su primer render. La instantánea nunca recibe foco ni interacción y no se reutiliza en una recarga explícita (F5) ni en un acceso directo, donde debe mostrarse el estado normal de carga con datos actualizados.
- Conservar una URL de respaldo para accesos directos o cuando el historial no corresponda al listado esperado. Invalidar el caché después de una mutación y recargar los datos para evitar información obsoleta.
- Separar por usuario autenticado todas las claves de almacenamiento de tablas. Cerrar sesión no equivale a cerrar la pestaña y no limpia automáticamente `sessionStorage` ni `localStorage`; ninguna cuenta debe heredar filtros, búsqueda, página, orden o cantidad de filas de otra.
- Las preferencias estables —cantidad de filas y orden— pueden persistir para cada usuario entre visitas. El contexto temporal —búsqueda, filtros y página— se restaura al recargar o regresar desde una vista relacionada, pero se reinicia al entrar nuevamente al listado desde la navegación normal.

**Peligro**

- Rojo únicamente para eliminar, desactivar permanentemente o una acción irreversible.
- Pedir confirmación y explicar la consecuencia.

**Solo icono**

- Permitido para acciones universales y repetitivas: menú de fila, cerrar, mostrar contraseña.
- Área interactiva mínima de 32 × 32 px en escritorio y 40 × 40 px en móvil.
- Requiere `aria-label` y tooltip.

### 5.3 Política de iconos

- La familia oficial es **Font Awesome**. Ya es la más utilizada por el sistema y está disponible desde el layout principal.
- Los iconos apoyan la comprensión; no reemplazan etiquetas importantes.
- Usar icono en Crear, Filtrar, Buscar, Descargar, menú de acciones y navegación de regreso.
- En menús de acciones de tabla, usar icono más texto: lápiz para Editar, llave para Cambiar contraseña y papelera para Eliminar. Mantener los iconos alineados en una columna y reservar el rojo únicamente para la opción destructiva.
- Usar una llave para Generar contraseña; evitar chispas u otros símbolos ambiguos.
- En campos de contraseña, usar `far fa-eye` y `far fa-eye-slash` con color gris neutro, tamaño visual de 15–16 px y un área transparente de 40 × 40 px. El control debe actualizar `aria-label` y tooltip entre “Mostrar contraseña” y “Ocultar contraseña”; no usar el color institucional.
- Guardar, Cancelar, Limpiar y Aplicar no necesitan icono si el texto es inequívoco.
- No mezclar familias dentro de un mismo componente. Los Ionicons existentes se reemplazarán gradualmente al migrar cada pantalla, sin hacer una sustitución global riesgosa.
- Mantener el mismo grosor visual y tamaño entre iconos equivalentes.
- Iconos normales se colocan antes del texto; chevrons de apertura se colocan al extremo derecho.

### 5.4 Orden de acciones

- Escritorio: secundarias a la izquierda y primaria al extremo derecho.
- Móvil: apiladas; primaria primero visualmente si el flujo lo requiere, sin cambiar el orden lógico del teclado.
- Separación: 8–9 px.
- No colocar acciones de navegación dentro del grupo de envío del formulario.

## 6. Formularios

### 6.1 Estructura

- Agrupar por significado, no por cantidad de campos.
- Cada sección usa una franja gris clara de 38 px, icono opcional y título.
- Dos columnas desde 1024 px; una columna debajo de ese ancho.
- Mantener campos relacionados en la misma fila: correo/teléfono, contraseña/confirmación.
- Pie de acciones separado por borde superior.

### 6.2 Campos

- Alto estándar: 34 px en escritorio y 40 px en móvil.
- Fondo blanco, borde neutral y radio de 7 px.
- Label encima; no usar placeholder como sustituto de label.
- Placeholder solo como ejemplo o formato: “Ej: usuario@ejemplo.com”.
- Asterisco obligatorio en `--ui-danger`.
- Ayuda persistente debajo del campo cuando el formato no sea evidente.

Estados:

- Hover: fondo gris muy claro y borde ligeramente más oscuro.
- Foco: borde `--ui-focus` y anillo de 2 px con `--ui-focus-ring`.
- Error: borde rojo y mensaje inline debajo del campo.
- Correcto: no pintar todo el campo de verde; mostrar confirmación discreta cuando aporte valor.
- Deshabilitado: fondo gris, texto atenuado y cursor correspondiente.

### 6.3 Selects

- Todos los selects visibles de una misma pantalla deben usar el mismo componente.
- Usar Tom Select para consistencia visual entre navegadores.
- Habilitar búsqueda cuando haya más de 10 opciones o la lista pueda crecer.
- Deshabilitar búsqueda cuando haya 10 opciones o menos y el catálogo sea estable; activarla aunque la lista sea corta si se prevé un crecimiento considerable.
- Configuración de los formularios de usuario: Distrito con búsqueda; Cargo, Rol y Estado sin búsqueda.
- Conservar selección anterior, validación, navegación por teclado y limpieza del formulario.
- “Seleccione…” funciona únicamente como placeholder; no debe aparecer como una opción elegible dentro del menú.
- El menú debe mostrarse completo sobre las secciones y acciones siguientes; ningún contenedor del formulario puede recortarlo.
- Diferenciar sin iconos el valor seleccionado y la opción activa: el seleccionado conserva un fondo neutral muy tenue y peso 600; la opción recorrida por cursor o teclado usa un gris ligeramente más marcado.
- Si la opción seleccionada también está activa, prevalece el fondo del estado activo y se conserva el peso 600.
- La opción activa usa azul grisáceo o neutral, no el color institucional.

### 6.4 Validación

- Validar al salir del campo y al enviar; no mostrar errores mientras el usuario apenas empieza a escribir.
- Mensajes directos: “Seleccione un distrito”, no “Campo inválido”.
- El primer campo inválido recibe foco.
- Los errores del servidor deben regresar al mismo componente y conservar los valores válidos.
- No depender únicamente de alertas del navegador o del color.

### 6.5 Contraseñas

- Fortaleza y reglas pertenecen al campo Contraseña.
- Coincidencia pertenece a Confirmar contraseña.
- Generar contraseña se coloca con la confirmación porque completa ambos campos.
- El generador debe llenar contraseña y confirmación y usar aleatoriedad criptográfica.
- Mostrar contraseña es una acción de solo icono con etiqueta accesible.
- Los estados de seguridad usan colores semánticos, nunca color de marca.

### 6.6 Cambios sin guardar

- Los formularios de edición comparan su estado actual con el estado inicial; abrir la vista sin modificarla nunca genera una advertencia.
- Al intentar navegar mediante un enlace interno con cambios pendientes, usar el diálogo compartido “Descartar cambios”.
- Al recargar, cerrar la pestaña o usar la navegación del navegador, usar la advertencia nativa mediante `beforeunload`.
- Enviar correctamente el formulario no muestra una advertencia de salida.
- “Restablecer cambios” solicita confirmación únicamente cuando existen cambios y devuelve todos los controles, incluidos los selects enriquecidos, a su valor inicial.
- Si el usuario devuelve manualmente todos los campos a sus valores iniciales, el formulario deja de considerarse modificado.

## 7. Tablas y páginas de consulta

### 7.1 Composición

1. Encabezado de página.
2. Toolbar: filtros a la izquierda; búsqueda y tamaño de página a la derecha.
3. Chips de filtros activos en una segunda línea solo cuando existan.
4. Encabezado de tabla.
5. Filas.
6. Pie con rango mostrado y paginación.

### 7.2 Tabla

- Encabezado en superficie gris clara, texto de 12 px y peso 700.
- Densidad única administrativa: filas de 56 px como base, permitiendo crecimiento natural cuando el contenido requiera una segunda línea.
- Separadores horizontales suaves; evitar una cuadrícula completa.
- Hover de fila neutral.
- Selección de fila claramente distinguible sin saturar con marca.
- Texto principal en azul oscuro; metadatos en gris.
- Fechas y números con cifras tabulares.
- Columnas de estado y acciones no deben reordenarse.
- Acción por fila mediante menú de tres puntos con etiqueta accesible.

### 7.3 Filtros

- El botón Filtros muestra un contador cuando hay filtros activos.
- Panel de 22rem máximo, encabezado fijo, cuerpo desplazable y acciones fijas abajo.
- “Limpiar” es una acción textual; “Aplicar filtros” es la confirmación primaria.
- Chips removibles deben mostrar el valor aplicado, no solo el nombre del campo.
- Cerrar, cancelar o presionar Escape no aplica cambios parciales.

### 7.4 Búsqueda y paginación

- Buscar incluye icono, placeholder específico y botón para limpiar cuando exista texto.
- Los buscadores de tablas usan `type="search"`, `autocomplete="off"`, no llevan `name` si no se envían con un formulario y desactivan corrección ortográfica y capitalización automática.
- El foco del buscador usa el mismo borde `--ui-focus` y anillo `--ui-focus-ring` de campos, selects y controles de modal; no usar el azul predeterminado del navegador ni el color institucional.
- Usar debounce y mostrar actividad sin bloquear la tabla.
- En actualizaciones posteriores a la carga inicial —ordenar, filtrar, buscar, paginar o cambiar la cantidad de filas— conservar los datos visibles, atenuarlos como máximo a 80 % y deshabilitar temporalmente las acciones de fila. Retrasar cualquier indicador unos 150 ms para evitar parpadeos en respuestas rápidas.
- Para ordenar, filtrar, paginar o cambiar la cantidad de filas, mostrar una línea de progreso neutral en la parte superior de la tabla. Durante una búsqueda, sustituir esa línea por un único spinner neutral de 14–16 px dentro del campo; no mostrar ambos indicadores a la vez.
- “Mostrar 10” conserva una sola altura y estilo con el resto de la toolbar. Usar un icono neutral de lista o filas para comunicar cantidad visible; no añadir otra flecha de despliegue ni animar el icono. En el menú, señalar la opción seleccionada solamente mediante fondo suave y peso de texto, sin palomita ni otro icono redundante; conservar `aria-selected`.
- Pie: “Mostrando 1–10 de 40” a la izquierda y paginación a la derecha.
- Anterior/Siguiente deshabilitados deben seguir siendo legibles.

### 7.5 Estados obligatorios

- Carga inicial: skeleton de filas que conserva encabezado, columnas y altura aproximada; no usar una tabla vacía ni un spinner aislado.
- Sin resultados: estado neutral dentro de la tabla, icono de búsqueda, mensaje breve y acción “Limpiar búsqueda y filtros”.
- Los estados vacíos o sin resultados no reaccionan al hover: el contenedor mantiene la superficie blanca de la tabla porque no es una fila interactiva.
- Sin datos: estado neutral dentro del contenedor, icono relacionado con el módulo, título, una oración y la acción principal pertinente.
- Error: franja inline rojo suave, icono de advertencia, explicación directa y botón secundario “Reintentar”; no usar modal para errores de carga.
- Selección múltiple: barra contextual con cantidad y acciones disponibles.

## 8. Paneles, menús y confirmaciones

- Menús y dropdowns deben cerrar con Escape y clic exterior.
- El foco regresa al control que abrió el panel.
- Usar panel inline o popover para filtros; modal solo cuando la decisión bloquea el flujo.
- Confirmar acciones destructivas y navegación con cambios sin guardar.
- No usar `window.alert()` para errores de negocio.

### 8.1 Modal de confirmación

- Diseñar y revisar siempre al 100 % de zoom del navegador.
- Tamaños oficiales: pequeño 400 px, normal 440 px y amplio 640 px; una confirmación breve de eliminación usa el tamaño pequeño.
- Usar una sola superficie cohesiva. No segmentar encabezado, mensaje y acciones con franjas, fondos o divisores salvo que el contenido realmente lo requiera.
- Padding horizontal de 20 px, padding vertical de 16-18 px y radio de 7 px, igual que los controles del piloto.
- Título en Open Sans, 15 px y peso 700.
- Descripción en Open Sans, 13 px, peso 400 e interlineado 1.45.
- En confirmaciones destructivas, usar un icono semántico pequeño junto al título. Debe ir sin fondo, círculo ni contenedor decorativo y compartir la alineación del bloque de texto.
- Resaltar el nombre o identificador del elemento afectado con peso 600 y color de texto principal. No usar rojo, píldoras ni recuadros para este énfasis.
- Para eliminar una entidad, separar el contenido en dos niveles: pregunta directa con el nombre destacado y, debajo, una explicación neutral de que la acción es permanente.
- Todas las eliminaciones y descartes irreversibles deben invocar el componente compartido mediante `window.confirmDeleteDialog()`. No crear markup específico ni usar `window.confirm()` en una vista.
- La vista proporciona únicamente `title`, `subject` o `question`, `description` y, si hace falta, la etiqueta de confirmación. El componente fija icono, variante roja, botones, accesibilidad y movimiento.
- En eliminaciones múltiples, destacar como sujeto la cantidad y el tipo de elementos afectados, por ejemplo “3 usuarios”.
- Advertencias y confirmaciones no destructivas usan el mismo componente y jerarquía de pregunta más consecuencia; cambia únicamente su icono, color semántico y verbo de confirmación.
- Botones de 34 px y peso 600; en móvil aumentan a 40 px y ocupan todo el ancho.
- Cancelar usa variante neutral; una eliminación usa rojo semántico, nunca color institucional.
- Overlay azul grisáceo al 34 %, sin desenfoque.
- Usar el mismo borde `#d8dee8`, radio de 7 px y sombra contenida de los menús y popovers del piloto.
- Separar las acciones con un único divisor neutral `#e5e7eb`, sin crear un pie de color distinto.
- Al abrir, enfocar la opción segura; contener Tab dentro del modal, cerrar con Escape y devolver el foco al control de origen.
- En confirmaciones simples no mostrar una X redundante: Cancelar, Escape y clic en el overlay cubren la salida.
- Entrada de 150 ms y salida de 120 ms mediante opacidad y un desplazamiento vertical máximo de 4 px. Sin rebote, zoom ni movimientos decorativos.
- Con `prefers-reduced-motion`, eliminar el desplazamiento y reducir la transición al mínimo técnico.

### 8.2 Modal de tarea breve

- Usarlo para una operación contextual de pocos campos que permita continuar en la vista de origen, como cambiar una contraseña.
- No usarlo para editar una entidad completa, formularios largos ni tareas con varias secciones; esos flujos conservan una página propia.
- Tamaño normal de 440-480 px, campos en una columna y acciones separadas por un único divisor neutral.
- Mostrar al inicio la entidad afectada para evitar que la persona modifique el registro equivocado.
- Incluir estados de validación, envío, error y éxito dentro del flujo. No depender de alertas del navegador.
- Si existen cambios escritos, cerrar, usar Escape o pulsar el overlay debe pedir confirmación antes de descartarlos.
- La ruta de página debe conservarse como respaldo cuando JavaScript no esté disponible o se acceda mediante URL directa.

### 8.3 Notificaciones toast

- Usar exclusivamente el componente global `AppToast`; no crear variantes locales por vista ni usar `window.alert()` para mensajes transitorios.
- La referencia de interacción es Sonner, adaptada a Blade y JavaScript sin depender de React: superficie compacta, pila con profundidad, expansión al interactuar y salida en la dirección de la posición.
- Posición oficial: inferior derecha en escritorio e inferior centrada en pantallas menores de 640 px. Mantener separación de 20 px en escritorio y respetar el área segura del dispositivo.
- Mostrar como máximo tres notificaciones en la pila compacta y conservar hasta cinco activas. Al pasar el cursor o llevar el foco a la pila, expandirlas con 10 px de separación.
- Tipos oficiales: neutral, éxito, información, advertencia, error y carga. Los tipos semánticos usan fondo, borde, texto e icono suaves del sistema; el color institucional no comunica estados.
- Cada toast admite título breve y descripción opcional. Evitar signos de exclamación, frases genéricas y detalles técnicos que no ayuden a resolver la situación.
- Redactar el título como una oración breve y directa: qué ocurrió o qué requiere atención. Usar la descripción para explicar la consecuencia o el siguiente paso, no para repetir el título.
- No mostrar excepciones, nombres de clases, rutas, identificadores internos ni respuestas crudas del servidor. El detalle técnico pertenece al registro de la aplicación.
- Preferir “se guardó correctamente” frente a “guardado exitosamente” y conjugar singular y plural de forma natural; no usar fórmulas como `archivo(s)` o `registro(s)`.
- Para fallos recuperables, indicar una acción concreta: “Revisa los datos e inténtalo nuevamente”. Los errores de campos específicos permanecen junto al campo y el toast solo resume el resultado.
- Duración orientativa: 4 segundos para éxito e información, 5 segundos para advertencia y 6 segundos para error. Los estados de carga permanecen hasta actualizarse o cerrarse.
- Incluir botón de cierre. En escritorio puede aparecer al interactuar con la pila; en dispositivos táctiles permanece visible.
- Pausar el temporizador mientras la pila tiene hover o foco y cuando la pestaña no está visible.
- Las notificaciones son transitorias: descartarlas al abandonar la página y no restaurarlas desde el historial o la caché de navegación (`bfcache`). Al regresar sin guardar desde crear o editar no debe reaparecer un éxito anterior.
- Permitir deslizar horizontalmente para descartar. Respetar `prefers-reduced-motion` y mantener un cierre accesible por teclado.
- Entrada oficial: desplazamiento vertical de 18 px, escala de 0.98 a 1 y opacidad, con 260 ms y desaceleración suave. Salida: 220 ms hacia el borde correspondiente, con leve reducción de escala. Nunca desactivar la salida salvo durante el arrastre directo.
- Usar acciones solamente para una consecuencia inmediata y reversible, como “Deshacer” o “Reintentar”. No colocar dos botones ni usar el toast como sustituto de un modal de confirmación.
- Para operaciones asíncronas, actualizar el mismo toast por ID de carga a éxito o error; no encadenar tres avisos independientes.
- La API heredada `showToast(mensaje, tipo, duración)` se conserva para compatibilidad. El código nuevo debe preferir `AppToast.success()`, `info()`, `warning()`, `error()`, `loading()`, `promise()`, `update()` y `dismiss()`.

## 9. Responsive

Puntos de referencia:

- Menos de 640 px: botones principales de ancho completo.
- Menos de 760 px: formularios en una columna y ayudas apiladas.
- Menos de 1024 px: toolbar en varias filas y columnas no esenciales ocultables.
- Escritorio amplio: tabla fluida; formulario limitado para evitar líneas excesivamente largas.

Reglas:

- No depender de hover para descubrir acciones.
- No reducir texto por debajo de 12 px en controles importantes.
- Priorizar columnas; si no caben, permitir desplazamiento horizontal explícito o una vista de detalle, nunca cortar datos silenciosamente.

## 10. Accesibilidad y comportamiento

- Contraste mínimo WCAG AA.
- Foco visible en todos los elementos interactivos.
- Orden de tabulación igual al orden visual.
- Labels asociados mediante `for`/`id`.
- Iconos decorativos con `aria-hidden="true"`.
- Botones de icono con `aria-label`.
- Estados dinámicos con `aria-live` cuando corresponda.
- Errores asociados con `aria-describedby`.
- Respetar `prefers-reduced-motion`.
- Transiciones de 120–200 ms; evitar movimientos grandes.

## 11. Arquitectura técnica objetivo

No continuar acumulando reglas por pantalla al final de `app.css`. La estructura objetivo es:

```text
resources/css/design-system/
  tokens.css
  base.css
  buttons.css
  page-header.css
  forms.css
  tables.css
  overlays.css
  states.css
```

Componentes Blade propuestos:

```text
resources/views/components/ui/
  page-header.blade.php
  button.blade.php
  back-link.blade.php
  form/field.blade.php
  form/select.blade.php
  form/section.blade.php
  table/shell.blade.php
  badge.blade.php
  empty-state.blade.php
```

API mínima recomendada:

- `x-ui.button variant="primary|secondary|danger|text|icon" size="compact|default"`
- `x-ui.page-header title="..." description="..." backHref="..."`
- `x-ui.form.field label="..." required error="..." help="..."`
- `x-ui.form.select searchable="true|false"`
- `x-ui.badge tone="success|warning|danger|neutral|info|category"`

Reglas técnicas:

- Los componentes consumen tokens; no colores hexadecimales en vistas.
- No seleccionar acciones por posición, por ejemplo `button:last-child`; usar variantes explícitas.
- Evitar `!important` en componentes nuevos.
- No incluir `<style>` o dependencias externas dentro de una vista de módulo.
- Centralizar iconografía y Tom Select.
- Documentar cualquier variante nueva antes de utilizarla en una segunda pantalla.
- Aplicar el tema institucional mediante variables bajo `data-ui-theme` en el elemento `<html>`; las vistas y componentes no deben conocer el nombre ni los colores de una administración.

## 12. Plan de adopción

### Fase 0 — Validación visual

- Revisar las dos pantallas piloto en escritorio, tableta y móvil.
- Aprobar tipografía, paleta neutral, uso del vino, densidad y jerarquía de botones.
- Congelar este documento como versión 1.0.

### Fase 1 — Fundamentos

- Crear archivos de tokens y componentes base.
- Mover fuentes, colores, espaciado, radios, foco y estados semánticos.
- Mantener el aspecto de las pantallas piloto durante la extracción.

### Fase 2 — Componentes piloto

- Convertir encabezados, botones, secciones de formulario, campos, selects y tabla de usuarios en componentes compartidos.
- Eliminar estilos inline y reglas duplicadas solo después de comprobar equivalencia visual.
- Agregar estados de carga, vacío y error.

### Fase 3 — Plantillas oficiales

- Publicar una plantilla de listado basada en Gestión de usuarios.
- Publicar una plantilla de alta/edición basada en Registro de usuario.
- Incluir ejemplos de código y una página interna de componentes.

### Fase 4 — Migración por módulos

Orden recomendado:

1. Módulos con tablas y formularios similares.
2. Reportes y estadísticas.
3. Perfil, notificaciones y pantallas especiales.
4. Estados vacíos, errores, 404 y flujos menos frecuentes.

Migrar una pantalla completa a la vez. No mezclar diseño anterior y nuevo dentro de una misma pantalla.

### Fase 5 — Control de calidad

- Comparación visual a 100 %, 125 % y 200 % de zoom.
- Navegación completa con teclado.
- Contraste y lectores de pantalla.
- Chrome, Edge y Firefox.
- Anchos de 375, 768, 1024, 1366 y 1920 px.
- Pruebas con textos largos, cero datos, muchos datos y errores del servidor.

## 13. Lista de aceptación por pantalla

Una pantalla se considera migrada cuando:

- Usa el encabezado oficial.
- Usa exclusivamente tokens y componentes aprobados.
- Tiene una sola acción primaria por contexto.
- Los iconos cumplen la política y tienen etiquetas accesibles.
- Formularios, selects y mensajes usan los estados oficiales.
- Tablas incluyen carga, vacío, error y paginación.
- Funciona con teclado y foco visible.
- Responde correctamente en móvil y escritorio.
- No contiene colores institucionales hardcodeados.
- No agrega estilos inline ni nuevas excepciones globales.
- Pasó revisión funcional y visual.

## 14. Decisiones tomadas con los pilotos

- El color vino queda reservado al shell institucional y acciones de confirmación.
- El foco general es gris azulado neutral.
- Los obligatorios usan rojo semántico.
- Título y descripción comparten escala entre listados y formularios.
- El regreso se presenta encima del título y no se duplica al final.
- Todos los selects de un formulario comparten Tom Select; la búsqueda depende del tamaño de la lista.
- Guardar es primario; Limpiar es secundario; Generar contraseña es contextual.
- La asistencia de contraseña se distribuye junto a los dos campos correspondientes.
- Font Awesome es la familia oficial de iconos y la migración se hará por pantalla.
- Los formularios tienen un ancho máximo de 1440 px.
- Las tablas usan una sola densidad administrativa de 56 px por fila.
- Carga, vacío, sin resultados y error se presentan dentro del componente afectado.
- El tema institucional se selecciona con `data-ui-theme` y variables CSS.

## 15. Decisiones cerradas para versión 1.0

- Iconos: Font Awesome.
- Formularios: máximo 1440 px.
- Tablas: densidad administrativa única, 56 px como altura base de fila.
- Estados: skeleton para carga y mensajes inline para vacío, sin resultados y error.
- Tema institucional: selector `data-ui-theme` y variables CSS centralizadas.
