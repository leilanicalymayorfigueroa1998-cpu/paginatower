        </div> <!-- container-fluid -->
        </div> <!-- content -->

        <!-- Bootstrap Bundle (incluye Popper) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- DataTables Config -->
        <script>
            $(document).ready(function() {
                $('table').DataTable({
                    pageLength: 5,
                    lengthMenu: [
                        [5, 20, 35, 50,100],
                        [5, 20, 35, 50,100]
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.2/i18n/es-MX.json'
                    }
                });
            });
        </script>

        <!-- Toggle Submenu -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const toggle = document.querySelector(".menu-toggle");
                const submenu = document.getElementById("submenuPropiedades");

                if (toggle && submenu) {
                    toggle.addEventListener("click", function(e) {
                        e.preventDefault();
                        submenu.classList.toggle("show");
                    });
                }

            });
        </script>
        
        </body>

        </html>