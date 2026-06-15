<?php
// includes/footer.php
?>
            </main>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const arrowIcon = document.getElementById('icon-arrow');
            
            if (window.innerWidth >= 1024) { // lg screens (desktop)
                if (sidebar.style.marginLeft === '-20rem') {
                    sidebar.style.marginLeft = '0';
                    localStorage.setItem('sidebarPinned', 'true');
                    if (arrowIcon) arrowIcon.classList.remove('rotate-180');
                } else {
                    sidebar.style.marginLeft = '-20rem';
                    localStorage.setItem('sidebarPinned', 'false');
                    if (arrowIcon) arrowIcon.classList.add('rotate-180');
                }
            } else { // mobile screens
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                    // Prevent body scroll
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                    // Restore body scroll
                    document.body.style.overflow = '';
                }
            }
        }
    </script>
</body>
</html>
