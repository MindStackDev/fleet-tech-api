# FleetTech API - Directivas de Arquitectura MVC Estándar

## 1. 🛡️ Protocolo de Seguridad Git (ESTRICTO)
- **PROHIBIDO EJECUTAR `git push` SIN AUTORIZACIÓN EXPRESA DEL USUARIO EN EL CHAT.**
- Todo commit, migración, prueba y cambio se mantiene estrictamente en local.
- Solo se ejecuta `git push origin <branch>` cuando el usuario lo solicite explícitamente.

---

## 2. 🏛️ Arquitectura Clásica de Laravel (MVC Limpio)
1. **Modelos en `app/Models/`:**
   - Todos los modelos Eloquent residen en `app/Models/` (`Vehicle.php`, `Inspection.php`, `Defect.php`, `User.php`, etc.).
2. **Controladores en `app/Http/Controllers/Api/V1/`:**
   - Controladores delgados, delegan lógica a FormRequests, Services y Resources.
3. **Servicios de Negocio en `app/Services/`:**
   - Operaciones complejas (relevo de turno con doble firma, deslinde de herramientas, bloqueo automático de unidad) residen en `app/Services/` (`InspectionService.php`, `DefectService.php`).
4. **Validaciones en `app/Http/Requests/Api/V1/`:**
   - FormRequests dedicados para cada endpoint con tipado y validación estricta.
5. **Transformación JSON en `app/Http/Resources/Api/V1/`:**
   - ApiResources con envelope estándar `{ success, message, data, meta }`.
6. **Enums en `app/Enums/`:**
   - Estados y tipos como BackedEnums de PHP 8.3 (`UserRole`, `ShiftType`, `VehicleStatus`, `AnswerStatus`, `DefectSeverity`, `DefectStatus`).

---

## 3. 🚛 Reglas de Negocio de Patio y Taller
- **Relevo de Turno:** Registro de odómetro, combustible, bahía, GPS y doble firma (operador saliente + operador entrante).
- **Custodia de Herramientas:** Ítem con falla o faltante genera automáticamente un `Defect` para deslinde de responsabilidades.
- **Bloqueo Preventivo:** Todo defecto `critical` cambia el vehículo a `in_maintenance` hasta que el técnico registre la solución con evidencia fotográfica.
