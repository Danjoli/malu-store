<script>
    (() => {
        const savedTheme = localStorage.getItem('malu-store-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = savedTheme ? savedTheme === 'dark' : prefersDark;
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.dataset.theme = dark ? 'dark' : 'light';

        window.storeTheme = {
            set(nextDark) {
                document.documentElement.classList.toggle('dark', nextDark);
                document.documentElement.dataset.theme = nextDark ? 'dark' : 'light';
                localStorage.setItem('malu-store-theme', nextDark ? 'dark' : 'light');
                document.querySelector('meta[name="theme-color"]')?.setAttribute('content', nextDark ? '#171315' : '#9f4055');
            },
        };
    })();
</script>
