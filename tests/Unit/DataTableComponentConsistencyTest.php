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
    }

    public function test_user_table_controls_use_the_shared_search_and_page_size_components(): void
    {
        $filters = file_get_contents(resource_path('views/components/filtros/usuarios.blade.php'));

        $this->assertStringContainsString('<x-ui.data-table.search', $filters);
        $this->assertStringContainsString('id="dt-search-users"', $filters);
        $this->assertStringContainsString('<x-ui.data-table.page-size', $filters);
        $this->assertStringContainsString('id="dt-per-page"', $filters);
        $this->assertStringContainsString('class="ui-data-table__filter-chips users-filter-chips"', $filters);
    }

    public function test_data_table_design_system_is_imported_once_and_keeps_legacy_hooks(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));
        $dataTableCss = file_get_contents(resource_path('css/design-system/data-table.css'));
        $panel = file_get_contents(resource_path('views/components/ui/data-table/panel.blade.php'));
        $toolbar = file_get_contents(resource_path('views/components/ui/data-table/toolbar.blade.php'));
        $viewport = file_get_contents(resource_path('views/components/ui/data-table/viewport.blade.php'));
        $table = file_get_contents(resource_path('views/components/ui/data-table/table.blade.php'));

        $this->assertSame(1, substr_count($css, "@import './design-system/data-table.css';"));
        $this->assertSame(1, substr_count($css, "@import './design-system/form-help.css';"));
        $this->assertStringContainsString("'app-table-card'", $panel);
        $this->assertStringContainsString("'app-table-toolbar'", $toolbar);
        $this->assertStringContainsString("'app-table-shell'", $viewport);
        $this->assertStringContainsString("'app-data-table'", $table);
        $this->assertStringContainsString('--data-table-row-min-height: 4.25rem;', $dataTableCss);
        $this->assertStringContainsString('--data-table-actions-width: 4rem;', $dataTableCss);
        $this->assertStringContainsString('--data-table-selected: #fdf2f5;', $dataTableCss);
        $this->assertStringContainsString('--data-table-divider: var(--table-line, #d9e2ec);', $dataTableCss);
        $this->assertStringContainsString('.ui-data-table__bulk-actions:not(.hidden)', $dataTableCss);
        $this->assertStringContainsString('border-top: 0 !important;', $dataTableCss);
        $this->assertStringContainsString('border-bottom: 0 !important;', $dataTableCss);
        $this->assertStringContainsString('.ui-data-table__filter-chips:not(:empty)', $dataTableCss);
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
        $this->assertStringContainsString('aria-label="Municipio de residencia" title="Municipio de residencia">Residencia</th>', $view);
        $this->assertStringContainsString('aria-label="Municipio de defunción" title="Municipio de defunción">Defunción</th>', $view);
        $this->assertStringContainsString('<x-ui.data-table.search', $filters);
        $this->assertStringContainsString('<x-ui.data-table.page-size', $filters);
        $this->assertStringContainsString('class="ui-data-table__filter-chips users-filter-chips"', $filters);
        $this->assertStringContainsString('initializeDeathsPageSize', $filters);
        $this->assertSame(2, substr_count($filters, 'class="statistics-location-group"'));
        $this->assertStringContainsString('statistics-location-residence-title', $filters);
        $this->assertStringContainsString('statistics-location-death-title', $filters);
        $this->assertStringContainsString('.statistics-location-group + .statistics-location-group', $styles);
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
        $this->assertStringContainsString("wrapper.closest('.app-standard-filter-panel .users-filter-panel-body')", $filterSelect);
        $this->assertStringContainsString('has-open-filter-select', $filterSelect);
        $this->assertStringContainsString('.users-filter-panel-body.has-open-filter-select', $styles);
        $this->assertStringContainsString('.app-standard-filter-panel.users-filter-panel.has-open-filter-select', $styles);
        $this->assertStringContainsString(':has(.ts-wrapper.app-filter-select.dropdown-active)', $styles);
        $this->assertStringContainsString(':not([role="combobox"])', $styles);
        $this->assertStringContainsString('overscroll-behavior: contain', $styles);
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
    }
}
