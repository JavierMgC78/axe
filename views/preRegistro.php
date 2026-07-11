
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preregistro - Centro Educativo América</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .step-content {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos personalizados para inputs */
        .form-input {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            outline: none;
            transition: all 0.2s;
        }

        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-3xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <!-- Header -->
        <div class="bg-blue-800 p-6 text-white text-center">
            <h1 class="text-2xl font-bold">Centro Educativo América</h1>
            <p class="text-blue-200 mt-1">Solicitud de Inscripción 2026-2027</p>
        </div>

        <!-- Progress Bar -->
        <div class="px-6 pt-6">
            <div class="flex items-center justify-between mb-4 relative">
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 z-0 rounded-full"></div>
                <div id="progress-bar" class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-blue-600 z-0 rounded-full transition-all duration-300 w-0"></div>
                
                <div class="step-indicator relative z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm transition-colors" id="indicator-1">1</div>
                    <span class="text-xs mt-2 font-medium text-gray-500 hidden sm:block">Alumno</span>
                </div>
                <div class="step-indicator relative z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold text-sm transition-colors" id="indicator-2">2</div>
                    <span class="text-xs mt-2 font-medium text-gray-500 hidden sm:block">Tutor</span>
                </div>
                <div class="step-indicator relative z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold text-sm transition-colors" id="indicator-3">3</div>
                    <span class="text-xs mt-2 font-medium text-gray-500 hidden sm:block">Facturación</span>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form id="preregistroForm" class="p-6" onsubmit="submitForm(event)">
            
            <!-- STEP 1: Datos del Alumno -->
            <div id="step-1" class="step-content active">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Datos del Alumno</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Nivel Educativo *</label>
                        <select id="nivel_educativo" class="form-input" required onchange="updateGrados()">
                            <option value="">Seleccione un nivel...</option>
                            <option value="Preescolar">Preescolar</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Secundaria">Secundaria</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Grado a ingresar *</label>
                        <select id="grado" class="form-input" required disabled>
                            <option value="">Seleccione primero el nivel...</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="form-label">Nombre(s) *</label>
                        <input type="text" id="nombre" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Apellido Paterno *</label>
                        <input type="text" id="apellido_paterno" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Apellido Materno</label>
                        <input type="text" id="apellido_materno" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">CURP *</label>
                        <input type="text" id="curp" class="form-input uppercase" maxlength="18" pattern="^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]\d$" required placeholder="18 caracteres">
                    </div>
                    <div>
                        <label class="form-label">Fecha de Nacimiento *</label>
                        <input type="date" id="fecha_nacimiento" class="form-input" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Dirección (Calle, Cruzamientos, Colonia) *</label>
                    <input type="text" id="direccion" class="form-input" required placeholder="Ej. Calle 60 #123 x 45 y 47, Col. Centro">
                </div>

                <div class="mb-4">
                    <label class="form-label">Datos Médicos (Alergias, Tratamientos)</label>
                    <textarea id="datos_medicos" class="form-input" rows="2" placeholder="Indique si es alérgico a algún medicamento o alimento, o si lleva algún tratamiento médico."></textarea>
                </div>
            </div>

            <!-- STEP 2: Datos del Tutor -->
            <div id="step-2" class="step-content">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Datos del Tutor Principal</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Nombre Completo *</label>
                        <input type="text" id="tutor_nombre" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Parentesco/Relación *</label>
                        <select id="tutor_relacion" class="form-input" required>
                            <option value="">Seleccione...</option>
                            <option value="Madre">Madre</option>
                            <option value="Padre">Padre</option>
                            <option value="Tutor">Tutor Legal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Teléfono Celular *</label>
                        <input type="tel" id="tutor_telefono" class="form-input" required pattern="[0-9]{10}" placeholder="10 dígitos">
                    </div>
                    <div>
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" id="tutor_correo" class="form-input" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Ocupación / Puesto</label>
                        <input type="text" id="tutor_ocupacion" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Empresa / Lugar de trabajo</label>
                        <input type="text" id="tutor_empresa" class="form-input">
                    </div>
                </div>
                
                <div class="mt-6 p-4 bg-blue-50 rounded-lg flex items-start">
                    <input type="checkbox" id="requiere_factura" class="mt-1 mr-3 h-4 w-4 text-blue-600 rounded" onchange="toggleFacturacion()">
                    <label for="requiere_factura" class="text-sm text-blue-900 font-medium cursor-pointer">
                        ¿Requerirá factura a nombre de una empresa o persona física con actividad empresarial?
                    </label>
                </div>
            </div>

            <!-- STEP 3: Datos de Facturación -->
            <div id="step-3" class="step-content">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Datos de Facturación</h2>
                <p class="text-sm text-gray-500 mb-4">Complete esta información únicamente si requiere comprobante fiscal (CFDI).</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">RFC</label>
                        <input type="text" id="fac_rfc" class="form-input uppercase" maxlength="13">
                    </div>
                    <div>
                        <label class="form-label">Código Postal (Fiscal)</label>
                        <input type="text" id="fac_cp" class="form-input" maxlength="5">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Razón Social / Nombre Completo</label>
                    <input type="text" id="fac_razon" class="form-input">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Régimen Fiscal</label>
                        <select id="fac_regimen" class="form-input">
                            <option value="">Seleccione...</option>
                            <option value="605">605 - Sueldos y Salarios</option>
                            <option value="606">606 - Arrendamiento</option>
                            <option value="612">612 - Personas Físicas con Actividades Empresariales</option>
                            <option value="601">601 - General de Ley Personas Morales</option>
                            <option value="626">626 - RESICO</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Uso de CFDI</label>
                        <select id="fac_uso" class="form-input">
                            <option value="D10">D10 - Pagos por servicios educativos (Colegiaturas)</option>
                            <option value="G03">G03 - Gastos en general</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="mt-8 flex justify-between pt-4 border-t">
                <button type="button" id="btn-prev" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors hidden" onclick="prevStep()">
                    <i class="fas fa-arrow-left mr-2"></i> Atrás
                </button>
                <div class="ml-auto">
                    <button type="button" id="btn-next" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md" onclick="nextStep()">
                        Siguiente <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" id="btn-submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-md hidden">
                        Completar Preregistro <i class="fas fa-check ml-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de Éxito (Reemplazo de alert) -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 text-center shadow-2xl transform scale-95 transition-transform" id="modalContent">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-3xl text-green-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Preregistro Exitoso!</h3>
            <p class="text-gray-600 mb-6">La solicitud ha sido procesada correctamente. En un escenario real, los datos se enviarían a tu controlador Axe.</p>
            <div class="bg-gray-100 p-4 rounded-lg text-left text-xs font-mono text-gray-700 mb-6 overflow-auto max-h-40" id="jsonOutput">
                <!-- JSON output will go here -->
            </div>
            <button onclick="closeModal()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Aceptar y Nuevo Registro
            </button>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let requiresBilling = false;
        const totalSteps = 3;

        // Lógica para actualizar los grados dependiendo del nivel educativo
        function updateGrados() {
            const nivel = document.getElementById('nivel_educativo').value;
            const gradoSelect = document.getElementById('grado');
            
            gradoSelect.innerHTML = '<option value="">Seleccione el grado...</option>';
            
            if (nivel === '') {
                gradoSelect.disabled = true;
                return;
            }
            
            gradoSelect.disabled = false;
            let maxGrados = 0;

            switch(nivel) {
                case 'Preescolar': maxGrados = 3; break;
                case 'Primaria': maxGrados = 6; break;
                case 'Secundaria': maxGrados = 3; break;
            }

            for(let i = 1; i <= maxGrados; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `${i}° Año`;
                gradoSelect.appendChild(option);
            }
        }

        // Mostrar/Ocultar paso de facturación
        function toggleFacturacion() {
            requiresBilling = document.getElementById('requiere_factura').checked;
            
            // Ajustar validaciones requeridas dinámicamente
            const facInputs = ['fac_rfc', 'fac_cp', 'fac_razon', 'fac_regimen'];
            facInputs.forEach(id => {
                const el = document.getElementById(id);
                if (requiresBilling) {
                    el.setAttribute('required', 'true');
                } else {
                    el.removeAttribute('required');
                }
            });
        }

        // Validar inputs del paso actual utilizando la API de validación nativa de HTML5
        function validateCurrentStep() {
            const currentStepEl = document.getElementById(`step-${currentStep}`);
            const inputs = currentStepEl.querySelectorAll('input, select, textarea');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.reportValidity(); // Muestra el tooltip nativo del navegador
                    isValid = false;
                }
            });

            return isValid;
        }

        function updateUI() {
            // Manejo de visibilidad de pasos
            document.querySelectorAll('.step-content').forEach((el, index) => {
                if (index + 1 === currentStep) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });

            // Lógica de botones
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');
            const btnSubmit = document.getElementById('btn-submit');

            if (currentStep === 1) {
                btnPrev.classList.add('hidden');
            } else {
                btnPrev.classList.remove('hidden');
            }

            // Si es el último paso, o si estamos en el paso 2 y NO requiere facturación
            if (currentStep === totalSteps || (currentStep === 2 && !requiresBilling)) {
                btnNext.classList.add('hidden');
                btnSubmit.classList.remove('hidden');
            } else {
                btnNext.classList.remove('hidden');
                btnSubmit.classList.add('hidden');
            }

            // Actualizar barra de progreso e indicadores
            const effectiveTotalSteps = requiresBilling ? 3 : 2;
            const progressPercentage = ((currentStep - 1) / (effectiveTotalSteps - 1)) * 100;
            document.getElementById('progress-bar').style.width = `${progressPercentage}%`;

            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`indicator-${i}`);
                if (i <= currentStep) {
                    indicator.classList.remove('bg-gray-200', 'text-gray-600');
                    indicator.classList.add('bg-blue-600', 'text-white');
                } else {
                    indicator.classList.add('bg-gray-200', 'text-gray-600');
                    indicator.classList.remove('bg-blue-600', 'text-white');
                }
            }
            
            // Si facturación no es requerida, atenuar el paso 3
            if(!requiresBilling && currentStep < 3) {
                 document.getElementById('indicator-3').parentElement.style.opacity = '0.4';
            } else {
                 document.getElementById('indicator-3').parentElement.style.opacity = '1';
            }
        }

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep === 2 && !requiresBilling) {
                    // Si estamos en el paso 2 y no requiere factura, el botón debió ser "Submit", 
                    // pero por si acaso lo interceptamos aquí.
                    return; 
                }
                currentStep++;
                updateUI();
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        }

        function submitForm(event) {
            event.preventDefault();
            
            if (!validateCurrentStep()) return;

            // Construcción del objeto JSON para enviar al backend (Axe/PHP)
            const payload = {
                solicitud: {
                    ciclo_escolar: "2026-2027",
                    nivel_educativo: document.getElementById('nivel_educativo').value,
                    grado: document.getElementById('grado').value,
                    fecha_registro: new Date().toISOString()
                },
                alumno: {
                    curp: document.getElementById('curp').value.toUpperCase(),
                    nombre: document.getElementById('nombre').value,
                    apellido_paterno: document.getElementById('apellido_paterno').value,
                    apellido_materno: document.getElementById('apellido_materno').value,
                    fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
                    direccion: document.getElementById('direccion').value,
                    datos_medicos: document.getElementById('datos_medicos').value
                },
                tutor: {
                    nombre_completo: document.getElementById('tutor_nombre').value,
                    relacion: document.getElementById('tutor_relacion').value,
                    telefono: document.getElementById('tutor_telefono').value,
                    correo: document.getElementById('tutor_correo').value,
                    datos_laborales: {
                        ocupacion: document.getElementById('tutor_ocupacion').value,
                        empresa: document.getElementById('tutor_empresa').value
                    }
                },
                facturacion: null
            };

            if (requiresBilling) {
                payload.facturacion = {
                    rfc: document.getElementById('fac_rfc').value.toUpperCase(),
                    razon_social: document.getElementById('fac_razon').value,
                    codigo_postal: document.getElementById('fac_cp').value,
                    regimen_fiscal: document.getElementById('fac_regimen').value,
                    uso_cfdi: document.getElementById('fac_uso').value
                };
            }

            // Mostrar el Modal de éxito
            document.getElementById('jsonOutput').textContent = JSON.stringify(payload, null, 2);
            const modal = document.getElementById('successModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            // Pequeño delay para la animación
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('successModal');
            modal.classList.add('hidden');
            // Reset form
            document.getElementById('preregistroForm').reset();
            currentStep = 1;
            requiresBilling = false;
            updateGrados();
            toggleFacturacion();
            updateUI();
        }

        // Inicializar UI
        updateUI();
    </script>
</body>
</html>