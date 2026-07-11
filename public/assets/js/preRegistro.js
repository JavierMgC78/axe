
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
    