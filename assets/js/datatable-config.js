// Configuración común para DataTables
const dataTableConfig = {
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
    },
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
    order: [[0, 'desc']]
};

function initDataTable(selector, config = {}) {
    return $(selector).DataTable({
        ...dataTableConfig,
        ...config
    });
}
