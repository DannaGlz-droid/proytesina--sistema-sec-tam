<x-modal-reporte-base tipo="alcoholimetria" titulo="Reporte de Alcoholimetría" modal-id="modalAlcoholimetria">
    <section class="report-section alcohol-report-section">
        <h3 class="report-section-title"><span>Datos generales</span></h3>
        <dl class="report-field-grid report-field-grid--three report-general-grid alcohol-general-grid">
            <div class="report-field"><dt>Fecha</dt><dd class="modal-fecha-actividad">-</dd><small>Fecha de la actividad</small></div>
            <div class="report-field"><dt>Municipio</dt><dd class="modal-municipio">-</dd><small>Área de cobertura</small></div>
            <div class="report-field"><dt>Distrito</dt><dd class="modal-distrito">-</dd><small>Zona administrativa</small></div>
        </dl>
    </section>
    <section class="report-section alcohol-report-section">
        <h3 class="report-section-title"><span>Resultados del operativo</span></h3>
        <dl class="report-metrics-grid alcohol-results-grid">
            <div class="report-metric"><dd class="modal-puntos-revision">0</dd><dt>Puntos de revisión</dt></div>
            <div class="report-metric"><dd class="modal-pruebas-realizadas">0</dd><dt>Pruebas realizadas</dt></div>
            <div class="report-metric"><dd class="modal-conductores-no-aptos">0</dd><dt>Conductores no aptos</dt></div>
        </dl>
    </section>
    <section class="report-section alcohol-report-section alcohol-breakdown-section">
        <h3 class="report-section-title"><span>Conductores no aptos</span></h3>
        <div class="alcohol-breakdown-groups">
            <section class="alcohol-breakdown-group" aria-labelledby="alcohol-breakdown-gender">
                <h4 id="alcohol-breakdown-gender" class="report-subsection-title">Por género</h4>
                <dl class="alcohol-breakdown-list alcohol-breakdown-list--gender">
                    <div><dt>Mujeres</dt><dd class="modal-mujeres-no-aptas">0</dd></div>
                    <div><dt>Hombres</dt><dd class="modal-hombres-no-aptos">0</dd></div>
                </dl>
            </section>

            <section class="alcohol-breakdown-group" aria-labelledby="alcohol-breakdown-vehicle">
                <h4 id="alcohol-breakdown-vehicle" class="report-subsection-title">Por tipo de vehículo</h4>
                <dl class="alcohol-breakdown-list alcohol-breakdown-list--vehicles">
                    <div><dt>Automóviles y camionetas</dt><dd class="modal-automoviles-no-aptos">0</dd></div>
                    <div><dt>Motocicletas</dt><dd class="modal-motocicletas-no-aptas">0</dd></div>
                    <div><dt>Transporte colectivo</dt><dd class="modal-transporte-colectivo-no-apto">0</dd></div>
                    <div><dt>Transporte individual</dt><dd class="modal-transporte-individual-no-apto">0</dd></div>
                    <div><dt>Transporte de carga</dt><dd class="modal-transporte-carga-no-apto">0</dd></div>
                    <div><dt>Vehículos de emergencia</dt><dd class="modal-emergencia-no-apto">0</dd></div>
                </dl>
            </section>
        </div>
    </section>
</x-modal-reporte-base>
