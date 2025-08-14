document.addEventListener('DOMContentLoaded', () => {
  const themeIcon = document.getElementById('theme-icon');

  // Cek preferensi tersimpan
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    themeIcon.classList.remove('fa-moon');
    themeIcon.classList.add('fa-sun');
  }

  document.getElementById('toggle-theme').addEventListener('click', (e) => {
    e.preventDefault();
    document.body.classList.toggle('dark-mode');

    const isDark = document.body.classList.contains('dark-mode');
    themeIcon.classList.toggle('fa-moon', !isDark);
    themeIcon.classList.toggle('fa-sun', isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });
});
