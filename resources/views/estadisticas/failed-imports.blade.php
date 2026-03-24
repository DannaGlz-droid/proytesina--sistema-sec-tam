@extends('layouts.principal')
@section('title', 'Registros Fallidos de Importación')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('statistic.import-history-view') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 text-sm font-semibold">
                <span>←</span> Volver al Historial
            </a>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Registros Fallidos - Importación #{{ $importId }}</h1>
            <p class="text-sm lg:text-base text-[#404041] font-lora">
                Revise y corrija los registros que fallaron durante la importación para reintentar su guardado.
            </p>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="space-y-4">
            <div class="bg-gray-100 rounded-lg p-8 text-center">
                <p class="text-gray-600">Cargando registros fallidos...</p>
            </div>
        </div>

        <!-- Records Container -->
        <div id="records-container" class="hidden space-y-6">
            <!-- Empty State -->
            <div id="empty-state" class="hidden bg-green-50 border border-green-200 rounded-lg p-8 text-center">
                <p class="text-green-700 font-semibold">✓ No hay registros fallidos pendientes para esta importación.</p>
            </div>

            <!-- Records List -->
            <div id="records-list" class="space-y-6"></div>

            <!-- Pagination -->
            <div id="pagination" class="flex justify-center gap-2 mt-8"></div>
        </div>
    </div>

@endsection

<!-- Template for failed record card -->
<template id="record-template">
    <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 shadow-md record-card" data-record-id="">
        
        <!-- Error Alert -->
        <div class="bg-red-50 border-l-4 border-red-600 p-4 mb-6 rounded-r-lg">
            <p class="text-red-700 font-semibold text-sm">Error en el registro:</p>
            <p class="text-red-600 text-sm mt-1 error-message"></p>
        </div>

        <!-- Validation Errors -->
        <div class="validation-errors hidden bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="text-red-700 font-semibold text-sm mb-2">Errores de validación:</p>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1"></ul>
        </div>

        <!-- VIEW MODE -->
        <div class="view-mode">
            <!-- Sección: Información del fallecido -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <ion-icon name="person-outline" class="text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg font-lora font-bold text-[#404041]">Información del fallecido</h2>
                    <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4">
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Folio</p>
                        <p class="text-sm font-medium text-gray-800 original-folio mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Nombre</p>
                        <p class="text-sm font-medium text-gray-800 original-nombre mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Apellido Paterno</p>
                        <p class="text-sm font-medium text-gray-800 original-primerapellido mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Apellido Materno</p>
                        <p class="text-sm font-medium text-gray-800 original-segundoapellido mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Sexo</p>
                        <p class="text-sm font-medium text-gray-800 original-sexod mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Fecha Defunción</p>
                        <p class="text-sm font-medium text-gray-800 original-fechadefuncion mt-1">-</p>
                    </div>
                </div>
            </div>

            <div class="h-px bg-gray-300 my-4"></div>

            <!-- Sección: Ubicación -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <ion-icon name="location-outline" class="text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg font-lora font-bold text-[#404041]">Ubicación</h2>
                    <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Municipio Residencia</p>
                        <p class="text-sm font-medium text-gray-800 original-municipioresidenciad mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Municipio Defunción</p>
                        <p class="text-sm font-medium text-gray-800 original-municipiodefunciond mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Lugar Específico</p>
                        <p class="text-sm font-medium text-gray-800 original-sitiodefunciond mt-1">-</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <p class="text-xs font-medium text-gray-600 uppercase font-lora">Causa Muerte</p>
                        <p class="text-sm font-medium text-gray-800 original-causa mt-1">-</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons - View Mode -->
            <div class="flex flex-wrap gap-2 pt-6 border-t border-gray-300">
                <button type="button" class="btn-toggle-edit bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-semibold transition-all duration-200">
                    ✏️ Editar
                </button>
                <button type="button" class="btn-discard bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm rounded-lg font-semibold transition-all duration-200">
                    🗑️ Descartar
                </button>
            </div>
        </div>

        <!-- EDIT MODE -->
        <div class="edit-mode hidden">
            <form class="correction-form space-y-0">
                <!-- Sección: Información del fallecido -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <ion-icon name="person-outline" class="text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg font-lora font-bold text-[#404041]">Información del fallecido</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Folio <span class="text-red-600">*</span></label>
                            <input type="text" name="folio" maxlength="9" pattern="[0-9]{9}" inputmode="numeric"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="9 dígitos" required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) <span class="text-red-600">*</span></label>
                            <input type="text" name="nombre"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Nombre completo" required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido Paterno <span class="text-red-600">*</span></label>
                            <input type="text" name="primerapellido"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Apellido paterno" required>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido Materno</label>
                            <input type="text" name="segundoapellido"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Apellido materno">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo <span class="text-red-600">*</span></label>
                            <select name="sexod"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" required>
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad</label>
                            <input type="number" name="edad" min="0" max="150"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Edad en años">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha Defunción <span class="text-red-600">*</span></label>
                            <input type="date" name="fechadefuncion"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" required>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-gray-300 my-4"></div>

                <!-- Sección: Ubicación -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <ion-icon name="location-outline" class="text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg font-lora font-bold text-[#404041]">Ubicación</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio Residencia</label>
                            <input type="text" name="municipioresidenciad"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Municipio de residencia">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio Defunción</label>
                            <input type="text" name="municipiodefunciond"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Municipio de defunción">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar Específico</label>
                            <input type="text" name="sitiodefunciond"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Lugar de defunción">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa Muerte</label>
                            <input type="text" name="sheet"
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: Accidente Vial">
                        </div>
                    </div>
                </div>
            </form>

            <!-- Action Buttons - Edit Mode -->
            <div class="flex flex-wrap gap-2 pt-6 border-t border-gray-300">
                <button type="button" class="btn-save-correction bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-semibold transition-all duration-200">
                    💾 Guardar
                </button>
                <button type="button" class="btn-retry bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm rounded-lg font-semibold transition-all duration-200">
                    ✓ Importar
                </button>
                <button type="button" class="btn-cancel-edit bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 text-sm rounded-lg font-semibold transition-all duration-200">
                    ✕ Cancelar
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<style>
    .view-mode { display: block; }
    .edit-mode { display: none; }
    
    .record-card.in-edit .view-mode { display: none; }
    .record-card.in-edit .edit-mode { display: block; }
    
    .record-card {
        transition: all 0.3s ease;
    }
    
    .record-card.in-edit {
        border-color: #3b82f6 !important;
        background-color: #f0f9ff !important;
    }
</style>

<script>
// Helper function to parse dates in various formats and convert to YYYY-MM-DD for HTML date input
function parseDateForInput(dateStr) {
    if (!dateStr) return '';
    
    let date = null;
    
    // Try DD/MM/YYYY format (common in imports)
    if (dateStr.includes('/')) {
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            // Try MM/DD/YYYY first
            if (parseInt(parts[0]) <= 12) {
                date = new Date(parseInt(parts[2]), parseInt(parts[0]) - 1, parseInt(parts[1]));
            } else {
                // Must be DD/MM/YYYY
                date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
            }
        }
    } else if (dateStr.includes('-')) {
        // Try YYYY-MM-DD or DD-MM-YYYY
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            if (parseInt(parts[0]) > 31) {
                // YYYY-MM-DD
                date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            } else {
                // DD-MM-YYYY
                date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
            }
        }
    }
    
    // If parsing failed, try generic Date constructor
    if (!date || isNaN(date.getTime())) {
        date = new Date(dateStr);
    }
    
    // If still invalid, return empty
    if (!date || isNaN(date.getTime())) {
        return '';
    }
    
    // Format as YYYY-MM-DD
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const importId = {{ $importId }};
    let currentPage = 1;

    function loadFailedRecords(page = 1) {
        document.getElementById('loading-state').classList.remove('hidden');
        document.getElementById('records-container').classList.add('hidden');

        fetch(`/api/estadisticas/importaciones/${importId}/registros-fallidos?page=${page}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-state').classList.add('hidden');
                document.getElementById('records-container').classList.remove('hidden');

                if (data.ok && data.data.data.length > 0) {
                    renderRecords(data.data.data);
                    renderPagination(data.data);
                    currentPage = page;
                } else {
                    document.getElementById('empty-state').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loading-state').innerHTML = '<p class="text-red-600">Error cargando registros</p>';
            });
    }

    function renderRecords(records) {
        const container = document.getElementById('records-list');
        container.innerHTML = '';

        records.forEach(record => {
            const template = document.getElementById('record-template');
            const clone = template.content.cloneNode(true);
            
            const card = clone.querySelector('.record-card');
            card.dataset.recordId = record.id;

            clone.querySelector('.error-message').textContent = record.error_message;

            const originalData = record.original_row_data || {};
            clone.querySelector('.original-folio').textContent = originalData.folio || originalData.folio_gob || '-';
            clone.querySelector('.original-nombre').textContent = originalData.nombre || '-';
            clone.querySelector('.original-primerapellido').textContent = originalData.primerapellido || originalData.primerapellid || '-';
            clone.querySelector('.original-segundoapellido').textContent = originalData.segundoapellido || originalData.segundoapellid || '-';
            
            // Handle sexo: convert HOMBRE/MUJER to M/F for display and form
            let sexoValue = originalData.sexod || '-';
            if (sexoValue === 'HOMBRE') sexoValue = 'M';
            else if (sexoValue === 'MUJER') sexoValue = 'F';
            clone.querySelector('.original-sexod').textContent = sexoValue;
            
            clone.querySelector('.original-fechadefuncion').textContent = originalData.fechadefuncion || '-';
            clone.querySelector('.original-municipioresidenciad').textContent = originalData.municipioresidenciad || '-';
            clone.querySelector('.original-municipiodefunciond').textContent = originalData.municipiodefunciond || '-';
            clone.querySelector('.original-sitiodefunciond').textContent = originalData.sitiodefunciond || '-';
            // Causa: mostrar el nombre de la hoja (sheet) que es lo que se usa en el import normal
            let causaValue = originalData.sheet || '-';
            clone.querySelector('.original-causa').textContent = causaValue;

            const formData = record.corrected_data || originalData;
            const form = clone.querySelector('.correction-form');
            form.querySelectorAll('[name]').forEach(field => {
                const fieldName = field.getAttribute('name');
                let value = formData[fieldName];
                
                // Special handling for sexo field - convert HOMBRE/MUJER to M/F
                if (fieldName === 'sexod' && value) {
                    if (value === 'HOMBRE') value = 'M';
                    else if (value === 'MUJER') value = 'F';
                }
                
                // Special handling for fechadefuncion - convert to YYYY-MM-DD format for date input
                if (fieldName === 'fechadefuncion' && value) {
                    value = parseDateForInput(value);
                }
                
                // Special handling for sheet field - use sheet name from data
                if (fieldName === 'sheet' && !value) {
                    value = formData.sheet || '';
                }
                
                if (value) {
                    field.value = value;
                }
            });

            const originalFormData = {};
            form.querySelectorAll('[name]').forEach(field => {
                originalFormData[field.getAttribute('name')] = field.value;
            });
            card.dataset.originalFormData = JSON.stringify(originalFormData);

            attachRecordListeners(clone, record.id);
            container.appendChild(clone);
        });
    }

    function attachRecordListeners(cardElement, recordId) {
        const card = cardElement.querySelector('.record-card');
        const form = cardElement.querySelector('.correction-form');
        
        const btnToggleEdit = cardElement.querySelector('.btn-toggle-edit');
        const btnCancelEdit = cardElement.querySelector('.btn-cancel-edit');
        const btnSaveCorrection = cardElement.querySelector('.btn-save-correction');
        const btnRetry = cardElement.querySelector('.btn-retry');
        const btnDiscard = cardElement.querySelector('.btn-discard');

        // Debug: verify buttons exist
        if (!btnSaveCorrection || !btnRetry) {
            console.warn('Warning: Save or Retry button not found for record', recordId);
        }

        if (btnToggleEdit) {
            btnToggleEdit.addEventListener('click', (e) => {
                e.preventDefault();
                card.classList.add('in-edit');
            });
        }

        if (btnCancelEdit) {
            btnCancelEdit.addEventListener('click', (e) => {
                e.preventDefault();
                card.classList.remove('in-edit');
                const originalFormData = JSON.parse(card.dataset.originalFormData || '{}');
                form.querySelectorAll('[name]').forEach(field => {
                    const fieldName = field.getAttribute('name');
                    field.value = originalFormData[fieldName] || '';
                });
            });
        }

        if (btnSaveCorrection) {
            btnSaveCorrection.addEventListener('click', (e) => {
                e.preventDefault();
                saveCorrection(recordId, form, card, false);
            });
        }

        if (btnRetry) {
            btnRetry.addEventListener('click', (e) => {
                e.preventDefault();
                saveCorrection(recordId, form, card, true);
            });
        }

        if (btnDiscard) {
            btnDiscard.addEventListener('click', (e) => {
                e.preventDefault();
                if (confirm('¿Descartar este registro? No podrá ser recuperado.')) {
                    discardRecord(recordId, card);
                }
            });
        }
    }

    function saveCorrection(recordId, form, card, shouldRetry = false) {
        const correctedData = {};
        form.querySelectorAll('[name]').forEach(field => {
            const fieldName = field.getAttribute('name');
            const value = field.value ? field.value.trim() : '';
            if (value !== '') {
                correctedData[fieldName] = value;
            }
        });

        const endpoint = shouldRetry 
            ? `/api/estadisticas/registros-fallidos/${recordId}/reintentar`
            : `/api/estadisticas/registros-fallidos/${recordId}/corregir`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ corrected_data: correctedData }),
        })
        .then(response => response.json())
        .then(data => {
            const validationErrors = card.querySelector('.validation-errors');
            
            if (data.ok) {
                if (shouldRetry) {
                    alert('✓ Registro importado exitosamente');
                    card.classList.add('opacity-50');
                    setTimeout(() => location.reload(), 500);
                } else {
                    card.classList.remove('in-edit');
                    validationErrors.classList.add('hidden');
                    form.querySelectorAll('[name]').forEach(field => {
                        const fieldName = field.getAttribute('name');
                        const displayElement = card.querySelector(`.original-${fieldName}`);
                        if (displayElement) {
                            displayElement.textContent = field.value || '-';
                        }
                    });
                    alert('✓ Correcciones guardadas');
                }
            } else {
                if (data.errors && Array.isArray(data.errors)) {
                    const errorsList = validationErrors.querySelector('ul');
                    errorsList.innerHTML = '';
                    data.errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorsList.appendChild(li);
                    });
                    validationErrors.classList.remove('hidden');
                } else {
                    alert('❌ Error: ' + (data.message || 'Error desconocido'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error: ' + error.message);
        });
    }

    function discardRecord(recordId, card) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`/api/estadisticas/registros-fallidos/${recordId}/descartar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                card.remove();
                if (document.querySelectorAll('.record-card').length === 0) {
                    loadFailedRecords(currentPage);
                }
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function renderPagination(data) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        if (data.last_page <= 1) return;

        if (data.prev_page_url) {
            const btn = document.createElement('button');
            btn.className = 'px-3 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm';
            btn.textContent = '← Anterior';
            btn.addEventListener('click', () => loadFailedRecords(data.current_page - 1));
            pagination.appendChild(btn);
        }

        for (let i = 1; i <= data.last_page; i++) {
            const btn = document.createElement('button');
            btn.className = i === data.current_page 
                ? 'px-3 py-2 bg-[#611132] text-white rounded text-sm' 
                : 'px-3 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm';
            btn.textContent = i;
            btn.addEventListener('click', () => loadFailedRecords(i));
            pagination.appendChild(btn);
        }

        if (data.next_page_url) {
            const btn = document.createElement('button');
            btn.className = 'px-3 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm';
            btn.textContent = 'Siguiente →';
            btn.addEventListener('click', () => loadFailedRecords(data.current_page + 1));
            pagination.appendChild(btn);
        }
    }

    loadFailedRecords(1);
});
</script>
@endpush
