<?php

$flash = $flash ?? null;
?>
    </main>

    <footer class="page-footer footer-lite">
        <div class="container footer-inner">
            <span>Controle Acadêmico • CRUD em PHP (POO) + MySQL</span>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selects = document.querySelectorAll('select');
            M.FormSelect.init(selects);

            const sidenavs = document.querySelectorAll('.sidenav');
            M.Sidenav.init(sidenavs);

            const flash = <?= $flash ? json_encode($flash) : 'null' ?>;
            if (flash) {
                const map = {
                    success: 'green darken-1',
                    error: 'red darken-1',
                    warning: 'amber darken-2'
                };
                M.toast({
                    html: flash.message,
                    classes: map[flash.type] || map.success
                });
            }
        });
    </script>
</body>

</html>

