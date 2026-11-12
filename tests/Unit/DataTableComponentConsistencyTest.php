<?php

namespace Tests\Unit;

use Tests\TestCase;

class DataTableComponentConsistencyTest extends TestCase
{
    public function test_user_management_uses_the_shared_data_table_structure(): void
    {
        $view = file_get_contents(resource_path('views/usuarios/gestion-de-usuarios.blade.php'));
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('<x-ui.data-table.panel', $view);
        $this->assertStringContainsString('<x-ui.data-table.toolbar>', $view);
        $this->assertStringContainsString('<x-ui.data-table.bulk-actions', $view);
        $this->assertStringContainsString('<x-ui.data-table.viewport>', $view);
        $this->assertStringContainsString('<x-ui.data-table.table id="users-table">', $view);
        $this->assertStringContainsString('class="dt-actions-cell px-3 py-2', $view);
        $this->assertStringContainsString('class="users-username-text"', $view);
        $this->assertStringContainsString('.users-username-text {', $css);
        $this->assertStringContainsString('<x-ui.data-table.footer', $view);
        $this->assertStringContainsString(
            "window.AppDataTable?.focusSearch(document.getElementById('dt-search-users'));",
            $view
        );
    }

    public function test_user_table_controls_use_the_shared_search_and_page_size_components(): void
    {
        $filters = file_get_contents(resource_path('views/components/filtros/usuarios.blade.php'));

        $this->assertStringContainsString('<x-ui.data-table.search', $filters);
        $this->assertStringContainsString('id="dt-search-users"', $filters);
        $this->assertStringContainsString('<x-ui.data-table.page-size', $filters);
        $this->assertStringContainsString('id="dt-per-page"', $filters);
        $this->assertStringContainsString('class="ui-data-table__filter-chips users-filter-chips"', $filters);
        $this->assertStringNotContainsString('usersDateRangeHelp', $filters);
    }

    public function test_data_table_design_system_is_imported_once_and_keeps_legacy_hooks(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));
        $dataTableCss = file_get_contents(resource_path('css/design-system/data-table.css'));
        $panel = file_get_contents(resource_path('views/components/ui/data-table/panel.blade.php'));
        $toolbar = file_get_contents(resource_path('views/components/ui/data-table/toolbar.blade.php'));
        $viewport = file_get_contents(resource_path('views/components/ui/data-table/viewport.blade.php'));
        $table = file_get_contents(resource_path('views/components/ui/data-table/table.blade.php'));
        $footer = file_get_contents(resource_path('views/components/ui/data-table/footer.blade.php'));

        $this->assertSame(1, substr_count($css, "@import './design-system/data-table.css';"));
        $this->assertSame(1, substr_count($css, "@import './design-system/form-help.css';"));
        $this->assertStringContainsString("'app-table-card'", $panel);
        $this->assertStringContainsString("'app-table-toolbar'", $toolbar);
        $this->assertStringContainsString("'app-table-shell'", $viewport);
        $this->assertStringContainsString("'app-data-table'", $table);
        $this->assertStringContainsString("'filteredTotal' => null", $footer);
        $this->assertStringContainsString("'overallTotal' => null", $footer);
        $this->assertStringContainsString('(de {{ $overallCount }} totales)', $footer);
        $this->assertStringContainsString('--data-table-row-min-height: 4.25rem;', $dataTableCss);
        $this->assertStringContainsString('--data-table-actions-width: 4rem;', $dataTableCss);
        $this->assertStringContainsString('--data-table-selected: #fdf2f5;', $dataTableCss);
        $this->assertStringContainsString('--data-table-divider: var(--table-line, #d9e2ec);', $dataTableCss);
        $this->assertStringContainsString('.ui-data-table__bulk-actions:not(.hidden)', $dataTableCss);
        $this->assertStringContainsString('border-top: 0 !important;', $dataTableCss);
        $this->assertStringContainsString('border-bottom: 0 !important;', $dataTableCss);
        $this->assertStringContainsString('.ui-data-table__filter-chips:not(:empty)', $dataTableCss);
        $this->assertMatchesRegularExpression(
            '/\.ui-data-table__filter-chips:not\(:empty\)\s*\{\s*border-bottom: 1px solid #e5e7eb !important;/',
            $dataTableCss
        );
        $this->assertStringContainsString(':has(input[type="checkbox"]:checked)', $dataTableCss);
        $this->assertStringContainsString('height: var(--data-table-row-min-height);', $dataTableCss);
        $this->assertStringContainsString('.ui-data-table__row-actions, .users-row-actions', $dataTableCss);
        $this->assertStringContainsString('justify-content: center;', $dataTableCss);
        $this->assertStringNotContainsString('#users-table .users-row-actions', $css);
        $this->assertStringNotContainsString('width: 2.75rem !important;', $css);
        $this->assertStringContainsString('scrollbar-gutter: auto;', $dataTableCss);
        $this->assertStringNotContainsString('scrollbar-gutter: stable;', $dataTableCss);
    }

    public function test_user_page_size_uses_the_shared_keyboard_accessible_behavior(): void
    {
        $app = file_get_contents(resource_path('js/app.js'));
        $behavior = file_get_contents(resource_path('js/data-table.js'));
        $view = file_get_contents(resource_path('views/usuarios/gestion-de-usuarios.blade.php'));

        $this->assertStringContainsString("from './data-table'", $app);
        $this->assertStringContainsString('initPageSize: initDataTablePageSize', $app);
        $this->assertStringContainsString('initRowActions: initDataTableRowActions', $app);
        $this->assertStringContainsString("app:data-table-ready", $app);
        $this->assertStringContainsString("event.key === 'Escape'", $behavior);
        $this->assertStringContainsString("'ArrowDown', 'ArrowUp', 'Home', 'End'", $behavior);
        $this->assertStringContainsString("footerSelector = '.ui-data-table__footer'", $behavior);
        $this->assertStringContainsString('initializeUsersRowActions', $view);
        $this->assertStringContainsString('initializeUsersPageSize', $view);
    }

    public function test_deaths_table_uses_the_shared_structure_and_controls(): void
    {
        $view = file_get_contents(resource_path('views/estadisticas/datos.blade.php'));
        $filters = file_get_contents(resource_path('views/components/filtros/defunciones.blade.php'));
        $styles = file_get_contents(resource_path('css/pages/statistics-data.css'));

        $this->assertStringContainsString('<x-ui.data-table.panel', $view);
        $this->assertStringContainsString('<x-ui.data-table.toolbar>', $view);
        $this->assertStringContainsString('<x-ui.data-table.bulk-actions', $view);
        $this->assertStringContainsString('<x-ui.data-table.viewport>', $view);
        $this->assertStringContainsString('<x-ui.data-table.table id="deaths-table">', $view);
        $this->assertStringContainsString('class="dt-actions-cell px-3 py-2', $view);
        $this->assertStringContainsString('<x-ui.data-table.footer', $view);
        $this->assertStringContainsString(
            "window.AppDataTable?.focusSearch(document.getElementById('dt-search-deaths'));",
            $view
        );
        $this->assertStringContainsString('aria-label="Municipio de residencia" title="Municipio de residencia">Residencia</th>', $view);
        $this->assertStringContainsString('aria-label="Municipio de defunción" title="Municipio de defunción">Defunción</th>', $view);
        $this->assertStringContainsString('<x-ui.data-table.search', $filters);
        $this->assertStringContainsString('<x-ui.data-table.page-size', $filters);
        $this->assertStringContainsString('class="ui-data-table__filter-chips users-filter-chips"', $filters);
        $this->assertStringContainsString('initializeDeathsPageSize', $filters);
        $this->assertSame(2, substr_count($filters, 'class="statistics-location-group"'));
        $this->assertStringContainsString('statistics-location-residence-title', $filters);
        $this->assertStringContainsString('statistics-location-death-title', $filters);
        $this->assertStringContainsString('$residenceDistricts', $filters);
        $this->assertStringContainsString('$residenceMunicipalities', $filters);
        $this->assertStringContainsString("=== 'OTRO'", $filters);
        $this->assertStringContainsString('id="statisticsYearFormatHelp"', $filters);
        $this->assertStringContainsString('aria-describedby="statistics-filter-error"', $filters);
        $this->assertStringNotContainsString('statisticsDateRangeHelp', $filters);
        $this->assertStringContainsString('id="statisticsAgeFormatHelp"', $filters);
        $this->assertStringContainsString('placeholder="Ej. 25 o 20-30"', $filters);
        $this->assertStringContainsString('Si omite el año, se considerarán todos los años.', $filters);
        $this->assertStringNotContainsString('statisticsDateModeHelp', $filters);
        $this->assertStringNotContainsString('Sin año, este periodo no se aplicará.', $filters);
        $this->assertStringContainsString('.statistics-location-group + .statistics-location-group', $styles);
        $this->assertStringContainsString('renderDeathLocation(', $view);
        $this->assertStringContainsString('statistics-location__classification', $view);
        $this->assertStringContainsString('.statistics-location .statistics-location__classification', $styles);
        $this->assertStringContainsString('renderDeathCauseDetail(', $view);
        $this->assertStringContainsString("dataRow.child(renderDeathCauseDetail", $view);
        $this->assertStringContainsString('statistics-cause-toggle', $view);
        $this->assertStringContainsString('.statistics-cause-detail', $styles);
        $this->assertStringContainsString('initializeDeathRowActions', $view);
        $this->assertStringContainsString(".toggleClass('is-selected', isSelected)", $view);
        $this->assertStringNotContainsString('function positionDeathActionMenu', $view);
        $this->assertStringNotContainsString('.statistics-table-card > .app-table-toolbar .users-filter-search:focus-within', $styles);
        $this->assertStringNotContainsString('.statistics-table-card > .app-table-toolbar .users-page-size-button[aria-expanded="true"]', $styles);
        $this->assertStringNotContainsString('#deaths-table.dataTable tbody tr { height:', $styles);
    }

    public function test_user_and_death_filters_share_the_same_panel_behavior(): void
    {
        $app = file_get_contents(resource_path('js/app.js'));
        $filterSelect = file_get_contents(resource_path('js/filter-select.js'));
        $styles = file_get_contents(resource_path('css/app.css'));
        $filterSelectStyles = file_get_contents(resource_path('css/design-system/filter-select.css'));
        $users = file_get_contents(resource_path('views/usuarios/gestion-de-usuarios.blade.php'));
        $userFilters = file_get_contents(resource_path('views/components/filtros/usuarios.blade.php'));
        $deaths = file_get_contents(resource_path('views/components/filtros/defunciones.blade.php'));
        $deathTable = file_get_contents(resource_path('views/estadisticas/datos.blade.php'));

        $this->assertSame(1, substr_count($app, 'window.AppFilterPanel = {'));
        $this->assertSame(1, substr_count($app, 'window.setFilterSectionExpanded ='));
        $this->assertStringContainsString('highlightTarget(target)', $app);
        $this->assertStringContainsString("body.scrollTo({", $app);
        $this->assertStringContainsString("'app-filter-section-reveal'", $app);
        $this->assertStringContainsString("prefers-reduced-motion: reduce", $app);
        $this->assertStringContainsString('placeCaretAtEnd(control)', $app);
        $this->assertStringContainsString('emptyOptionLabel', $filterSelect);
        $this->assertStringContainsString("String(data.value ?? '') === ''", $filterSelect);
        $this->assertStringContainsString('Canonical filter-panel select contract', $filterSelectStyles);
        $this->assertStringContainsString('.app-standard-filter-panel .ts-wrapper.app-filter-select .ts-control', $filterSelectStyles);
        $this->assertStringContainsString('position: static !important', $filterSelectStyles);
        $this->assertStringContainsString('--ui-shadow-dropdown:', $styles);
        $this->assertStringContainsString('var(--ui-shadow-dropdown', $filterSelectStyles);
        $this->assertMatchesRegularExpression('/id="quarter"[^>]*:searchable="false"/', $deaths);
        $this->assertStringContainsString('clip-path: inset(50%)', $filterSelectStyles);
        $this->assertStringContainsString('data-filter-date-proxy="selectedMonths[]"', $deaths);
        $this->assertStringContainsString('date: dateFocusTarget', $deaths);
        $this->assertStringContainsString("quarter: 'quarter'", $deaths);
        $this->assertStringContainsString("wrapper.closest('.app-standard-filter-panel .users-filter-panel-body')", $filterSelect);
        $this->assertStringContainsString('has-open-filter-select', $filterSelect);
        $this->assertStringContainsString('.users-filter-panel-body.has-open-filter-select', $styles);
        $this->assertStringContainsString('.app-standard-filter-panel.users-filter-panel.has-open-filter-select', $styles);
        $this->assertStringContainsString(':has(.ts-wrapper.app-filter-select.dropdown-active)', $styles);
        $this->assertStringContainsString(':not([role="combobox"])', $styles);
        $this->assertStringContainsString('overscroll-behavior: contain', $styles);
        $this->assertStringContainsString('Shared mobile table contract', $styles);
        $this->assertStringContainsString('.ui-data-table__search > input[type="search"]', $styles);
        $this->assertStringContainsString('overflow-x: clip', $styles);
        $this->assertStringContainsString('.users-row-menu.is-viewport-positioned', $styles);
        $this->assertStringContainsString("compactMobilePagination = window.matchMedia('(max-width: 639px)').matches", $users);
        $this->assertStringContainsString("compactMobilePagination = window.matchMedia('(max-width: 639px)').matches", $deathTable);
        $this->assertStringContainsString('Mobile pagination keeps the standard full-width control', $styles);
        $this->assertStringContainsString('Action-heavy page headers need a second row', $styles);
        $this->assertStringContainsString('.app-page-header:has(.app-page-header-actions > :nth-child(2))', $styles);
        $this->assertStringContainsString('.users-filter-option--all', $styles);
        $this->assertSame(5, substr_count($userFilters, 'users-filter-option--all'));
        $this->assertSame(1, substr_count($deaths, 'users-filter-option--all'));
        $this->assertStringContainsString('AppFilterPanel?.clear', $users);
        $this->assertStringContainsString('AppFilterPanel?.clear', $deaths);
        $this->assertStringContainsString('AppFilterPanel?.openSection', $users);
        $this->assertStringContainsString('AppFilterPanel?.openSection', $deaths);
        $this->assertStringContainsString('AppFilterPanel?.capture', $users);
        $this->assertStringContainsString('AppFilterPanel?.capture', $deaths);
        $this->assertStringContainsString('AppFilterPanel?.restore', $users);
        $this->assertStringContainsString('AppFilterPanel?.restore', $deaths);
        $this->assertMatchesRegularExpression(
            "/#clearUsersFilters'[\\s\\S]*?table\\.clearPipeline\\(\\)\\.ajax\\.reload\\(\\);[\\s\\S]*?setUsersFilterPanelOpen\\(false\\);/",
            $users
        );
        $this->assertMatchesRegularExpression(
            "/getElementById\\('limpiarFiltros'\\)[\\s\\S]*?form\\.dispatchEvent\\(new Event\\('submit'[\\s\\S]*?setPanel\\(false\\);/",
            $deaths
        );
    }

    public function test_failed_imports_and_reports_share_the_listing_contract_without_replacing_their_content(): void
    {
        $failedImports = file_get_contents(resource_path('views/estadisticas/failed-imports.blade.php'));
        $reports = file_get_contents(resource_path('views/reportes/publicaciones.blade.php'));
        $serverPagination = file_get_contents(resource_path('views/vendor/pagination/data-table.blade.php'));
        $pageSize = file_get_contents(resource_path('views/components/ui/data-table/page-size.blade.php'));
        $search = file_get_contents(resource_path('views/components/ui/data-table/search.blade.php'));
        $behavior = file_get_contents(resource_path('js/data-table.js'));

        foreach ([$failedImports, $reports] as $view) {
            $this->assertStringContainsString('<x-ui.data-table.panel', $view);
            $this->assertStringContainsString('<x-ui.data-table.toolbar', $view);
            $this->assertStringContainsString('<x-ui.data-table.search', $view);
            $this->assertStringContainsString('<x-ui.data-table.page-size', $view);
            $this->assertStringContainsString('users-table-state', $view);
        }

        $this->assertStringContainsString('<x-ui.data-table.footer', $failedImports);
        $this->assertStringContainsString("compactMobilePagination = window.matchMedia('(max-width: 639px)').matches", $failedImports);
        $this->assertStringContainsString('beforeChange: () => confirmDiscardFailedImportChanges', $failedImports);
        $this->assertStringContainsString('<template id="record-template">', $failedImports);
        $this->assertStringContainsString('class="correction-form ui-form-fields"', $failedImports);
        $this->assertStringContainsString('failed-correction-section', $failedImports);

        $this->assertStringContainsString('<x-ui.data-table.footer', $reports);
        $this->assertStringContainsString('class="reports-table-footer"', $reports);
        $this->assertStringContainsString(':filtered-total="$publications->total()"', $reports);
        $this->assertStringContainsString(':overall-total="$totalPublications"', $reports);
        $this->assertStringContainsString("links('vendor.pagination.data-table')", $reports);
        $this->assertStringContainsString('pagination-id="dt-pagination"', $reports);
        $this->assertStringContainsString('class="fb-pagination"', $serverPagination);
        $this->assertStringContainsString('fb-page-btn fb-page-num fb-page-active', $serverPagination);
        $this->assertStringContainsString("document.querySelector('#dt-pagination .fb-pagination')", $reports);
        $this->assertStringContainsString("item.classList.toggle('fb-page-mobile-hidden'", $reports);
        $this->assertStringContainsString('function restoreReportSearchFocus()', $reports);
        $this->assertStringContainsString('if (submittedFromSearch && replaced) restoreReportSearchFocus();', $reports);
        $this->assertStringContainsString('window.AppDataTable?.focusSearch(search);', $reports);
        $this->assertStringContainsString("document.addEventListener('input', function(e)", $reports);
        $this->assertStringContainsString("clearButton?.classList.toggle('hidden', search.value.trim().length === 0);", $reports);
        $this->assertStringContainsString('const requestController = new AbortController();', $reports);
        $this->assertStringContainsString('signal: requestController.signal', $reports);
        $this->assertStringContainsString('if (reportesPanelAbortController !== requestController) return false;', $reports);
        $this->assertStringContainsString('if (reportesPanelAbortController !== requestController) return;', $reports);
        $this->assertMatchesRegularExpression(
            '/reportesPanelAbortController\.abort\(\);[\s\S]*?reportesPanelCache\.has\(targetUrl\)/',
            $reports
        );
        $this->assertStringNotContainsString('reports-page-item', $reports);
        $this->assertStringContainsString('ui-data-table__filter-chips users-filter-chips', $reports);
        $this->assertStringContainsString('ui-data-table__bulk-actions reports-bulk-toolbar', $reports);
        $this->assertStringContainsString('class="reports-toolbar-selection"', $reports);
        $this->assertStringContainsString('id="select-all-reports"', $reports);
        $this->assertStringNotContainsString('id="reports-visible-count"', $reports);
        $this->assertStringContainsString("toolbar.classList.toggle('hidden', !hasSelection);", $reports);
        $this->assertStringContainsString('id="reports-bulk-actions" class="reports-bulk-actions"', $reports);
        $this->assertStringContainsString('selectAll.indeterminate = hasSelection && !allSelected;', $reports);
        $this->assertStringContainsString("getReportesPanel().querySelectorAll('.publication-check-btn')", $reports);
        $this->assertStringContainsString('toggleBulkToolbar({ announce: true });', $reports);
        $this->assertMatchesRegularExpression(
            '/<x-ui\.data-table\.toolbar>\s*<form id="reports-filter-form"[^>]*class="users-filter-form">[\s\S]*?<div class="users-filter-topbar">/',
            $reports
        );
        $this->assertMatchesRegularExpression(
            '/<form id="reports-filter-form"[\s\S]*?<div class="ui-data-table__filter-chips users-filter-chips"[\s\S]*?<\/form>\s*<\/x-ui\.data-table\.toolbar>/',
            $reports
        );
        $this->assertStringContainsString('users-filter-option--all', $reports);
        $this->assertStringNotContainsString('class="reports-search"', $reports);
        $this->assertStringNotContainsString('class="reports-page-size"', $reports);
        $this->assertStringNotContainsString('class="reports-filter-chip"', $reports);
        $this->assertStringContainsString('class="ui-data-table__filter-chips users-filter-chips" aria-label="Filtros activos" aria-live="polite"', $reports);
        $this->assertStringContainsString('class="users-filter-chip-label" data-open-filter-section="status"', $reports);
        $this->assertStringContainsString('class="users-filter-chip-remove" data-clear-report-filter-url=', $reports);
        $this->assertStringContainsString('data-filter-section="district"', $reports);
        $this->assertStringContainsString('data-filter-section="order"', $reports);
        $this->assertStringContainsString('data-reports-filter-section-toggle data-filter-section-toggle', $reports);
        $this->assertStringContainsString('window.setFilterSectionExpanded(section, willOpen);', $reports);
        $this->assertStringContainsString('window.AppFilterPanel?.openSection({', $reports);
        $this->assertStringContainsString("filterChipRemove.dataset.clearReportFilterUrl", $reports);
        $this->assertStringContainsString("menu.classList.toggle('opens-upward', openAbove);", $reports);
        $this->assertStringContainsString("['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(e.key)", $reports);
        $this->assertStringContainsString('<x-filtros.select id="district_id" name="district_id" placeholder="Todos">', $reports);
        $this->assertStringContainsString("document.getElementById('reports-filter-panel')", $reports);
        $this->assertStringNotContainsString("document.querySelector('#reports-filter-popover')", $reports);
        $this->assertStringContainsString('window.AppFilterSelect.initAll(panel', $reports);
        $this->assertStringNotContainsString('reports-district-tomselect', $reports);
        $this->assertStringNotContainsString("dropdownParent: 'body'", $reports);
        $reportsCss = file_get_contents(resource_path('css/pages/report-publications.css'));
        $this->assertStringNotContainsString('reports-ts-dropdown', $reportsCss);
        $this->assertStringContainsString('.reports-row-menu::before {', $reportsCss);
        $this->assertStringContainsString('.reports-row-menu.opens-upward::before {', $reportsCss);
        $this->assertStringContainsString('.reports-select-page-control {', $reportsCss);
        $this->assertStringContainsString('.reports-select-page-control input:focus,', $reportsCss);
        $this->assertStringContainsString('--tw-ring-color: transparent;', $reportsCss);
        $this->assertStringContainsString('border-left-width: 3px;', $reportsCss);
        $this->assertStringContainsString('border-top-color: var(--reports-category, #526278);', $reportsCss);
        $this->assertStringContainsString('background: #eef2f6;', $reportsCss);
        $this->assertStringContainsString('.reports-toolbar-selection {', $reportsCss);
        $this->assertStringContainsString('.reports-toolbar-selection::before {', $reportsCss);
        $this->assertMatchesRegularExpression(
            '/@media \(max-width: 639px\)[\s\S]*?\.reports-toolbar-selection\s*\{[^}]*grid-column:\s*1 \/ -1\s*!important;[^}]*grid-row:\s*3\s*!important;[^}]*justify-self:\s*start;[^}]*margin-left:\s*0;/s',
            $reportsCss
        );
        $this->assertMatchesRegularExpression(
            '/@media \(max-width: 639px\)[\s\S]*?\.reports-publications-page \.reports-table-card > \.app-table-toolbar[\s\S]*?\.app-standard-filter-panel\.users-filter-panel\s*\{[^}]*grid-row:\s*4\s*!important;/s',
            $reportsCss
        );
        $this->assertStringContainsString('.reports-bulk-actions.hidden {', $reportsCss);
        $this->assertStringContainsString('.reports-publications-page .reports-table-card > .reports-type-tabs {', $reportsCss);
        $this->assertStringContainsString('padding: .65rem .75rem 0 !important;', $reportsCss);
        $this->assertStringContainsString('padding: .65rem .5rem 0 !important;', $reportsCss);
        $this->assertStringContainsString('background-size: 220% 100%;', $reportsCss);
        $this->assertStringContainsString('animation: reports-progress 1.15s linear infinite;', $reportsCss);
        $this->assertStringContainsString('to { background-position: -120% 0; }', $reportsCss);
        $this->assertStringNotContainsString('.reports-table-progress::after', $reportsCss);
        $this->assertStringContainsString('updateReportsCompactPagination', $reports);
        $this->assertStringContainsString('AppFilterPanel?.capture', $reports);
        $this->assertStringContainsString('AppFilterPanel?.restore', $reports);
        $this->assertStringContainsString('AppDataTable?.initPageSize', $reports);
        $this->assertStringContainsString("window.addEventListener('app:data-table-ready'", $reports);
        $this->assertStringContainsString("e.target.closest('[data-reports-filter-target]')", $reports);
        $this->assertStringContainsString('<x-publicacion-card', $reports);
        $this->assertStringContainsString("@include('components.modal-alcoholimetria')", $reports);
        $this->assertStringContainsString("@include('components.modal-seguridad-vial')", $reports);

        $this->assertStringContainsString("'name' => null", $pageSize);
        $this->assertStringContainsString("'buttonClass' => ''", $pageSize);
        $this->assertStringContainsString("'name' => null", $search);
        $this->assertStringContainsString("'clearVisible' => false", $search);
        $this->assertStringContainsString('beforeChange,', $behavior);
        $this->assertStringContainsString('if (mayChange === false) return;', $behavior);
        $this->assertStringContainsString('export function initDataTableSearch({', $behavior);
        $this->assertStringContainsString('export function focusDataTableSearch(input)', $behavior);
        $this->assertStringContainsString('focusSearch: focusDataTableSearch', file_get_contents(resource_path('js/app.js')));
        $this->assertStringContainsString('initSearch: initDataTableSearch', file_get_contents(resource_path('js/app.js')));
        $this->assertStringContainsString('window.AppDataTable?.initSearch({', $failedImports);
        $this->assertStringContainsString('beforeSearch: nextSearch => restoringFailedImportsHistory', $failedImports);
        $this->assertStringContainsString("url.searchParams.get('page')", $failedImports);
        $this->assertStringContainsString("url.searchParams.get('per_page')", $failedImports);
        $this->assertStringContainsString("url.searchParams.get('search')", $failedImports);
        $this->assertStringContainsString('window.history.pushState({ failedImportsView: true }', $failedImports);
        $this->assertStringContainsString('window.history.replaceState({ failedImportsView: true }', $failedImports);
        $this->assertStringContainsString("window.addEventListener('popstate', async () =>", $failedImports);
        $this->assertStringContainsString("loadFailedRecords(currentPage, { history: 'replace' });", $failedImports);
        $this->assertStringContainsString('setValue(nextValue, { focus = false } = {})', $behavior);
        $this->assertStringContainsString('let failedRecordsAbortController = null;', $failedImports);
        $this->assertStringContainsString('let failedRecordGapAbortController = null;', $failedImports);
        $this->assertStringContainsString('const requestSequence = ++failedRecordsRequestSequence;', $failedImports);
        $this->assertStringContainsString('failedRecordsAbortController?.abort();', $failedImports);
        $this->assertStringContainsString('signal: requestController.signal,', $failedImports);
        $this->assertStringContainsString('failedRecordsRequestSequence !== requestSequence', $failedImports);
        $this->assertStringContainsString('recordsList !== document.getElementById(\'records-list\')', $failedImports);
        $this->assertStringNotContainsString("failedSearchInput?.addEventListener('keydown'", $failedImports);
    }
}
