<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script>
$(document).ready(function() {
    $('table.dataTable, #tablaContratos, .table').not('.no-dt').DataTable({
        pageLength: 5,
        lengthMenu: [[5,20,35,50,100],[5,20,35,50,100]],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.2/i18n/es-MX.json' }
    });
});
</script>

<!-- Sidebar submenu toggle -->
<script>
function togglePropiedades() {
    var s = document.getElementById('submenuPropiedades');
    var a = document.getElementById('arrowProp');
    if (s) s.classList.toggle('show');
    if (a) a.classList.toggle('rotate');
}
</script>

</body>
</html>
